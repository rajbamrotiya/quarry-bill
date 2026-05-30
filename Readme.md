# Quarry Bill

This project is a Laravel application built with Livewire and NativePHP.

## Requirements

- PHP 8.3 or higher
- Composer
- Node.js & NPM

## Setup Instructions

1. **Install PHP dependencies:**
   ```bash
   composer install
   ```

2. **Environment Setup:**
   Copy the example environment file and configure your database settings.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Migrations:**
   ```bash
   php artisan migrate
   ```

4. **Install NPM dependencies & build assets:**
   ```bash
   npm install
   npm run build
   ```

## Development

You can start the local development server with the following command, which runs the PHP server, queue listener, and Vite simultaneously:

```bash
composer run dev
```

### NativePHP Desktop App

This project is configured with NativePHP. To run the desktop application locally during development:

```bash
composer run native:dev
```

## Testing & Code Quality

- **Run tests:**
  ```bash
  composer test
  ```
- **Lint code (using Laravel Pint):**
  ```bash
  composer lint
  ```

## License

This project is open-sourced software licensed under the MIT license.
