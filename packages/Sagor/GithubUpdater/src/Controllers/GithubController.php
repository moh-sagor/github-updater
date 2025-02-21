<?php

namespace Sagor\GithubUpdater\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GithubController
{
    public function executeCommands()
    {
        $projectRoot = base_path();
        chdir($projectRoot);

        $githubToken = config('github-updater.github_token');
        $userName = config('github-updater.github_username');
        $repoLink = config('github-updater.github_repo_link');
        $artisanCommands = config('github-updater.artisan_commands');

        $repositoryUrl = "https://$userName:$githubToken@$repoLink";

        $commands = [
            "git pull $repositoryUrl",
            $artisanCommands,
        ];

        echo "<pre id='terminal-output' style='background-color: black; color: green; padding: 10px; height: 500px; overflow-y: auto;'>";
        ob_implicit_flush(true);

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(0);

            try {
                $process->run(function ($type, $buffer) {
                    echo nl2br(htmlspecialchars($buffer)) . "\n";
                    echo "<script>document.getElementById('terminal-output').scrollTop = document.getElementById('terminal-output').scrollHeight;</script>";
                    @ob_flush();
                    flush();
                });

                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
            } catch (ProcessFailedException $exception) {
                echo "<span style='color: red;'>Command failed: " . htmlspecialchars($exception->getMessage()) . "</span>";
            }
        }

        echo "</pre>";
    }
}
