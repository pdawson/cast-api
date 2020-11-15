<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->createServerSettings();
    }

    /**
     * Creates NGINX server / global settings
     */
    protected function createServerSettings(): void
    {
        // Global
        $this->createSetting('nginx-user', 'User', 'www-data', 'The linux user to run as');
        $this->createSetting('nginx-group', 'Group', 'www-data', 'The linux group to run as');
        $this->createSetting('client-max-body-size', 'Client Max Body Size', '16');

        // Security
        $this->createSetting('ssl-protocols', 'Supported SSL Protocols', 'TLSv1.2 TLSv1.3');
        $this->createSetting('lets-encrypt-root', "Let's Encrypt Webroot", '/var/www/_letsencrypt');
        $this->createSetting('lets-encrypt-dir', "Let's Encrypt Certificate Directory", '/etc/letsencrypt/live', 'The root directory where certificates will be stored');
        $this->createSetting('server-tokens', 'Server Tokens', 'off');
        $this->createSetting('referrer-policy', 'Referrer-Policy', 'no-referrer-when-downgrade');
        $this->createSetting('content-security-policy', 'Content-Security-Policy', "default-src 'self' http: https: data: blob: 'unsafe-inline'");

        // Performance
        $this->createSetting('enable-gzip', 'Enable GZIP Compression', 'on');
        $this->createSetting('enable-brotli', 'Enable Brotli Compression', 'off');
        $this->createSetting('expiration-assets', 'Expiration for Assets', '30d');
        $this->createSetting('expiration-media', 'Expiration for Media', '30d');
        $this->createSetting('expiration-svg', 'Expiration for SVGs', '365d');
        $this->createSetting('expiration-fonts', 'Expiration for fonts', '365d');
    }

    /**
     * Creates or updates a new setting record

     * @param string $key
     * @param string $name
     * @param string $default
     * @param string|null $description
     * @return Setting
     */
    protected function createSetting(string $key, string $name, string $default, ?string $description = null): Setting
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            compact('name', 'default', 'description')
        );
    }
}
