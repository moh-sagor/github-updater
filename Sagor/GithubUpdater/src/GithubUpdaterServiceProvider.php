<?php

namespace Sagor\GithubUpdater;

use Illuminate\Support\ServiceProvider;

class GithubUpdaterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load Routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        // Load Config
        $configPath = function_exists('config_path') ? config_path('github-updater.php') : base_path('config/github-updater.php');

        $this->publishes([
            __DIR__ . '/config/github-updater.php' => $configPath,
        ], 'config');

        // Register Console Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Sagor\GithubUpdater\Commands\GithubPullCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/github-updater.php', 'github-updater');
    }
}
