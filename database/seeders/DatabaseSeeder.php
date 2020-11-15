<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ServerSeeder::class,
            SiteSeeder::class,
            SettingSeeder::class,
            PresetSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
