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
        }

        $cyberAsciiArt = "
  ███╗   ███╗ ██████╗ ██╗  ██╗    ███████╗ █████╗  ██████╗  ██████╗ ██████╗ 
  ████╗ ████║██╔═══██╗██║  ██║    ██╔════╝██╔══██╗██╔════╝ ██╔═══██╗██╔══██╗
  ██╔████╔██║██║   ██║███████║    ███████╗███████║██║  ███╗██║   ██║██████╔╝
  ██║╚██╔╝██║██║   ██║██╔══██║    ╚════██║██╔══██║██║   ██║██║   ██║██╔══██╗
  ██║ ╚═╝ ██║╚██████╔╝██║  ██║    ███████║██║  ██║╚██████╔╝╚██████╔╝██║  ██║
  ╚═╝     ╚═╝ ╚═════╝ ╚═╝  ╚═╝    ╚══════╝╚═╝  ╚═╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═╝
        ";

        echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MOH SAGOR | Cyber Deck Deployment</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background-color: #06070a;
    background-image: 
      radial-gradient(circle at 50% 0%, rgba(0, 240, 255, 0.15), transparent 70%),
      radial-gradient(circle at 100% 100%, rgba(255, 0, 127, 0.1), transparent 50%),
      linear-gradient(rgba(0, 240, 255, 0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(0, 240, 255, 0.03) 1px, transparent 1px);
    background-size: 100% 100%, 100% 100%, 30px 30px, 30px 30px;
    color: #00f0ff;
    font-family: \'Share Tech Mono\', monospace;
    min-height: 100vh;
    padding: 1.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .cyber-container {
    width: 100%;
    max-width: 1100px;
    background: rgba(10, 14, 23, 0.88);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(0, 240, 255, 0.4);
    box-shadow: 0 0 35px rgba(0, 240, 255, 0.25), inset 0 0 15px rgba(0, 240, 255, 0.1);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
  }
  .cyber-container::before {
    content: \'\';
    position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #00f0ff, #ff007f, #02ffa1, #00f0ff);
    background-size: 200% 100%;
    animation: cyber-beam 3s linear infinite;
  }
  @keyframes cyber-beam {
    0% { background-position: 0% 0%; }
    100% { background-position: 200% 0%; }
  }
  .cyber-header {
    background: rgba(5, 7, 12, 0.95);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(0, 240, 255, 0.3);
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }
  .cyber-brand {
    font-family: \'Orbitron\', sans-serif;
    font-size: 1.8rem;
    font-weight: 900;
    letter-spacing: 4px;
    color: #ffffff;
    text-shadow: 0 0 10px #00f0ff, 0 0 20px #00f0ff, 0 0 30px #00f0ff;
    text-transform: uppercase;
  }
  .cyber-subbrand {
    font-size: 0.85rem;
    color: #ff007f;
    letter-spacing: 2px;
    text-transform: uppercase;
    text-shadow: 0 0 8px rgba(255, 0, 127, 0.6);
  }
  .cyber-badges {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .cyber-badge {
    padding: 0.4rem 0.85rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .badge-status {
    background: rgba(0, 240, 255, 0.12);
    border: 1px solid #00f0ff;
    color: #00f0ff;
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.3);
  }
  .status-dot {
    width: 8px; height: 8px;
    background-color: #02ffa1;
    border-radius: 50%;
    box-shadow: 0 0 8px #02ffa1;
    animation: blink 1.2s infinite alternate;
  }
  @keyframes blink { 0% { opacity: 0.3; } 100% { opacity: 1; } }

  .cyber-ascii-wrapper {
    background: rgba(2, 4, 8, 0.95);
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid rgba(0, 240, 255, 0.2);
    overflow-x: auto;
    display: flex;
    justify-content: center;
  }
  .cyber-ascii {
    color: #00f0ff;
    text-shadow: 0 0 10px rgba(0, 240, 255, 0.8), 0 0 20px rgba(0, 240, 255, 0.4);
    font-family: \'Share Tech Mono\', monospace;
    font-size: 0.8rem;
    line-height: 1.15;
    white-space: pre;
  }

  .cyber-terminal {
    background: #030407;
    height: 440px;
    overflow-y: auto;
    padding: 1.25rem 1.5rem;
    font-size: 0.95rem;
    line-height: 1.6;
    color: #02ffa1;
    position: relative;
    border-bottom: 1px solid rgba(0, 240, 255, 0.3);
  }
  .log-line {
    margin-bottom: 0.35rem;
    word-break: break-all;
  }
  .log-cmd { color: #ffb700; font-weight: bold; text-shadow: 0 0 5px rgba(255, 183, 0, 0.4); }
  .log-error { color: #ff0055; text-shadow: 0 0 8px rgba(255, 0, 85, 0.6); font-weight: bold; }
  .log-info { color: #00f0ff; }
  .log-success { color: #02ffa1; text-shadow: 0 0 5px rgba(2, 255, 161, 0.4); }

  .cyber-footer {
    background: rgba(5, 7, 12, 0.95);
    padding: 0.85rem 1.5rem;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: rgba(0, 240, 255, 0.75);
    gap: 0.5rem;
  }
</style>
</head>
<body>
<div class="cyber-container">
  <div class="cyber-header">
    <div>
    <div class="cyber-brand">MD SAGOR HOSSAIN</div>
      <div class="cyber-subbrand">CYBER DESK GITHUB UPDATER DECK</div>
    </div>
    <div class="cyber-badges">
      <div class="cyber-badge badge-status">
        <span class="status-dot"></span> SYSTEM ONLINE
      </div>
    </div>
  </div>
  <div class="cyber-ascii-wrapper">
    <pre class="cyber-ascii">' . htmlspecialchars($cyberAsciiArt) . '</pre>
  </div>
  <div id="terminal-output" class="cyber-terminal">
';

        if (function_exists('ob_implicit_flush')) {
            @ob_implicit_flush(true);
        }

        foreach ($commands as $command) {
            $isGitPull = function_exists('str_starts_with')
                ? str_starts_with(trim($command), 'git pull')
                : (strncmp(trim($command), 'git pull', 8) === 0);

            $displayCommand = $isGitPull ? 'git pull' : $command;

            echo "<div class='log-line log-cmd'>&gt; Executing: " . htmlspecialchars($displayCommand) . "</div>";
            echo "<script>var t=document.getElementById('terminal-output');if(t)t.scrollTop=t.scrollHeight;</script>";
            if (function_exists('ob_flush')) { @ob_flush(); }
            flush();

            $process = method_exists(Process::class, 'fromShellCommandline')
                ? Process::fromShellCommandline($command)
                : new Process($command);

            $process->setTimeout(0);

            try {
                $process->run(function ($type, $buffer) use ($repositoryUrl, $githubToken) {
                    $lines = explode("\n", $buffer);
                    foreach ($lines as $line) {
                        $trimmedLine = trim($line);
                        if (empty($trimmedLine)) continue;

                        $startsWithFrom = function_exists('str_starts_with')
                            ? str_starts_with($trimmedLine, 'From')
                            : (strncmp($trimmedLine, 'From', 4) === 0);

                        if (!$startsWithFrom) {
                            $class = 'log-line';
                            if (stripos($trimmedLine, 'error') !== false || stripos($trimmedLine, 'failed') !== false || stripos($trimmedLine, 'fatal') !== false) {
                                $class .= ' log-error';
                            } else if (stripos($trimmedLine, 'INFO') !== false || stripos($trimmedLine, 'DONE') !== false || stripos($trimmedLine, 'Nothing to migrate') !== false || stripos($trimmedLine, 'Seeding') !== false) {
                                $class .= ' log-success';
                            }

                            $sanitizedLine = $line;
                            if (!empty($repositoryUrl)) {
                                $sanitizedLine = str_replace($repositoryUrl, 'git pull', $sanitizedLine);
                            }
                            if (!empty($githubToken)) {
                                $sanitizedLine = str_replace($githubToken, '***', $sanitizedLine);
                            }
                            $sanitizedLine = preg_replace('/https?:\/\/[^:\s]+:[^@\s]+@/', 'https://***@', $sanitizedLine);

                            echo "<div class='{$class}'>" . htmlspecialchars($sanitizedLine) . "</div>";
                            echo "<script>var t=document.getElementById('terminal-output');if(t)t.scrollTop=t.scrollHeight;</script>";
                        }
                    }
                    if (function_exists('ob_flush')) {
                        @ob_flush();
                    }
                    flush();
                });

                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
            } catch (ProcessFailedException $exception) {
                $errorMessage = $exception->getMessage();
                if (!empty($repositoryUrl)) {
                    $errorMessage = str_replace($repositoryUrl, 'git pull', $errorMessage);
                }
                if (!empty($githubToken)) {
                    $errorMessage = str_replace($githubToken, '***', $errorMessage);
                }
                $errorMessage = preg_replace('/https?:\/\/[^:\s]+:[^@\s]+@/', 'https://***@', $errorMessage);

                echo "<div class='log-line log-error'>[✘] Command failed: " . htmlspecialchars($errorMessage) . "</div>";
                echo "<script>var t=document.getElementById('terminal-output');if(t)t.scrollTop=t.scrollHeight;</script>";
            }
        }

        echo '</div>
  <div class="cyber-footer">
    <div>[ SYSTEM ENGINE: LARAVEL ' . (function_exists('app') && method_exists(app(), 'version') ? app()->version() : '13.x') . ' ] [ CYBER DECK GITHUB UPDATER V2.0 ] [ PHP ' . PHP_VERSION . ' ]</div>
    <div>DEVELOPED BY <strong><a href="https://github.com/moh-sagor" target="_blank" style="color: #02ffa1;"> MOH SAGOR</a></strong></div>
  </div>
</div>
</body>
</html>';

        return;
    }
}
