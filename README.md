# Laravel 12 Multivendor FleetCart

A full-stack multi-vendor e-commerce administration platform built on Laravel 12, inspired by FleetCart. The application provides everything needed to run a marketplace: vendor onboarding and isolation, product catalog with variations and options, order processing with per-vendor commission tracking, payouts and withdrawals, RBAC, multilingual content, polymorphic media management, and a comprehensive admin backend.

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12.53-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

---

## Table of contents

1. [Highlights](#highlights)
2. [Tech stack](#tech-stack)
3. [Quick start](#quick-start)
4. [Default credentials](#default-credentials)
5. [Architecture](#architecture)
6. [Project layout](#project-layout)
7. [Database](#database)
8. [Routes](#routes)
9. [Development commands](#development-commands)
10. [Testing](#testing)
11. [Code style](#code-style)
12. [Deployment notes](#deployment-notes)
13. [Further documentation](#further-documentation)
14. [License](#license)

---

## Highlights

- **103 tables** modelled across two consolidated migrations (multivendor core + stock management)
- **96 per-table database seeders** wired through a FK-safe `DatabaseSeeder`
- **618 admin routes** organised by domain (products, orders, vendors, reports, settings, etc.)
- **Resource-based RBAC** — every backend controller gates CRUD via `BaseController` and the `PermissionMiddleware`
- **Vendor isolation** — orders are split into `vendor_orders`, products carry a per-vendor approval status, payouts and withdrawals tracked per vendor
- **Polymorphic translations** — any Eloquent model can become multilingual via the `HasTranslations` trait
- **Polymorphic media zones** — centralised `media` table with `entity_media` pivot tracking zone (logo, banner, gallery, etc.) for any owner type
- **Stock management** — purchase orders, goods receipts, stock takes, transfers, adjustments, and per-warehouse stock levels
- **SQLite-friendly** — the audited query layer works against both SQLite (default for dev) and MySQL (recommended for prod)

## Tech stack

| Layer            | Technology                                                  |
| ---------------- | ----------------------------------------------------------- |
| Language         | PHP **8.3**                                                 |
| Framework        | Laravel **12.53** (skeleton from `laravel/laravel`)         |
| Auth scaffolding | Laravel Breeze 2.x                                          |
| Database         | SQLite (dev default) · MySQL/MariaDB (prod)                 |
| Frontend build   | Vite                                                        |
| CSS              | Tailwind CSS                                                |
| JS               | Alpine.js, jQuery (Metismenu sidebar, jsTree categories)    |
| Templating       | Blade                                                       |
| Flash messages   | `php-flasher/flasher-laravel` + SweetAlert adapter          |
| CLI tooling      | Laravel Pail (log tail), Pint (code style), Sail (optional) |
| Testing          | PHPUnit 11, Mockery, Faker, Collision                       |

## Quick start

> The fastest path is SQLite + the built-in PHP server. Switch `DB_CONNECTION` to `mysql` when you're ready for production.

```bash
# 1. Clone
git clone https://github.com/oudampanha/laravel12-multivendor-fleetcart.git
cd laravel12-multivendor-fleetcart

# 2. Install dependencies
composer install
npm install

# 3. Bootstrap the env file and key
cp .env.example .env
php artisan key:generate

# 4. Create the SQLite file (skip if using MySQL — set DB_CONNECTION instead)
mkdir -p database
touch database/database.sqlite

# 5. Migrate + seed all 103 tables, then build front-end assets
php artisan migrate:fresh --seed --force
npm run build

# 6. Run it
php artisan serve --host 127.0.0.1 --port 8000
```

The admin panel is at <http://127.0.0.1:8000/admin>.

For a hot-reloading dev environment with the queue worker, log tail, and Vite dev server all running together:

```bash
composer run dev
```

## Default credentials

After `db:seed`, the seeded super-admin is:

| Field    | Value                  |
| -------- | ---------------------- |
| Email    | `superadmin@gmail.com` |
| Password | `12345678`             |

The super-admin role bypasses standard UI status checks and has every permission granted. Additional roles (admin, vendor, customer) and 400+ permissions are seeded; see `database/seeders/RoleTableSeeder.php` and `PermissionTableSeeder.php`.

## Architecture

### BaseController + resource-based permissions

Every backend controller extends `app/Http/Controllers/Backend/BaseController.php`, which:

- Requires `auth` middleware
- Requires `permission:dashboard_access`
- Maps CRUD methods to permissions automatically:
  - `index` / `show` → `{resource}_access`
  - `create` / `store` → `{resource}_create`
  - `edit` / `update` → `{resource}_edit`
  - `destroy` → `{resource}_delete`
- Allows extra guards via `protected array $additionalPermissions`

```php
class ProductController extends BaseController
{
    protected string $resource = 'product';
    protected array $additionalPermissions = ['product_management_access'];
}
```

Permission checks are surfaced into Blade through custom directives:

```blade
@permission('user_create')
    <a href="{{ route('admin.users.create') }}">Create User</a>
@endpermission

@cancrud('product', 'edit')
    <button>Edit Product</button>
@endcancrud
```

The full permission catalogue lives in [`CONTROLLER_PERMISSIONS.md`](CONTROLLER_PERMISSIONS.md). Implementation details (PermissionMiddleware, HasPermissions trait, Blade directives) are documented in [`PERMISSION_USAGE.md`](PERMISSION_USAGE.md).

### Vendor isolation

- Each `User` may have one `Vendor` (`User hasOne Vendor`).
- `Product.vendor_id` ties every product to one vendor; products carry a `vendor_status` (pending / approved / rejected) used by the admin approval flow.
- Customer-facing orders live in `orders`; for commission tracking and per-vendor fulfilment, each line item also writes to `vendor_orders`.
- Earnings move from `vendor_orders` → `vendor_payouts` (admin-initiated) and `vendor_withdrawals` (vendor-initiated, admin-approved).
- Vendor-scoped notifications, settings, reviews, and shipping zones are first-class.

### Translation system

The `HasTranslations` trait (`app/Traits/HasTranslations.php`) makes any model multilingual without per-model translation tables:

```php
class Product extends Model
{
    use HasTranslations;

    protected array $translatable = ['name', 'description'];
}

$product->setTranslation('name', 'Product Name', 'en');
$name = $product->getTranslation('name', 'es');
```

All translations are stored polymorphically in the `translations` table with columns:

- `translatable_type` (FQCN of the owner model)
- `translatable_id` (owner PK)
- `locale` (e.g. `en`, `es`, `km`)
- `field` (the attribute being translated)
- `value`

The full system, including the admin UI, import/export, and missing-translation discovery, is documented in [`TRANSLATION_SYSTEM_GUIDE.md`](TRANSLATION_SYSTEM_GUIDE.md).

### Media zones

A single `media` table stores every uploaded file. Owners attach media polymorphically via the `entity_media` pivot, which adds a **`zone`** column so the same owner can carry distinct visual roles (logo, banner, gallery, featured, attachments, etc.).

```php
// In a model
public function media()
{
    return $this->morphToMany(Media::class, 'entity', 'entity_media', 'entity_id', 'file_id')
        ->withPivot('zone')
        ->withTimestamps();
}

public function logo()
{
    return $this->media()->wherePivot('zone', 'logo');
}
```

Note the explicit pivot keys `entity_id, file_id` — the pivot column for the Media side is `file_id`, **not** the Laravel-default `media_id`.

## Project layout

```
laravel12-multivendor-fleetcart/
├── app/
│   ├── Http/
│   │   ├── Controllers/Backend/   # All admin controllers (extend BaseController)
│   │   ├── Middleware/            # PermissionMiddleware, FlasherMiddleware, ...
│   │   └── Requests/              # Form requests
│   ├── Models/                    # Product, Vendor, Order, Translation, Media, ...
│   ├── Providers/                 # BladeServiceProvider (perms directives), ...
│   ├── Services/                  # TranslationService, ...
│   └── Traits/                    # HasPermissions, HasTranslations
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── 2025_09_07_170807_create_laravel_multivendor_table.php   # 80 tables
│   │   └── 2026_05_20_000000_create_stock_management_tables.php     # 14 tables
│   ├── seeders/                   # 96 per-table seeders + DatabaseSeeder
│   └── database.sqlite            # local dev (gitignored)
├── public/
│   └── build/                     # Vite output (gitignored — run `npm run build`)
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── admin/                 # Per-resource Blade dirs (kebab-case for most)
│       ├── auth/                  # Breeze auth views
│       └── layouts/
├── routes/
│   ├── admin.php                  # 618 admin routes (see below)
│   ├── auth.php
│   ├── console.php
│   └── web.php
├── storage/
└── tests/
    ├── Feature/
    └── Unit/
```

## Database

| Migration                                                       | Tables  |
| --------------------------------------------------------------- | ------- |
| `2025_09_07_170807_create_laravel_multivendor_table.php`        | 80      |
| `2026_05_20_000000_create_stock_management_tables.php`          | 14      |
| RBAC + auth (Breeze, sessions, jobs, cache)                     | 9       |
| **Total**                                                       | **103** |

Each business table has its own seeder under `database/seeders/`, kept compact (PHPDoc lists columns, `$rows = []` for owner-provided fixtures, guarded by `if (! empty($rows))`). They're registered in `DatabaseSeeder.php` in FK-safe layers so `php artisan db:seed` runs cleanly on a fresh database.

To reset everything:

```bash
php artisan migrate:fresh --seed --force
```

## Routes

- **618 routes** under the `/admin` prefix, declared in [`routes/admin.php`](routes/admin.php).
- Naming convention is `admin.<resource>.<action>` (e.g. `admin.products.index`, `admin.vendor-orders.show`).
- Static sub-paths (`/abandoned`, `/popular`, `/export`, etc.) are declared **before** parametric show routes so they aren't shadowed by the model binding.

To inspect everything:

```bash
php artisan route:list
php artisan route:list --path=admin/products
```

## Development commands

```bash
# All-in-one dev (server + queue + log tail + Vite)
composer run dev

# Server only
php artisan serve

# Front-end
npm run dev          # Vite dev server (HMR)
npm run build        # production build → public/build/manifest.json

# Tests
composer test
php artisan test --filter=ProfileTest

# DB
php artisan migrate
php artisan migrate:fresh --seed --force
php artisan db:seed

# Caches
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Queue
php artisan queue:listen --tries=1

# Real-time log tail
php artisan pail
```

## Testing

PHPUnit configuration is in `phpunit.xml`. Tests live in `tests/Feature/` (Breeze auth, profile, password) and `tests/Unit/`.

```bash
php artisan test                  # all
php artisan test --coverage       # with coverage
php artisan test --filter=Login   # one
```

The Vite manifest (`public/build/manifest.json`) must exist before any view-rendering test will pass — run `npm install && npm run build` first.

## Code style

Format with **Pint** (Laravel's official PHP CS Fixer wrapper):

```bash
vendor/bin/pint            # auto-fix
vendor/bin/pint --test     # check only (CI mode)
```

CI / pre-merge expectation: `vendor/bin/pint --test` passes on all 325+ files.

## Deployment notes

- **Production database**: set `DB_CONNECTION=mysql` (or `pgsql`) in `.env`. The audited query layer uses driver-aware SQL where needed (e.g. `ReportController::datePart()` picks `HOUR()` / `DAY()` / `MONTH()` on MySQL and `strftime()` on SQLite).
- **Assets**: run `npm install && npm run build` on the deploy machine — `public/build/` is gitignored.
- **Queue**: configure `QUEUE_CONNECTION=database` (or `redis`) and run `php artisan queue:work` under a supervisor.
- **Caches**: after deploy, run `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
- **Storage**: `php artisan storage:link` to expose `storage/app/public/`.
- **Permissions**: ensure `storage/` and `bootstrap/cache/` are writable by the web user.

## Further documentation

| Document                                                          | Topic                                                                |
| ----------------------------------------------------------------- | -------------------------------------------------------------------- |
| [`CLAUDE.md`](CLAUDE.md)                                          | Per-repo conventions, contribution rules, file-structure expectations |
| [`CONTROLLER_PERMISSIONS.md`](CONTROLLER_PERMISSIONS.md)          | Complete resource → permission mapping for every backend controller  |
| [`PERMISSION_USAGE.md`](PERMISSION_USAGE.md)                      | PermissionMiddleware, HasPermissions trait, Blade directives         |
| [`TRANSLATION_SYSTEM_GUIDE.md`](TRANSLATION_SYSTEM_GUIDE.md)      | HasTranslations trait, polymorphic schema, import/export, admin UI   |
| [`CRUD_PATTERN_DOCUMENTATION.md`](CRUD_PATTERN_DOCUMENTATION.md)  | Standard controller + view patterns for new resources                |
| [`BRAND_HASMEDIA_MIGRATION.md`](BRAND_HASMEDIA_MIGRATION.md)      | Migrating brands to the polymorphic media zone model                 |
| [`CATEGORIES_FIX.md`](CATEGORIES_FIX.md)                          | Category tree / jsTree integration notes                             |

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
