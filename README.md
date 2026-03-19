# ZweToe Pharmacy - Full-Stack Ecommerce & Management


Full-stack pharmacy POS, inventory, and ecommerce platform with Laravel 11 (API + Filament admin) and Next.js 16 storefront.

## At a Glance

- Backend: Laravel 11, Sanctum auth, Filament v3 admin, Spatie Permission RBAC, queues.
- Frontend: Next.js 16 (App Router), React 19, Tailwind 4, axios client.
- Database: SQLite for local; MySQL 8.0 for staging/production (optional).

## 🏗️ Application Architecture

The project is organized into a headless backend and a decoupled frontend to ensure scalability and maintainability.

### ⚙️ Backend Architecture (Laravel 11 API)
Located in `/backend`, this service manages the core API, database, and the Filament-powered administrative panel.

- **`app/`** — Contains the core domain logic, Eloquent models, API Controllers, and Filament resources.
- **`database/`** — Houses all database migrations, factories, and initial state seeders.
- **`routes/`** — Defines the REST API endpoints (`api.php`) and web routes.
- **`tests/`** — Contains automated feature and unit tests to ensure application stability.
- **`storage/`** — Stores application logs, compiled templates, and user-uploaded media.

### 💻 Frontend Architecture (Next.js 16)
Located in `/frontend`, this directory contains the high-performance Next.js storefront for consumers.

- **`src/app/`** — Implements the Next.js App Router for all public and authenticated pages.
- **`src/lib/`** — Contains the configured Axios API client, authentication logic, and generic utilities.
- **`public/`** — Stores static frontend assets such as images and fonts.

### 📚 Project Documentation
- **`README.md`** — The primary onboarding and setup guide.
- **`erd.md`** — Entity-Relationship Diagram outlining the database schema.

## 📅 Development Timeline

The application was purposefully built through the following milestone phases:

- **Mar 01** — Baseline Setup: Initial repository structure for Next.js and Laravel.
- **Mar 03** — Core Database: Designed schema and defined Eloquent models.
- **Mar 05** — API Authentication: Implemented Sanctum authentication and AuthController.
- **Mar 07** — Security layer: Built Spatie RBAC for Admin and Customer roles.
- **Mar 09** — Category Module: Created Category management with Filament Resource.
- **Mar 11** — Inventory Module: Built Medicine inventory management with expiry alerts.
- **Mar 13** — Frontend Client: Configured Axios interceptors and centralized API service.
- **Mar 15** — State Management: Implemented global Cart context for the storefront.
- **Mar 16** — UI Implementation: Built dynamic product grid with category filtering.
- **Mar 17** — Product Views: Implemented detailed product modals and quantity selection.

## Prerequisites

- PHP 8.2+, Composer
- Node.js 20+, npm
- SQLite (bundled) or MySQL 8.0
- Git

## Setup: Backend

1. `cd backend`
2. Copy env: `cp .env.example .env` (Windows: `copy .env.example .env`)
3. Choose DB: keep `DB_CONNECTION=sqlite` or set MySQL creds.
4. `composer install`
5. `php artisan key:generate`
6. `php artisan migrate --seed`
7. `php artisan storage:link`
8. `npm install` (for Vite/Tailwind assets)
9. Run: `php artisan serve` (or `composer run dev` to serve app + queue + logs + Vite)

## Setup: Frontend

1. `cd frontend`
2. Create `.env.local`:

```env
NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api/v1
GEMINI_API_KEY=your_gemini_key
GROQ_API_KEY=your_groq_key
```

3. `npm install`
4. `npm run dev` then open http://localhost:3000

## Default Test Accounts

- Admin: `admin@gmail.com` / `admin123`
- Staff: `staff@gmail.com` / `staff123`

## Useful Commands

Backend:

- `composer run dev` - serve app, queue listener, pail logs, Vite dev (needs `npm install`).
- `php artisan test` - backend tests.

Frontend:

- `npm run dev` - Next.js dev server.
- `npm run test` - Jest unit and component tests.
- `npm run build` - production build.

## Troubleshooting

- **Images 404**: Rerun `php artisan storage:link` to ensure the public disk is connected.
- **API calls failing**: Confirm `NEXT_PUBLIC_API_URL` accurately matches the backend host/port in your `.env.local`.
- **Database errors**: For local development, ensure the `database/database.sqlite` file exists or run `php artisan migrate`.
