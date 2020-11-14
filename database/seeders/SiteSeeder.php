<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Server;
use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $dev = Server::where('hostname', 'pauld.dev')->first();
        $this->createSite($dev, 'pauld.dev', 'Development', 'pauld.dev');
        $this->createSite($dev, 'test.pauld.dev', 'Testing', 'test.pauld.dev');

        $production = Server::where('hostname', 'pauldawson.me')->first();
        $this->createSite($production, 'pauldawson.me', 'Portfolio', 'pauldawson.me');
    }

    /**
     * Creates or updates a site model
     *
     * @param Server|null $server
     * @param string $domain
     * @param string $name
     * @param string|null $path
     * @param bool|null $active
     * @return Site|null
     */
    protected function createSite(
        ?Server $server,
        string $domain,
        string $name,
        ?string $path = null,
        ?bool $active = null
    ): ?Site {
        if ($server === null) {
            return null;
        }

        $active ??= true;
        $path ??= $domain;

        return Site::updateOrCreate(
            ['server_id' => $server->id, 'domain' => $domain],
            compact('name', 'path', 'active')
        );
    }
}
