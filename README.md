# 🚗 Parking Management System (Full-Stack)

A real-world parking management system built as a clean full-stack application.

Laravel handles the core business logic (tickets, payments, pricing), while Angular acts as a client over a REST API.


It’s meant for real use (entry/exit, payments, receipts, admin views), not a weekend CRUD tutorial.

---

## Short description

**What it does:** Vehicles in and out, spots per lot, open tickets, day/night pricing, payments, PDF receipts where that’s wired up, plus history and a dashboard so someone can see what’s going on without walking the lot.

**Why bother:** At the gate you want fewer dumb mistakes and a total people agree on. That’s basically what this is optimized for.

---

## 🧩 Features

**Day-to-day**

- Entry / exit flow and ticket state  
- See which spots are free in a given lot  
- Payments tied to how the backend calculates totals  
- Receipt PDFs (entry/exit) where implemented  

**Back office**

- Dashboard charts (occupancy-ish, revenue-ish numbers—depends what you’ve seeded)  
- Ticket history with filters  
- Frontend has export/chart tooling (xlsx, echarts) if you want to lean on that for reports  

**API side**

- Sanctum tokens, JSON in/out  
- Angular talks through a small shared HTTP layer + `environment.apiUrl` so you’re not hardcoding localhost everywhere  

---

## 🧠 Technical highlights

- **N+1:** I went through the painful part of tightening queries on lists, ticket relations, and dashboard stats so we’re not hammering MySQL with lazy-load loops. Not glamorous work, but it shows up fast when data grows.  
- **Controllers aren’t doing everything:** Business flow lives in use cases / services; controllers mostly validate and call stuff. Easier to test, easier to change without breaking routes.  
- **Backend is layered** (domain / application / infra / HTTP). Not textbook-perfect everywhere, but the split is real—you can swap persistence or tweak pricing without touching every file.  
- **Frontend stays dumb (on purpose):** It calls the API and renders. Pricing rules and ticket logic stay on the server where they belong.  
- **UI is split by modules** (tickets, shared services, etc.) so it doesn’t turn into one 4000-line component folder.  

---

## 🛠 Tech stack

**Backend** — `parking-management-backend/`

- Laravel 11, PHP 8.3  
- MySQL 8 (Docker compose file included)  
- Redis in the stack for cache/queues when you turn that on  
- Docker Compose: PHP-FPM, Nginx, DB, Redis  
- Sanctum for API auth  
- DomPDF for receipts; jobs/queues if you async that  

**Frontend** — `parking-management-frontend/`

- Angular 21  
- Material + Bootstrap (yeah, both—works fine)  
- RxJS, ngx-echarts for the dashboard bits  
- `environment.ts` / `environment.prod.ts` for API base URL  


---

## 🏗 Architecture

Backend exposes REST. Frontend logs in, stores the token, hits `/api/...`. Nothing fancy—just a clean boundary so two people could work on API vs UI without stepping on each other constantly.

---

## 📸 Preview

A few quick screenshots of the system in action:

![Dashboard](/Docs/Photos/Dashboard.png)
![Tickets](/Docs/Photos/Tickets%20Lista.png)

(Create `docs/` and add the files, or swap paths to wherever you keep images.)

---

## ⚙️ Installation

### Backend

```bash
cd parking-management-backend
cp .env.example .env
# fill in DB, APP_URL, whatever you need

composer install
php artisan key:generate
php artisan migrate
# php artisan db:seed   # if you want sample data
```

**Docker** (what I usually use so it matches prod-ish):

```bash
cd parking-management-backend
docker compose up -d
```

Nginx is mapped to **8080** in the compose file—check `docker-compose.yml` if yours differs. Run migrate inside the container if that’s how you’re developing.

### Frontend

```bash
cd parking-management-frontend
npm install
npm start
```

Dev server is typically **localhost:4200**. Set `apiUrl` in `src/environments/environment.ts`—for Docker backend that’s often `http://localhost:8080/api`.

---

## 🔌 API (quick)

- Base: `{host}/api`  
- Login: `POST /login`, then `Authorization: Bearer …` on the rest  
- Vehicles, lots, entry/exit, payment, tickets, dashboard, receipt routes—see the backend doc for the boring details  

Details: `parking-management-backend/API_DOCUMENTATION.md`

---

## 🌐 Deployment

- **Docker:** Compose file is there for the Laravel side; run it on a VPS or whatever you trust.  
- **Frontend:** `npm run build` and host the static output (Nginx, S3+CDN, etc.). Don’t forget to fix `environment.prod.ts`.  
- **Railway / Render / Fly / similar:** Same idea—Laravel + MySQL somewhere, static Angular somewhere else. I don’t ship provider-specific config in this repo; you wire env vars in their UI.

---

## 👨‍💻 Author

[Joiner Davila - Upwork](https://www.upwork.com/freelancers/~01f8288ffe387050cc)

---

## 🤝 For clients

If you're looking for a system like this (or something similar), I can adapt this architecture to your business needs.

This project is just a base — it can be extended with:
- Mobile apps
- Multi-location parking
- Custom pricing rules
- Integrations (payments, cameras, etc.)

Feel free to reach out.



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
