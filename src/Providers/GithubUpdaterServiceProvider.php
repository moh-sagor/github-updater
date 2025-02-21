<?php

namespace Sagor\GithubUpdater\Providers;

use Illuminate\Support\ServiceProvider;
use Sagor\GithubUpdater\Commands\GithubPullCommand;

class GithubUpdaterServiceProvider extends ServiceProvider
{
    /**
     * Path to the package's configuration file.
     *
     * @var string
     */
    protected $configPath;

    /**
     * Path to the package's routes file.
     *
     * @var string
     */
    protected $routesPath;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Define paths
        $this->configPath = __DIR__ . '/../../config/github-updater.php';
        $this->routesPath = __DIR__ . '/../../Routes/web.php';

        // Load Routes
        if (file_exists($this->routesPath)) {
            $this->loadRoutesFrom($this->routesPath);
        }

        // Publish Config
        if (file_exists($this->configPath)) {
            $publishPath = function_exists('config_path') 
                ? config_path('github-updater.php') 
                : base_path('config/github-updater.php');

            $this->publishes([
                $this->configPath => $publishPath,
            ], 'config');
        }

        // Register Console Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GithubPullCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge Config
        if (file_exists($this->configPath)) {
            $this->mergeConfigFrom($this->configPath, 'github-updater');
        }
    }
}