# GithubUpdater for Laravel

A Laravel package for automated GitHub pulls and running Artisan commands, developed by Md Sagor Hossain.

## Description
`sagor/github-updater` is a Laravel package that simplifies pulling the latest changes from a GitHub repository and executing predefined Artisan commands (e.g., migrations, seeding) in a single step. It provides both a console command (`php artisan github:pull`) and a web route (`/github-pull`) for convenience.

## Requirements
- PHP 7.4–8.4
- Laravel 8.x, 9.x, 10.x, or 11.x
- Git installed on the server

**Note**: PHP 7.4 reached end-of-life on November 28, 2022, and no longer receives security updates. We recommend using PHP 8.0 or higher for security and performance reasons.

## Installation
Install the package via Composer:

```bash
composer require sagor/github-updater
```

The package uses Laravel's auto-discovery to register the service provider. If auto-discovery is disabled, manually add the service provider to `config/app.php`:

```php
'providers' => [
    // ...
    Sagor\GithubUpdater\Providers\GithubUpdaterServiceProvider::class,
],
```

## Configuration
Publish the configuration file to customize the package settings:

```bash
php artisan vendor:publish --tag=config
```

This will create a `config/github-updater.php` file with the following default configuration:

```php
return [
    'github_token' => env('GITHUB_TOKEN', ''),
    'github_username' => env('GITHUB_USERNAME', ''),
    'github_repo_link' => env('GITHUB_REPO_LINK', ''),
    'artisan_commands' => env('ARTISAN_COMMANDS', 'php artisan migrate --force, php artisan db:seed'),
];
```

### Environment Variables
Set the following environment variables in your `.env` file:

```env
GITHUB_TOKEN=your_github_personal_access_token
GITHUB_USERNAME=your_github_username
GITHUB_REPO_LINK=your_repository_link (e.g., github.com/moh-sagor/project-url.git)
ARTISAN_COMMANDS="php artisan migrate --force,php artisan db:seed"
```

**Security Note**: Ensure your `GITHUB_TOKEN` is kept secure and not exposed in logs or public repositories.

## Usage

### 1. **Console Command**
Run the following Artisan command to pull the latest changes from GitHub and execute the configured Artisan commands:

```bash
php artisan github:pull
```

This command:
- Executes `git pull` to fetch the latest changes from the GitHub repository.
- Runs the Artisan commands specified in `ARTISAN_COMMANDS` (e.g., `php artisan migrate --force` and `php artisan db:seed`).

**Output**:
The command provides feedback on the progress and any errors encountered.

### 2. **Web Route**
Access the web route to trigger the same functionality via a browser or API:

```
GET /github-pull
```

This route:
- Pulls the latest changes from GitHub.
- Executes the configured Artisan commands.
- Displays the output in a terminal-like format in the browser.

**Note**: The route is protected by the `web` middleware. Ensure you have appropriate authentication or authorization middleware if you want to restrict access.

## Example Workflow
1. Configure your `.env` file with your GitHub credentials and repository link.
2. Run the console command or access the web route to pull updates and run migrations/seeds automatically.

## Testing
To test the package locally:
1. Clone the repository and install dependencies:
   ```bash
   composer install
   ```
2. Set up a Laravel project with PHP 7.4–8.4 and Laravel 8–11.
3. Configure the environment variables and test the console command and web route.

## Contributing
Contributions are welcome! Please follow these steps:
1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Submit a pull request with your changes.

## License
This package is open-source software licensed under the [MIT License](LICENSE).

## Support
For issues or questions, please open an issue on the [GitHub repository](https://github.com/moh-sagor/github-updater).

## Acknowledgments
Developed by Md Sagor Hossain ([sagorhassain4@gmail.com](mailto:sagorhassain4@gmail.com)).

---

### Notes for Customization
- Replace `https://github.com/moh-sagor/github-updater` with your actual GitHub repository URL if it differs.
- If you have additional features, testing instructions, or contributors, add them to the relevant sections.
- If you have a `LICENSE` file, ensure it exists in your repository and matches the MIT License mentioned.

This README is concise, clear, and follows best practices for open-source Laravel packages. You can further expand it with screenshots, more detailed examples, or additional sections like "Changelog" or "Roadmap" if applicable. Let me know if you’d like to adjust or add anything specific!
