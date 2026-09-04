<?php

namespace Sagor\GithubUpdater\Tests;

use Orchestra\Testbench\TestCase;
use Sagor\GithubUpdater\Providers\GithubUpdaterServiceProvider;
use Illuminate\Support\Facades\Route;

class GithubUpdaterTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            GithubUpdaterServiceProvider::class,
        ];
    }

    public function test_config_is_loaded()
    {
        $this->assertEquals('', config('github-updater.github_token'));
        $this->assertEquals('', config('github-updater.github_username'));
        $this->assertEquals('', config('github-updater.github_repo_link'));
    }

    public function test_route_is_registered()
    {
        $route = Route::getRoutes()->getByName('github.pull');
        $this->assertNotNull($route);
        $this->assertEquals('github-pull', $route->uri());
    }

    public function test_artisan_command_is_registered()
    {
        $this->assertTrue(array_key_exists('github:pull', \Illuminate\Support\Facades\Artisan::all()));
    }
}
