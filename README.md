# Parking Management System — Backend

Parking management API built with **Laravel 11** and **Clean Architecture**.

## Architecture

Layers:

- **Domain/** — Entities, repository interfaces, domain services, DTOs  
- **Application/** — Use cases and actions  
- **Infrastructure/** — Eloquent repository implementations  
- **Http/** — Controllers, form requests, API resources  

## Stack

- Laravel 11  
- PHP 8.3  
- MySQL 8 (or MariaDB)  
- Laravel Sanctum (API tokens)  
- Docker & Docker Compose (optional)  

## Local setup

1. Clone the repository.  
2. Copy `.env.example` to `.env` and set `APP_KEY`, database, and mail as needed.  
3. With Docker: `docker-compose up -d`, then run Artisan inside the app container.  
4. Run migrations: `php artisan migrate`  
5. (Optional) Seed demo data: `php artisan db:seed`  

## Project layout

```
app/
├── Domain/              # Domain layer
│   ├── Entities/
│   ├── Repositories/    # Interfaces
│   ├── Services/
│   └── DTOs/
├── Application/       # Application layer
│   ├── UseCases/
│   └── Actions/
├── Infrastructure/    # Infrastructure
│   └── Repositories/  # Eloquent implementations
└── Http/              # HTTP layer
    ├── Controllers/
    ├── Requests/
    └── Resources/
```

## API overview

All routes are prefixed with `/api` unless your web server maps the app root differently.

| Area | Method & path | Description |
|------|----------------|-------------|
| Auth | `POST /api/login`, `POST /api/logout` | Token login / revoke |
| Vehicles | `GET/POST /api/vehicles`, `GET /api/vehicles/{id}`, `GET /api/vehicles/search-by-plate` | CRUD & plate lookup |
| Parking lots | `GET/POST /api/parking-lots`, `GET/PUT /api/parking-lots/{id}` | Lot management |
| Spots | `GET /api/parking-spots/available?parking_lot_id=` | Available spots |
| Parking ops | `POST /api/parking/entry`, `POST /api/parking/exit`, `POST /api/parking/payment` | Entry, exit, payment |
| Queries | `GET /api/parking/current`, `GET /api/parking/history`, ticket by id/plate | Lists & lookups |

See **`API_DOCUMENTATION.md`** for request/response examples and error formats.

## SOLID (summary)

- Single responsibility per class  
- Open/closed via interfaces and new use cases  
- Liskov-safe repository contracts  
- Small, focused interfaces  
- Dependencies on abstractions (constructor injection)  

## Tests

```bash
composer install
cp .env.example .env   # configure DB for testing
php artisan key:generate --force
php artisan migrate --force
php artisan test
```

## CI (GitHub Actions)

On push and pull requests, **CI** runs:

1. **PSR & code style** — strict Composer autoload check + Laravel Pint (`composer cs-check`).  
2. **Tests** — MariaDB service, migrations, `php artisan test`, then `php artisan optimize`.  

Workflow: `.github/workflows/ci.yml`  
Composite setup: `.github/actions/php_install/action.yml`  

Local style check: `composer cs-check`  
Auto-fix style: `composer cs-fix`  

## License

MIT
