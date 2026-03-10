# ZweToe Pharmacy - Full-Stack Ecommerce & Management

Full-stack pharmacy POS, inventory, and ecommerce platform with Laravel 11 (API + Filament admin) and Next.js 16 storefront.

## At a Glance

- Backend: Laravel 11, Sanctum auth, Filament v3 admin, Spatie Permission RBAC, queues.
- Frontend: Next.js 16 (App Router), React 19, Tailwind 4, axios client.
- Database: SQLite for local; MySQL 8.0 for staging/production.
- CI cadence: two-week release rhythm with protected `main`.

## Repo Map

- `backend/` - API + admin (Vite assets, queues, migrations, seeders).
- `frontend/` - Next.js storefront (guest + registered flows).
- `README.md` - this guide.

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
- `npm run lint` - linting.
- `npm run build` - production build.

## Release Workflow (Two-Week Cadence)

- Day 1: create `release/YYYY-MM-DD` and a tracking issue with scope/checklist.
- Mon-Thu: short-lived feature branches; daily rebase on `main`; keep draft PRs open.
- Day 7: merge ready PRs into the release branch; run full tests/linters; update changelog.
- Days 10-12: feature freeze; only bugfix PRs; tag `vX.Y.0-rc1`.
- Day 14: merge release branch to `main`, tag `vX.Y.0`, publish notes; bump version on `main` to next dev.
- Guardrails: protect `main`, require reviews, CI on every PR, daily status in the release issue.

## Structure Highlights

- `backend/app` - domain logic, controllers, Filament resources.
- `backend/database/seeders` - roles, admin/staff users, sample inventory.
- `frontend/src/app` - App Router pages (guest vs registered home).
- `frontend/src/lib/api.ts` - axios instance with Sanctum handling.

## Troubleshooting

- Jobs not processing: start `php artisan queue:listen` (or use `composer run dev`).
- Images 404: rerun `php artisan storage:link`.
- API calls failing: confirm `NEXT_PUBLIC_API_URL` matches backend host/port and Sanctum cookie domain.
