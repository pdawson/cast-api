<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Server;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->createServer('Development', 'pauld.dev', '/var/www');
        //$this->createServer('Production', 'pauldawson.me', '/var/www');
    }

    /**
     * Creates or updates a new server record
     *
     * @param string $name
     * @param string $hostname
     * @param string $path
     * @return Server
     */
    protected function createServer(string $name, string $hostname, string $path): Server
    {
        return Server::updateOrCreate(
            ['hostname' => $hostname],
            [
                'name' => $name,
                'path' => $path,
            ]
        );
    }
}
