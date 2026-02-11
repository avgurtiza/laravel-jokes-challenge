# Laravel Jokes API

A Laravel 12 application with session-based web authentication and Sanctum API token authentication.

## Features

- **Web Interface**: Browse programming jokes with user registration/login
- **API**: Bearer token authentication via Laravel Sanctum
- **AJAX**: Dynamic joke loading with token management

## Tech Stack

- Laravel 12, SQLite, Laravel Sanctum
- Blade templates with BladeWind UI components
- PHP 8.2+

## Quick Start

```bash
cd app/src
composer install
php artisan migrate --seed
php artisan serve
```

See [QUICKSTART.md](QUICKSTART.md) for detailed setup instructions.
