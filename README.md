# GithubUpdater for Laravel

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-laravel-readme.png" width="120" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Automated GitHub repository updates and Artisan command deployment pipeline for Laravel.</strong>
</p>

<p align="center">
  <a href="https://packagist.org/packages/sagor/github-updater"><img src="https://img.shields.io/badge/Laravel-5.5%20to%2013.x-FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 5.5 to 13.x"></a>
  <a href="https://packagist.org/packages/sagor/github-updater"><img src="https://img.shields.io/badge/PHP-7.2%20to%208.4%2B-777BB4.svg?style=for-the-badge&logo=php&logoColor=white" alt="PHP 7.2 to 8.4+"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge" alt="License MIT"></a>
</p>

---

## 📖 Overview

**`sagor/github-updater`** is a high-performance Laravel package designed to simplify and automate repository pulling and post-update deployment tasks. With support for **Laravel 5.5 up to Laravel 13.x** and **PHP 7.2 through PHP 8.4+**, it provides a seamless way to pull updates from private or public GitHub repositories and execute chained Artisan commands (migrations, seeders, cache clearing, optimization) in a single action.

Whether triggered via **Artisan CLI** or the built-in **Web Terminal route** (with real-time streaming output), `github-updater` ensures your application stays updated effortlessly.

---

## ✨ Features

- 🚀 **Universal Compatibility**: Full support for Laravel versions `5.5`, `5.8`, `6.x`, `7.x`, `8.x`, `9.x`, `10.x`, `11.x`, `12.x`, and `13.x`.
- 🐘 **Modern & Legacy PHP Support**: Tested and compatible across PHP `7.2`, `7.4`, `8.0`, `8.1`, `8.2`, `8.3`, and `8.4+`.
- 🔑 **Secure Authentication**: Supports Personal Access Tokens (PAT) and custom repository URLs.
- ⚡ **Artisan Command Chaining**: Run multiple Artisan tasks sequentially after pull (e.g., `migrate --force`, `db:seed`, `config:cache`).
- 🖥️ **Web Terminal UI**: Built-in dark-themed web terminal output with real-time autoscrolling logs.
- 💻 **Console Integration**: Dedicated `php artisan github:pull` command for server scripts and CRON tasks.
- 📦 **Zero-Config Auto Discovery**: Automatically registers package service providers and aliases.

---

## 🏗️ Architecture & Execution Flow

The following diagram illustrates how requests flow through `GithubUpdater`:

```mermaid
flowchart TD
    subgraph Triggers ["Deployment Triggers"]
        A["🌐 Web Request: GET /github-pull"]
        B["💻 CLI Command: php artisan github:pull"]
    end

    subgraph Package ["GithubUpdater Core Engine"]
        C{"Router / Controller"}
        D{"Console Handler"}
        E["Load Configuration<br/>(config/github-updater.php)"]
        F["Build Git URL with PAT Token<br/>(https://user:token@repo.git)"]
        G["Run 'git pull' Subprocess"]
        H["Parse ARTISAN_COMMANDS Pipeline"]
        I["Execute Chained Artisan Commands"]
    end

    subgraph Outputs ["Execution Feedback"]
        J["🖥️ Streamed Web Terminal UI"]
        K["📋 Console Log Output"]
    end

    A --> C
    B --> D
    C --> E
    D --> E
    E --> F
    F --> G
    G --> H
    H --> I
    I -->|Web Output| J
    I -->|CLI Output| K
```

---

## 📋 Compatibility Matrix

| Technology | Supported Versions | Notes |
| :--- | :--- | :--- |
| **Laravel Framework** | `^5.5`, `^5.8`, `^6.0`, `^7.0`, `^8.0`, `^9.0`, `^10.0`, `^11.0`, `^12.0`, `^13.0` | Full backward & forward compatibility |
| **PHP Runtime** | `^7.2`, `^7.4`, `^8.0`, `^8.1`, `^8.2`, `^8.3`, `^8.4+` | Handles cross-version polyfills & processes |
| **Symfony Process** | `~3.4`, `^4.4`, `^5.0`, `^6.0`, `^7.0`, `^8.0` | Adapts dynamically to installed Symfony components |

---

## 📥 Installation

Install the package into your Laravel project using Composer:

```bash
composer require sagor/github-updater
```

### Service Provider Registration

For Laravel projects using **Package Auto-Discovery**, the service provider will automatically register.

If auto-discovery is disabled in your project, manually add `GithubUpdaterServiceProvider` to the `providers` array in `config/app.php`:

```php
'providers' => [
    // ...
    Sagor\GithubUpdater\Providers\GithubUpdaterServiceProvider::class,
],
```

---

## ⚙️ Configuration

Publish the package configuration file to your application's `config/` directory:

```bash
php artisan vendor:publish --tag=config
```

Or target the specific provider:

```bash
php artisan vendor:publish --provider="Sagor\GithubUpdater\Providers\GithubUpdaterServiceProvider"
```

This creates `config/github-updater.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GitHub Personal Access Token
    |--------------------------------------------------------------------------
    | Your GitHub Personal Access Token (PAT) used for authenticating
    | private repository git operations.
    */
    'github_token' => env('GITHUB_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | GitHub Username / Organization
    |--------------------------------------------------------------------------
    | The owner username or organization of the repository.
    */
    'github_username' => env('GITHUB_USERNAME', ''),

    /*
    |--------------------------------------------------------------------------
    | Repository Link
    |--------------------------------------------------------------------------
    | Target repository URL (e.g. github.com/username/repository.git)
    */
    'github_repo_link' => env('GITHUB_REPO_LINK', ''),

    /*
    |--------------------------------------------------------------------------
    | Post-Pull Artisan Commands
    |--------------------------------------------------------------------------
    | Comma-separated list of Artisan commands executed sequentially after
    | a successful git pull.
    */
    'artisan_commands' => env('ARTISAN_COMMANDS', 'php artisan migrate --force, php artisan db:seed'),
];
```

### Environment Variables (`.env`)

Add and configure the following variables in your project's `.env` file:

```env
GITHUB_TOKEN=ghp_YourPersonalAccessToken1234567890
GITHUB_USERNAME=your-github-username
GITHUB_REPO_LINK=github.com/your-username/your-repository.git
ARTISAN_COMMANDS="php artisan migrate --force, php artisan db:seed, php artisan config:cache"
```

> ⚠️ **Security Reminder**: Never commit your `GITHUB_TOKEN` to version control. Always set it inside server `.env` files.

---

## 🚀 Usage

### 1. CLI Artisan Command

To perform an update directly from the command line or CI/CD deployment scripts:

```bash
php artisan github:pull
```

**What it does:**
1. Connects to GitHub using your configured authentication parameters.
2. Executes `git pull` from the remote repository branch.
3. Sequentially executes all Artisan commands listed in `ARTISAN_COMMANDS`.
4. Outputs execution progress directly to stdout.

---

### 2. Web Interface (`/github-pull`)

Access the streaming terminal interface via browser:

```http
GET /github-pull
```

**Interface Highlights:**
- Custom terminal dark mode container.
- ASCII Banner display.
- Real-time line-by-line output streaming with autoscroll support.

#### Customizing Middleware & Route Security

By default, the `/github-pull` route uses the `web` middleware group. To restrict access to authorized users or admins, group the route in your `routes/web.php` file:

```php
use Sagor\GithubUpdater\Controllers\GithubController;

Route::middleware(['web', 'auth', 'can:manage-deployments'])->group(function () {
    Route::get('/github-pull', [GithubController::class, 'executeCommands'])
        ->name('github.pull');
});
```

---

## 🔒 Security Best Practices

1. **Protect Web Routes**: Always wrap `/github-pull` with authentication middleware (`auth`, admin roles) to prevent unauthorized execution.
2. **Token Permissions**: Scope your GitHub Personal Access Token (PAT) with minimum required permissions (`repo:status`, `repo_deployment`, or `contents:read`).
3. **Environment Security**: Keep `.env` permissions strict (`chmod 600 .env`) on production servers.

---

## 🧪 Testing

The package includes a full PHPUnit test suite using **Orchestra Testbench**. To run unit and feature tests:

```bash
vendor/bin/phpunit
```

### Test Coverage Includes:
- Service Provider Registration & Config Merging.
- Named Route Binding (`github.pull`).
- Artisan Command Registration (`github:pull`).

---

## 📄 License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

---

## 👨‍💻 Author & Support

Developed and maintained by **Md Sagor Hossain** ([sagorhassain4@gmail.com](mailto:sagorhassain4@gmail.com)).

- GitHub: [@moh-sagor](https://github.com/moh-sagor)
- Repository: [github.com/moh-sagor/github-updater](https://github.com/moh-sagor/github-updater)

If you find this package helpful, please give it a ⭐️ on GitHub!
