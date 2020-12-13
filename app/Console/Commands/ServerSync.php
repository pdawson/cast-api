<?php

namespace App\Console\Commands;

use App\Models\Server;
use Carbon\Carbon;
use Spatie\Ssh\Ssh;

class ServerSync extends ServerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs the nginx configuration files for each server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $servers = Server::all();

        foreach ($servers as $server) {
            $this->output->title(
                sprintf('Generating configs for %s [%s]', $server->name, $server->hostname)
            );

            // Connect the server via SSH
            $terminal = $this->runner->server($server)->create();

            // Generate the configs
            $files = $this->generateServerConfigs($server);

            // Backup existing config files
            $archived = $this->backupExistingConfigs($terminal);

            if (!$archived) {
                continue;
            }

            // Upload the new ones
            $this->uploadNewConfiguration($terminal, $files);

            // Test the configuration
            $valid = $this->testNginxConfiguration($terminal);

            if (!$valid) {
                $this->output->error('Nginx config is invalid!');

                continue;
            }

            $this->restartNginxService($terminal);
        }

        return 0;
    }

    /**
     * Generates a list of nginx server .conf files
     *
     * @param Server $server
     * @return array
     */
    protected function generateServerConfigs(Server $server): array
    {
        $config = $server->config()->toArray();

        $files = [
            'nginx.conf' => $this->generator->generate('nginx.conf', $config),
            'cast/general.conf' => $this->generator->generate('general.conf', $config),
            'cast/security.conf' => $this->generator->generate('security.conf', $config),
            'cast/letsencrypt.conf' => $this->generator->generate('letsencrypt.conf', $config),
            'cast/php.conf' => $this->generator->generate('php.conf', $config)
        ];

        $this->output->success('Generated config files');
        foreach ($files as $file => $contents) {
            $this->comment($file);
        }

        return $files;
    }

    /**
     * Performs a backup of the existing configuration files
     *
     * @param Ssh $terminal
     * @return bool
     */
    protected function backupExistingConfigs(Ssh $terminal): bool
    {
        $this->output->success('Performing backup of existing configuration');

        $date = Carbon::now()->format('d-m-y-h-i-s');

        $process = $terminal->execute([
            'cd /etc/nginx',
            'mkdir -p /etc/nginx/backups',
            "tar -czvf backups/nginx_$date.tar.gz nginx.conf sites-available/ sites-enabled/ cast/"
        ]);

        if (!$process->isSuccessful()) {
            $this->output->error('Failed to backup existing configuration files');

            return false;
        }

        return true;
    }

    /**
     * Updates the server configuration files with the newly generated ones
     *
     * @param Ssh $terminal
     * @param array $configs
     */
    protected function uploadNewConfiguration(Ssh $terminal, array $configs): void
    {
        foreach ($configs as $file => $contents) {
            $this->info(sprintf('Updating file [%s]', $file));

            $path = "/etc/nginx/{$file}";

            $temp = tmpfile();
            fwrite($temp, $contents);

            $command = $terminal->upload(
                stream_get_meta_data($temp)['uri'],
                $path
            );

            if (!$command->isSuccessful()) {
                $this->output->error(sprintf('Failed to replace the contents of file [%s]', $file));
            }
        }
    }
}
