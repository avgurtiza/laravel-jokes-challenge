# Laravel Jokes API

A Laravel 12 application with session-based web authentication and Sanctum API token authentication.

## Features

- **Web Interface**: Browse programming jokes with user registration/login
- **API**: Bearer token authentication via Laravel Sanctum
- **AJAX**: Dynamic joke loading with token management

## Tech Stack

- Laravel 12, SQLite, Laravel Sanctum
- Blade templates with BladeWind UI components
- Tailwind CSS 4, Vite 7
- PHP 8.2+

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ or Bun
- SQLite (included with PHP)

## Quick Start

```bash
cd app/src
composer install
npm install          # or: bun install
npm run build        # or: bun run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite  # Required for non-interactive environments
php artisan migrate --seed
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Development

For development with hot reload:

```bash
# Terminal 1: Start Vite dev server
npm run dev          # or: bun run dev

# Terminal 2: Start Laravel server
php artisan serve
```

## Testing

```bash
cd app/src
php artisan test
```

## Web Routes

| Method | URI | Name | Middleware | Description |
|--------|-----|------|------------|-------------|
| GET | / | home | web | Welcome page |
| GET | /login | login | guest | Login form |
| POST | /login | | guest | Submit login |
| GET | /register | register | guest | Registration form |
| POST | /register | | guest | Submit registration |
| POST | /logout | logout | auth | Logout |
| GET | /jokes | jokes.index | auth | Jokes page |

All routes are stateless except `/jokes` which requires authentication.

## API Endpoints

All API endpoints require authentication via Laravel Sanctum Bearer token.

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | /api/jokes | api.jokes.index | Returns 3 programming jokes as JSON |
| GET | /api/token | | Returns the authenticated user's token ID |

### Example Request

```bash
curl -X GET http://localhost:8000/api/jokes \
  -H "Authorization: Bearer <your-token>"
```

### Example Response

```json
{
  "data": [
    {
      "id": 1,
      "type": "programming",
      "setup": "Why do programmers prefer dark mode?",
      "punchline": "Because light attracts bugs."
    }
  ]
}
```

**Note:** The API returns exactly 3 jokes per request.

## Authentication

### Web Authentication (Session-based)

The application uses Laravel's built-in authentication with session storage.

#### Registration

1. Visit `/register`
2. Fill in name, email, password, and password confirmation
3. Submit the form
4. You will be automatically logged in and redirected to `/jokes`
5. An API token is automatically created and stored in your session

#### Login

1. Visit `/login`
2. Enter your email and password
3. Submit the form
4. On success, you are redirected to `/jokes`
5. Your existing or new token is stored in the session

#### Logout

Click the logout button to end your session.

### API Authentication (Sanctum Bearer Token)

The API uses Laravel Sanctum for token-based authentication.

#### Obtaining a Token

Tokens are automatically created and stored in your session upon registration or login. To retrieve your token:

```bash
curl -X GET http://localhost:8000/api/token \
  -H "Authorization: Bearer <your-token>"
```

#### Using the Token

Include the Bearer token in the Authorization header:

```bash
curl -X GET http://localhost:8000/api/jokes \
  -H "Authorization: Bearer 1|abc123..."
```

#### Token Storage

Tokens are stored in the `personal_access_tokens` table and associated with your user account.

## Code Quality

### Code Style (Laravel Pint)

```bash
# Check code style without making changes
./vendor/bin/pint --test

# Format code
./vendor/bin/pint
```

## External API

The application fetches programming jokes from the official Joke API.

- **API URL:** `https://official-joke-api.appspot.com/jokes/programming/ten`
- **Method:** GET
- **Response:** Returns 10 programming jokes with setup and punchline format

The application requests 10 jokes and randomly selects 3 for display. Caching is not currently implemented; each request goes to the external API.

See [JOKE_API_URL](/app/src/.env.example) in `.env.example` for configuration.
