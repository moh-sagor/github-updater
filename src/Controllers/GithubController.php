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

        $artisanCommandsArray = explode(',', $artisanCommands);

        $commands = [
            "git pull $repositoryUrl",
        ];

        foreach ($artisanCommandsArray as $command) {
            $commands[] = trim($command);
        }

        // ASCII Art for "SAGOR"
//         $sagorAsciiArt = "
//    _____         _____  ____  _____  
//   / ____|  /\   / ____|/ __ \|  __ \ 
//  | (___   /  \ | |  __| |  | | |__) |
//   \___ \ / /\ \| | |_ | |  | |  _  / 
//   ____) / ____ \ |__| | |__| | | \ \ 
//  |_____/_/    \_\_____|\____/|_|  \_\                                           
//     ";

//         echo "<pre id='terminal-output' style='background-color: black; color: green; padding: 10px; height: 500px; overflow-y: auto;'>";
//         echo "<span style='color: cyan;'>$sagorAsciiArt</span><br><br>";
//         ob_implicit_flush(true);


$sagorAsciiArt = "
 ▒▓██████████████▓▒░ ░▒▓██████▓▒░░▒▓█▓▒░░▒▓█▓▒░       ░▒▓███████▓▒░░▒▓██████▓▒░ ░▒▓██████▓▒░ ░▒▓██████▓▒░░▒▓███████▓▒░  
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░      ░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░ 
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░      ░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░ 
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓████████▓▒░       ░▒▓██████▓▒░░▒▓████████▓▒░▒▓█▓▒▒▓███▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓███████▓▒░  
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░             ░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░ 
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░             ░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░ 
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░░▒▓██████▓▒░░▒▓█▓▒░░▒▓█▓▒░      ░▒▓███████▓▒░░▒▓█▓▒░░▒▓█▓▒░░▒▓██████▓▒░ ░▒▓██████▓▒░░▒▓█▓▒░░▒▓█▓▒░                                          
       ";
   
       echo '
       <div style="background-color: black; color: green; padding: 10px; ">
           <div style="color: cyan; white-space: pre; font-family: monospace; padding-bottom: 10px; margin-left:15%;">
               ' . htmlspecialchars($sagorAsciiArt) . '
           </div>
           <div id="terminal-output" style="height: 400px; overflow-y: auto; font-family: monospace; margin-top: 10px; white-space: pre-wrap;">
       ';
   
       ob_implicit_flush(true);

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(0);

            try {
                $process->run(function ($type, $buffer) {
                    $lines = explode("\n", $buffer);
                    foreach ($lines as $line) {
                        if (!str_starts_with(trim($line), 'From')) {
                            echo nl2br(htmlspecialchars($line)) . "\n";
                            echo "<script>document.getElementById('terminal-output').scrollTop = document.getElementById('terminal-output').scrollHeight;</script>";
                        }
                    }
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

        return;
    }
}
