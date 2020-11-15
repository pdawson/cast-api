<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Preset;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class PresetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->createPreset('Nuxt', 'nuxt');
        $this->createSetting('site-proxy-url', 'Nuxt Proxy URL', 'http://127.0.0.1:3000', 'The url (normally localhost) and port that the nuxt server is running on');
    }

    /**
     * Creates or updates a preset record
     *
     * @param string $name
     * @param string|null $template
     * @return mixed
     */
    protected function createPreset(string $name, ?string $template = null)
    {
        return Preset::updateOrCreate(['name' => $name], ['template' => $template]);
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
