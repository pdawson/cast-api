<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Models\Site;
use Illuminate\Support\Collection;
use Spatie\Ssh\Ssh;

class SiteSync extends ServerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs all active sites to all servers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $servers = $this->getServers();

        foreach ($servers as $server) {
            // We get all sites here because even if its inactive, we store the config in sites-available
            $sites = $server->sites;

            // Connect the server via SSH
            $terminal = $this->runner->server($server)->create();

            $this->output->title(
                sprintf('Syncing sites for %s [%s]', $server->name, $server->hostname)
            );

            // Disable all sites on the server first; this way we can be sure all sites are valid in sequence
            foreach ($sites as $site) {
                $this->output->text(
                    sprintf('Disabling site %s [%s]', $server->name, $server->hostname)
                );

                $this->disableSite($terminal, $site);
            }

            foreach ($sites as $site) {
                $contents = $this->generateSiteConfig($site);

                $this->uploadNewConfiguration($terminal, $site, $contents);

                if ($site->active) {
                    $this->enableSite($terminal, $site);

                    $ssl = null;
                    if ($site->letsencrypt_active && !$site->letsencrypt_installed) {
                        $this->info('Generating LetsEncrypt certificate');

                        $ssl = $this->generateCertificate($terminal, $site);

                        if ($ssl) {
                            $site->letsencrypt_installed = true;
                            $site->save();
                        } else {
                            // If we failed to generate an SSL cert, disable the site as it breaks nginx config
                            $this->disableSite($terminal, $site);
                        }
                    }
                }
            }
        }

        return 0;
    }

    /**
     * Retrieves a list of servers to sync on
     *
     * @return Collection
     */
    protected function getServers(): Collection
    {
        return Server::all();
    }

    /**
     * Generates the master nginx configuration file for the site
     *
     * @param Site $site
     * @return string
     */
    protected function generateSiteConfig(Site $site): string
    {
        $config = $this->getSiteConfig($site);
        $preset = $site->preset->template ?? 'default';

        $generated = $this->generator->generate("presets/{$preset}.conf", $config);

        $this->output->success(
            sprintf('Generated server configuration for %s [%s]', $site->name, $site->domain)
        );

        return $generated;
    }

    /**
     * Uploads the new site configuration to the sites-available directory
     *
     * @param Ssh $terminal
     * @param Site $site
     * @param string $contents
     */
    protected function uploadNewConfiguration(Ssh $terminal, Site $site, string $contents): void
    {
        $this->info('Uploading site configuration');

        $path = "/etc/nginx/sites-available/{$site->domain}.conf";

        $temp = tmpfile();
        fwrite($temp, $contents);

        $command = $terminal->upload(
            stream_get_meta_data($temp)['uri'],
            $path
        );

        if (!$command->isSuccessful()) {
            $this->output->error(sprintf('Failed to replace the contents of file [%s]', $path));
        }
    }

    /**
     * Generates a site certificate with LetsEncrypt for the domain
     *
     * @param Ssh $terminal
     * @param Site $site
     * @return bool
     */
    protected function generateCertificate(Ssh $terminal, Site $site): bool
    {
        $path = "/etc/nginx/sites-available/{$site->domain}.conf";
        $config = $this->getSiteConfig($site);

        // Generate a new site configuration file
        $this->line('Removing SSL configuration directives');
        $original = $this->generateSiteConfig($site);

        // Comment SSL directives for ACME verification
        $temp = preg_replace_callback(
            '/(listen .*443)/',
            static function ($field) {
                return head($field) . ';#';
            },
            $original
        );

        $temp = preg_replace_callback(
            '/(ssl_(certificate|certificate_key|trusted_certificate) )/',
            static function ($field) {
                return '#' . head($field);
            },
            $temp
        );

        $this->uploadNewConfiguration($terminal, $site, $temp);

        if (
            !$this->testNginxConfiguration($terminal)
            || !$this->restartNginxService($terminal, 'reload')
        ) {
            $this->error('Failed to comment certificate rules; likely a broken configuration file');

            return false;
        }

        // Generate a certificate with Certbot
        $certEmail = "me@pauldawson.me";
        $leRoot = $config['lets-encrypt-root'] ?? '/var/www/_letsencrypt';

        $domainPath = "-d {$site->domain}";

        // If we have a www. subdomain, cert that too
        if ($site->subdomain_active) {
            $domainPath .= " -d www.{$site->domain}";
        }

        $command = $terminal->execute(
            "sudo certbot certonly --webroot {$domainPath} --email {$certEmail} -w {$leRoot} -n --agree-tos --force-renewal"
        );

        if (!$command->isSuccessful()) {
            $this->error('Failed to generate a certificate');

            return false;
        }

        // Replace the configuration with the SSL directives included
        $this->uploadNewConfiguration($terminal, $site, $original);

        if (
            !$this->testNginxConfiguration($terminal)
            || !$this->restartNginxService($terminal, 'reload')
        ) {
            $this->error('Failed to restart server after installing certificate');

            return false;
        }

        $this->output->success('Finished installing certificate');

        return true;
    }

    /**
     * Enables a site on the server
     *
     * @param Ssh $terminal
     * @param Site $site
     * @return bool
     */
    protected function enableSite(Ssh $terminal, Site $site): bool
    {
        $path = "/etc/nginx/sites-available/{$site->domain}.conf";

        $command = $terminal->execute(
            "ln -sf {$path} /etc/nginx/sites-enabled"
        );

        if (!$command->isSuccessful()) {
            $this->error(
                sprintf('Failed to enable site %s [%s]', $site->name, $site->domain)
            );

            return false;
        }

        return true;
    }

    /**
     * Disables a site and removes it from sites-enabled
     *
     * @param Ssh $terminal
     * @param Site $site
     * @return bool
     */
    protected function disableSite(Ssh $terminal, Site $site): bool
    {
        $path = escapeshellcmd("/etc/nginx/sites-enabled/{$site->domain}.conf");

        $command = $terminal->execute("rm {$path}");

        // We don't really care if it fails or succeeds as we try to remove the file whenever we rebuild

        return $command->isSuccessful();
    }

    /**
     * Gets configuration values for sites, merges the actual settings and the site and server models
     *
     * @param Site $site
     * @return array
     */
    protected function getSiteConfig(Site $site): array
    {
        $config = array_merge(
            // Inherited from the site model
            [
                'site-domain' => $site->domain,
                'site-name' => $site->name,
                'site-path' => $site->path,
                'site-public-path' => $site->public_path,
                'site-subdomain' => $site->subdomain_active ?? false,
            ],
            // Inherited from the server model
            [
                'server-name' => $site->server->name,
                'server-hostname' => $site->server->hostname,
                'server-path' => $site->server->path,
            ],
            // Configured values
            $site->config()->toArray()
        );

        $certPath = "{$config['lets-encrypt-dir']}/{$site->domain}";
        $config['site-crt-path'] = "$certPath/fullchain.pem";
        $config['site-key-path'] = "$certPath/privkey.pem";
        $config['site-pem-path'] = "$certPath/chain.pem";

        return $config;
    }
}
