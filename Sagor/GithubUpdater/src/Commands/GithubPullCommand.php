<?php

namespace Sagor\GithubUpdater\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GithubPullCommand extends Command
{
    protected $signature = 'github:pull';
    protected $description = 'Pull the latest changes from GitHub and run artisan commands';

    public function handle()
    {
        $this->info("Pulling latest changes from GitHub...");

        $commands = [
            "git pull",
            "php artisan migrate --force",
        ];

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(0);
            try {
                $process->mustRun();
                $this->info($process->getOutput());
            } catch (ProcessFailedException $exception) {
                $this->error("Error: " . $exception->getMessage());
            }
        }

        $this->info("GitHub pull completed.");
    }
}
