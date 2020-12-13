<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Server;
use Spatie\Ssh\Ssh;

class Run
{
    /**
     * The server to run the command on
     *
     * @var Server
     */
    protected Server $server;

    /**
     * Sets the server to run the command on
     *
     * @param Server $server
     * @return $this
     */
    public function server(Server $server): Run
    {
        $this->server = $server;

        return $this;
    }

    public function create(): Ssh
    {
        $user = env('CAST_USER', 'cast');
        $hostname = $this->server->hostname;

        return Ssh::create($user, $hostname)
            ->usePort(22)
            ->usePrivateKey(env('CAST_PRIVATE_KEY'))
            ->onOutput(static function ($type, $line) {
                echo $line;
            });
    }
}
