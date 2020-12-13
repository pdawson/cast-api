<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Generator;
use App\Services\Run;
use Illuminate\Console\Command;
use Spatie\Ssh\Ssh;

abstract class ServerCommand extends Command
{
    /**
     * The command runner instance
     *
     * @var Run
     */
    protected Run $runner;

    /**
     * The config generator instance
     *
     * @var Generator
     */
    protected Generator $generator;

    public function __construct(Run $runner, Generator $generator)
    {
        parent::__construct();

        $this->runner = $runner;
        $this->generator = $generator;
    }

    /**
     * Tests the newly uploaded configuration to ensure the syntax is valid
     *
     * @param Ssh $terminal
     * @return bool
     */
    protected function testNginxConfiguration(Ssh $terminal): bool
    {
        $command = $terminal->execute("sudo nginx -t");

        if (!$command->isSuccessful()) {
            return false;
        }

        // Get all output together - "nginx -t" result is in error output
        $output = $command->getOutput() . $command->getErrorOutput();

        return str_contains($output, 'syntax is ok')
            && str_contains($output, 'test is successful');
    }

    /**
     * Restarts the nginx service on the server
     *
     * @param Ssh $terminal
     * @param string|null $action
     * @return bool
     */
    protected function restartNginxService(Ssh $terminal, ?string $action = null): bool
    {
        $arg = $action ?? 'restart';
        $command = $terminal->execute("sudo /bin/systemctl {$action} nginx.service");

        return $command->isSuccessful();
    }
}
