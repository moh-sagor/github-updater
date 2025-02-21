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
        $this->publishes([
            __DIR__ . '/config/github-updater.php' => config_path('github-updater.php'),
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
