# Laravel Telescope setup

Telescope is included as a **dev** dependency. Use it only in non-production or tightly controlled environments.

## Requirements

- PHP 8.3+ (see `composer.json`)  
- Composer  

## Enable Telescope

### Option A: Windows helper

Run `install-telescope.bat` when PHP 8.3+ is on your PATH.

### Option B: Manual steps

1. **Install** (if not already in `composer.json`):

   ```bash
   composer require laravel/telescope --dev
   ```

   If Composer complains about the local PHP version:

   ```bash
   composer require laravel/telescope --dev --ignore-platform-reqs
   ```

   Use `--ignore-platform-reqs` only when your runtime PHP version is actually compatible.

2. **Publish config** (optional if config already exists):

   ```bash
   php artisan vendor:publish --tag=telescope-config
   ```

3. **Migrate**:

   ```bash
   php artisan migrate
   ```

   Creates `telescope_entries`, `telescope_entries_tags`, `telescope_monitoring`, etc.

4. **Gate** — edit `app/Providers/TelescopeServiceProvider.php` and restrict `viewTelescope` to trusted emails or `local` only:

   ```php
   Gate::define('viewTelescope', function ($user = null) {
       return in_array($user->email ?? null, [
           'admin@example.com',
       ]) || $this->app->environment('local');
   });
   ```

5. **Open UI** — e.g. `http://localhost:8080/telescope` (match your `APP_URL`).

## `.env` (optional)

```env
TELESCOPE_ENABLED=true
TELESCOPE_PATH=telescope
TELESCOPE_DOMAIN=null
```

## CLI PHP version

If `php -v` is older than the app’s requirement, call the right binary:

```bash
php8.3 artisan migrate
```

## Typical files

- `config/telescope.php`  
- `app/Providers/TelescopeServiceProvider.php`  
- Telescope migrations under `database/migrations/`  

## Verify

Visit `/telescope`. If the dashboard loads, Telescope is running.
