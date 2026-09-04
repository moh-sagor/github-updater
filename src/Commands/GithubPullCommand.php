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

        $githubToken = config('github-updater.github_token');
        $userName = config('github-updater.github_username');
        $repoLink = config('github-updater.github_repo_link');
        $artisanCommands = config('github-updater.artisan_commands');

        $repositoryUrl = null;
        if (!empty($repoLink)) {
            $cleanRepo = preg_replace('/^https?:\/\//', '', $repoLink);
            if (!empty($userName) && !empty($githubToken)) {
                $repositoryUrl = "https://{$userName}:{$githubToken}@{$cleanRepo}";
            } else {
                $repositoryUrl = "https://{$cleanRepo}";
            }
            $gitPullCmd = "git pull {$repositoryUrl}";
        } else {
            $gitPullCmd = "git pull";
        }

        $commands = [$gitPullCmd];

        if (!empty($artisanCommands)) {
            $artisanCommandsArray = explode(',', $artisanCommands);
            foreach ($artisanCommandsArray as $command) {
                $trimmed = trim($command);
                if (!empty($trimmed)) {
                    $commands[] = $trimmed;
                }
            }
        } else {
            $commands[] = "php artisan migrate --force";
        }

        foreach ($commands as $command) {
            $process = method_exists(Process::class, 'fromShellCommandline')
                ? Process::fromShellCommandline($command)
                : new Process($command);

            $process->setTimeout(0);
            try {
                $process->mustRun();
                $output = $process->getOutput();
                if (!empty($repositoryUrl)) {
                    $output = str_replace($repositoryUrl, 'git pull', $output);
                }
                if (!empty($githubToken)) {
                    $output = str_replace($githubToken, '***', $output);
                }
                $output = preg_replace('/https?:\/\/[^:\s]+:[^@\s]+@/', 'https://***@', $output);
                $this->info($output);
            } catch (ProcessFailedException $exception) {
                $errorMessage = $exception->getMessage();
                if (!empty($repositoryUrl)) {
                    $errorMessage = str_replace($repositoryUrl, 'git pull', $errorMessage);
                }
                if (!empty($githubToken)) {
                    $errorMessage = str_replace($githubToken, '***', $errorMessage);
                }
                $errorMessage = preg_replace('/https?:\/\/[^:\s]+:[^@\s]+@/', 'https://***@', $errorMessage);
                $this->error("Error: " . $errorMessage);
            }
        }

        $this->info("GitHub pull completed.");

        return 0;
    }
}
