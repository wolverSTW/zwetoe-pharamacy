# ZweToe Pharmacy — Full-Stack E-Commerce & Management System

## Overview

ZweToe Pharmacy is a full-stack pharmacy management and e-commerce platform developed as part of an academic project. The system integrates a **Laravel 11** RESTful API with a **Filament v3** administrative panel on the backend and a **Next.js 16** consumer-facing storefront on the frontend. It provides end-to-end support for inventory management, point-of-sale operations, role-based access control, and online ordering.

## Technology Stack

| Layer       | Technology                                                        |
|-------------|-------------------------------------------------------------------|
| Backend     | Laravel 11, Laravel Sanctum, Filament v3, Enum-based RBAC, Queues |
| Frontend    | Next.js 16 (App Router), React 19, Tailwind CSS 4, Axios          |
| Database    | SQLite (development), MySQL 8.0 (staging/production)               |
| Testing     | PHPUnit (backend), Jest (frontend)                                 |

## Application Architecture

The system adopts a **headless architecture**, decoupling the backend API from the frontend client to maximise scalability, maintainability, and independent deployability.

### Backend Service (Laravel 11 API)

Located in the `/backend` directory, the backend service is responsible for the core business logic, data persistence, and the Filament-powered administrative interface.

| Directory      | Purpose                                                                  |
|----------------|--------------------------------------------------------------------------|
| `app/`         | Core domain logic, Eloquent models, API controllers, and Filament resources |
| `database/`    | Database migrations, model factories, and initial state seeders           |
| `routes/`      | REST API endpoint definitions (`api.php`) and web route configuration     |
| `tests/`       | Automated feature and unit tests for application stability verification   |
| `storage/`     | Application logs, compiled templates, and user-uploaded media             |

### Frontend Client (Next.js 16)

Located in the `/frontend` directory, the frontend client delivers a high-performance consumer storefront.

| Directory      | Purpose                                                                  |
|----------------|--------------------------------------------------------------------------|
| `src/app/`     | Next.js App Router implementation for public and authenticated pages      |
| `src/lib/`     | Configured Axios API client, authentication logic, and shared utilities   |
| `public/`      | Static assets including images and fonts                                  |

### Supporting Documentation

- **`README.md`** — Primary onboarding and setup guide.
- **`erd.md`** — Entity-Relationship Diagram documenting the database schema.

## Key Features

- **Inventory Management** — Real-time stock tracking with automated expiry date monitoring and low-stock alerts.
- **Role-Based Access Control** — Custom enum-based RBAC supporting Admin, Staff, and Customer roles with granular permission enforcement.
- **Point-of-Sale Operations** — In-store sales processing with automatic stock synchronisation upon transaction completion.
- **E-Commerce Storefront** — Dynamic product catalogue with category filtering, cart management, and secure checkout.
- **Administrative Panel** — Filament v3-powered dashboard for comprehensive business operations management.

## Prerequisites

- PHP 8.2+ and Composer
- Node.js 20+ and npm
- SQLite (bundled) or MySQL 8.0
- Git

## Installation & Setup

### Backend

```bash
cd backend
cp .env.example .env          # Windows: copy .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install                    # Required for Vite/Tailwind asset compilation
php artisan serve              # Or: composer run dev (serves app + queue + logs + Vite)
```

### Frontend

1. Navigate to the frontend directory:
   ```bash
   cd frontend
   ```

2. Create a `.env.local` file with the following variables:
   ```env
   NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api/v1
   GEMINI_API_KEY=your_gemini_key
   GROQ_API_KEY=your_groq_key
   ```

3. Install dependencies and start the development server:
   ```bash
   npm install
   npm run dev
   ```

4. Access the storefront at `http://localhost:3000`.

## Default Credentials

| Role     | Email                  | Password    |
|----------|------------------------|-------------|
| Admin    | admin@gmail.com        | admin123    |
| Staff    | staff@gmail.com        | staff123    |
| Customer | customer@gmail.com     | customer123 |

## Development Commands

| Context  | Command              | Description                                       |
|----------|----------------------|---------------------------------------------------|
| Backend  | `php artisan test`   | Execute the backend test suite                    |
| Backend  | `composer run dev`   | Start application server, queue worker, and Vite  |
| Frontend | `npm run dev`        | Start the Next.js development server              |
| Frontend | `npm run test`       | Execute Jest unit and component tests             |

## Troubleshooting

| Issue                  | Resolution                                                                                                         |
|------------------------|--------------------------------------------------------------------------------------------------------------------|
| Hashing errors         | Ensure models use the `hashed` cast and that factories/seeders provide plain-text passwords.                        |
| Image 404 responses    | Re-run `php artisan storage:link` to re-establish the public disk symlink.                                          |
| API connection failures| Verify that `NEXT_PUBLIC_API_URL` in `.env.local` matches the backend host and port exactly.                        |
