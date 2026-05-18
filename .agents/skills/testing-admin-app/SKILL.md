---
name: testing-admin-app
description: Test the Laravel 12 FleetCart admin app end-to-end. Use when verifying migration, model, controller, or admin-UI changes against the multivendor schema.
---

# Testing the FleetCart admin app

## Stack

- Laravel 12, PHP 8.3, Composer.
- Frontend assets via Vite (`npm run build`) — without built assets, `/login` returns HTTP 500 `ViteManifestNotFoundException`. Always build first on a fresh clone.
- Local DB is SQLite at `database/database.sqlite` (`DB_CONNECTION=sqlite` in `.env`).
- Dev server: `php artisan serve --host 127.0.0.1 --port 8000`.

## One-time setup on a fresh clone

```bash
cp -n .env.example .env
mkdir -p database && touch database/database.sqlite
composer install --no-interaction --prefer-dist
php artisan key:generate --force
npm install && npm run build
php artisan migrate:fresh --seed --force
```

The blueprint should already do this in future sessions; if it didn't, run the above manually.

## Seeded super-admin credentials

- Email: `superadmin@gmail.com`
- Password: `12345678`
- Created by `database/seeders/UserTableSeeder.php`; tied to the `Super Admin` role via `RoleUserTableSeeder.php`.
- Note: the seeder does not set a `status`, so the edit page shows the user as `Inactive`. Login still works (the auth check does not look at this field). If you need an Active super-admin, edit the seeder before re-seeding, or `UPDATE users SET status = 1 WHERE id = 1;` directly.

## What to test for migration / schema changes

The spec migration lives at `database/migrations/2025_09_07_170807_create_laravel_multivendor_table.php`. The most reliable signals after any change to it:

1. `php artisan migrate:fresh --seed --force` — must complete cleanly. Any `SQLSTATE`, `duplicate column`, or `Method ... does not exist` is a regression.
2. `php artisan migrate:rollback --force` followed by another `migrate:fresh --seed --force` — catches mismatches between `up()` and `down()` (the most common bug is dropping a pivot under the wrong name and then having the next `up()` fail with "table X already exists").
3. SQLite schema PRAGMAs against the affected tables. Concrete examples:
   - `PRAGMA table_info(permission_user);` — should return exactly four rows (`user_id`, `permission_id`, `created_at`, `updated_at`), no duplicate timestamp columns.
   - `PRAGMA foreign_key_list(media);` — should show the `user_id -> users.id` FK with `SET NULL` on delete.
   - `SELECT name FROM sqlite_master WHERE type='table' AND name IN ('role_user','user_roles');` — the Laravel pivot convention is `role_user` (singular alphabetical); `user_roles` is wrong.

## Admin UI smoke (browser)

After login, hit these four pages — they each touch a different piece of the schema:

| URL | Exercises |
|---|---|
| `/admin/users` | `users` table + index/show paths |
| `/admin/roles` | `roles` + `role_user` pivot relation (Status / counts) |
| `/admin/permissions` | `permissions` + `permission_user` / `permission_role` pivots |
| `/admin/media` | `media` table including the `user_id` FK |

All four should return HTTP 200 and render a list/empty-state component, not a Whoops/Ignition error page.

For a CRUD smoke, open `/admin/users/1/edit` — must load with the seeded fields populated.

## Common gotchas

- **`ViteManifestNotFoundException` on `/login`** — run `npm install && npm run build` once. `public/build/` is gitignored.
- **`No such file or directory: .../database.sqlite`** — `touch database/database.sqlite` and re-run migrations.
- **`SQLSTATE[HY000]: General error: 1 no such table: sessions`** — the session driver default in this repo is `database`. Either run `migrate` or set `SESSION_DRIVER=file` in `.env`.
- **`migrate:fresh` failing with `duplicate column name: created_at`** — symptom of a duplicate `$table->timestamps()` in a pivot definition; check the migration file.
- **`Method Illuminate\Database\Schema\Blueprint::constrained does not exist`** — only `foreignId()` supports `->constrained()`. On a plain `unsignedBigInteger()` you must use the explicit `$table->foreign('col')->references('id')->on('table')->...` form.

## Devin Secrets Needed

- None for local testing — the seeded super-admin credentials above are local-dev only and live in `UserTableSeeder.php`.

## Out of scope for this skill

- Storefront / vendor-portal flows (those touch separate routes and controllers).
- MySQL / Postgres regressions for `->nullOnDelete()` semantics — verify against the production driver in a separate environment if the project switches off SQLite.
