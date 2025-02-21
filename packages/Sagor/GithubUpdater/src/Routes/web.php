<?php

use Illuminate\Support\Facades\Route;
use Sagor\GithubUpdater\Controllers\GithubController;

Route::get('/github-pull', [GithubController::class, 'executeCommands'])
    ->middleware('web')
    ->name('github.pull');
