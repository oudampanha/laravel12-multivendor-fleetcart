# Developer Tutorial — From Zero to a Live Marketplace

A step-by-step walkthrough that takes a brand-new developer from cloning the repo to a production-ready, end-user-accessible multi-vendor marketplace.

Audience: backend / full-stack developers comfortable with PHP, the command line, and basic Linux server administration. No prior Laravel experience required, but it helps.

> **Tip — TL;DR setup**
> If you just want the fastest possible local run, jump straight to [§3 Quick start](#3-quick-start). The rest of this document expands every step in detail and adds the admin walkthrough, deployment, and troubleshooting.

---

## Table of contents

1. [Prerequisites](#1-prerequisites)
2. [Clone the repository](#2-clone-the-repository)
3. [Quick start](#3-quick-start)
4. [Configure the environment file](#4-configure-the-environment-file)
5. [Run database migrations and seeders](#5-run-database-migrations-and-seeders)
6. [Build front-end assets](#6-build-front-end-assets)
7. [Start the dev server and sign in](#7-start-the-dev-server-and-sign-in)
8. [Admin walkthrough — turn it into a working store](#8-admin-walkthrough--turn-it-into-a-working-store)
   1. [Roles and permissions](#81-roles-and-permissions)
   2. [Taxes and shipping](#82-taxes-and-shipping)
   3. [Brands and categories](#83-brands-and-categories)
   4. [Vendors](#84-vendors)
   5. [Products](#85-products)
   6. [Orders](#86-orders)
   7. [Payouts and withdrawals](#87-payouts-and-withdrawals)
   8. [Translations](#88-translations)
   9. [Media library](#89-media-library)
   10. [Stock management](#810-stock-management)
   11. [System settings](#811-system-settings)
9. [Going to production](#9-going-to-production)
10. [Day-2 operations](#10-day-2-operations)
11. [Troubleshooting cheatsheet](#11-troubleshooting-cheatsheet)
12. [Where to go next](#12-where-to-go-next)

---

## 1. Prerequisites

Install these once on your dev machine.

| Tool      | Version       | Install                                                                                                                                                                                                       |
| --------- | ------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Git       | any           | <https://git-scm.com/downloads>                                                                                                                                                                               |
| PHP       | **8.3**       | Ubuntu: `sudo add-apt-repository ppa:ondrej/php && sudo apt install php8.3 php8.3-cli php8.3-curl php8.3-mbstring php8.3-xml php8.3-zip php8.3-sqlite3 php8.3-mysql php8.3-bcmath php8.3-gd php8.3-intl`       |
| Composer  | 2.x           | `curl -sS https://getcomposer.org/installer \| php -- --install-dir=/usr/local/bin --filename=composer && sudo chmod +x /usr/local/bin/composer`                                                              |
| Node.js   | **20 or 22**  | <https://nodejs.org/> (or use `nvm install 20`)                                                                                                                                                               |
| SQLite    | bundled       | Comes with `php8.3-sqlite3`. Used by default for dev.                                                                                                                                                          |
| MySQL    | **8.0+** (prod) | <https://dev.mysql.com/downloads/> (or use MariaDB 10.6+)                                                                                                                                                     |

**Verify everything:**

```bash
php --version          # PHP 8.3.x
composer --version     # Composer 2.x
node --version         # v20.x or v22.x
npm --version          # 10.x
git --version
```

If `php artisan` later complains about a missing extension, install the corresponding `php8.3-<name>` package and re-run.

---

## 2. Clone the repository

```bash
git clone https://github.com/oudampanha/laravel12-multivendor-fleetcart.git
cd laravel12-multivendor-fleetcart
```

You should see the project root with `app/`, `database/`, `routes/`, `resources/`, `composer.json`, `package.json`, and the documentation files (`README.md`, `CLAUDE.md`, this file, etc.).

---

## 3. Quick start

For developers who just want the app running locally on SQLite, this is the entire flow:

```bash
# Install PHP + JS dependencies
composer install
npm install

# Bootstrap env + key
cp .env.example .env
php artisan key:generate

# Create the SQLite file
mkdir -p database && touch database/database.sqlite

# Run all 103 migrations + all 96 seeders
php artisan migrate:fresh --seed --force

# Build front-end assets (required — the @vite directive needs the manifest)
npm run build

# Start the dev server
php artisan serve --host 127.0.0.1 --port 8000
```

Open <http://127.0.0.1:8000/admin> and sign in with:

| Field    | Value                  |
| -------- | ---------------------- |
| Email    | `superadmin@gmail.com` |
| Password | `12345678`             |

Done. Everything below is detail — keep reading to understand *what* you just did and *how* to take it further.

---

## 4. Configure the environment file

The `.env` file controls everything from database connection to mail credentials. The repo ships an `.env.example` you copied in step 3.

### 4.1 SQLite (dev default)

Open `.env` and confirm:

```env
APP_NAME="Multivendor FleetCart"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
# DB_DATABASE may be omitted with SQLite — Laravel resolves to database/database.sqlite
```

Make sure the SQLite file exists:

```bash
mkdir -p database
touch database/database.sqlite
```

### 4.2 MySQL (recommended for staging/production)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multivendor_fleetcart
DB_USERNAME=fleetcart
DB_PASSWORD=<a-strong-password>
```

Create the database and user once:

```sql
CREATE DATABASE multivendor_fleetcart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'fleetcart'@'localhost' IDENTIFIED BY 'a-strong-password';
GRANT ALL PRIVILEGES ON multivendor_fleetcart.* TO 'fleetcart'@'localhost';
FLUSH PRIVILEGES;
```

### 4.3 Mail (for notifications, password resets, vendor approvals)

Use Mailpit for local dev:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

For production use SES, Mailgun, Postmark, or a real SMTP server.

### 4.4 Queue, cache, session

For local dev, the defaults (`QUEUE_CONNECTION=database`, `CACHE_STORE=database`, `SESSION_DRIVER=database`) work out of the box. For production switch to Redis:

```env
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 4.5 Generate the application key

```bash
php artisan key:generate
```

This populates `APP_KEY` in `.env`. Required — Laravel will refuse to boot without it.

---

## 5. Run database migrations and seeders

The schema is split across two consolidated migrations:

- `2025_09_07_170807_create_laravel_multivendor_table.php` — 80 tables (vendors, products, orders, payouts, taxes, settings, blog, CMS, …)
- `2026_05_20_000000_create_stock_management_tables.php` — 14 tables (warehouses, purchase orders, goods receipts, stock takes, transfers, adjustments, …)

Plus 9 framework tables (sessions, jobs, cache, Breeze auth scaffolding).

Run them all:

```bash
php artisan migrate:fresh --seed --force
```

The `--seed` flag chains in `DatabaseSeeder`, which calls 96 per-table seeders in FK-safe layers:

```
RoleTableSeeder → PermissionTableSeeder → PermissionRoleTableSeeder
→ UserTableSeeder → RoleUserTableSeeder
→ (lookup tables: brands, tax classes, warehouses, suppliers, ...)
→ (everything else, registered in DatabaseSeeder.php)
```

After it finishes you should see one row per seed in the output and zero errors. The result on a fresh DB:

| Item             | Rows |
| ---------------- | ---- |
| Roles            | 10   |
| Permissions      | 432  |
| Users            | 1    |
| Brands (sample)  | 1    |
| Tax classes      | 1    |
| Warehouses       | 1    |
| (everything else)| 0    |

To re-seed only one table:

```bash
php artisan db:seed --class=BrandsTableSeeder
```

---

## 6. Build front-end assets

The admin Blade views use the `@vite` directive, which requires `public/build/manifest.json`. Without this file, every view-rendering route 500s with `ViteManifestNotFoundException`.

```bash
npm install        # once
npm run build      # produces public/build/manifest.json + hashed JS/CSS
```

For active front-end development:

```bash
npm run dev        # Vite dev server with HMR
```

Leave `npm run dev` running in a second terminal while editing Tailwind classes / Alpine components — pages will hot-reload.

---

## 7. Start the dev server and sign in

In a separate terminal:

```bash
php artisan serve --host 127.0.0.1 --port 8000
```

Or run server + queue worker + log tail + Vite all at once via the project script:

```bash
composer run dev
```

Open <http://127.0.0.1:8000/admin>, sign in with `superadmin@gmail.com` / `12345678`, and you'll land on the admin dashboard.

---

## 8. Admin walkthrough — turn it into a working store

A fresh seeded database has a super-admin and not much else. This section walks you through populating the marketplace in the order most stores need.

### 8.1 Roles and permissions

URL: <http://127.0.0.1:8000/admin/roles>

Out of the box you have 10 seeded roles (super_admin, admin, vendor_owner, vendor_staff, customer, …) and 432 permissions, each named `{resource}_{action}` (e.g. `product_access`, `product_create`, `vendor_management_access`).

- **Create a new role** — Roles → Add new role → name it, then tick the permissions you want.
- **Assign roles to users** — Users → pick a user → Edit → multi-select roles.
- **Permission groups in views** — Use the Blade directives `@permission(...)` and `@cancrud(...)` to gate UI elements. See [`PERMISSION_USAGE.md`](PERMISSION_USAGE.md).

Architecturally, every backend controller extends `BaseController`, which auto-maps CRUD methods to `{resource}_{action}` permissions. Adding a new resource means: extend `BaseController`, set `$resource`, and the perms are enforced automatically. Details in [`CONTROLLER_PERMISSIONS.md`](CONTROLLER_PERMISSIONS.md).

### 8.2 Taxes and shipping

URLs:

- Tax classes: <http://127.0.0.1:8000/admin/tax-classes>
- Tax rates: <http://127.0.0.1:8000/admin/tax-rates>
- Shipping settings: <http://127.0.0.1:8000/admin/settings/shipping>

Create the tax classes your jurisdiction needs (e.g. "Standard 10%", "Reduced 5%", "Zero-rated"), then add rates per region (country / state / zip range). Products reference a `tax_class_id`; the rate at checkout is resolved against the customer's shipping address.

For shipping, set the available shipping methods and per-zone fees under Settings → Shipping.

### 8.3 Brands and categories

URLs:

- Brands: <http://127.0.0.1:8000/admin/brands>
- Categories: <http://127.0.0.1:8000/admin/categories>

**Brands** are flat — just a name, slug, optional logo, and status. The brand index supports search, status toggle, and per-brand product listings (`/admin/brands/{brand}/products`).

**Categories** are hierarchical via a jsTree UI. Drag and drop to reorder; click a node to edit. The tree is exposed at `/admin/categories/tree` for use as a picker in product forms. See [`CATEGORIES_FIX.md`](CATEGORIES_FIX.md) for the relationship/integration details.

### 8.4 Vendors

URL: <http://127.0.0.1:8000/admin/vendors>

A vendor is a user (`users` row) plus a `vendors` row that holds business details, commission rate, and approval status (`pending` → `approved` / `rejected`).

Typical flow:

1. A customer registers (`/register`) and applies to become a vendor — they land in `vendors` with `status=pending`.
2. Admin reviews at `/admin/vendors`, opens the vendor, sets the commission rate, and clicks **Approve**.
3. Vendor receives an email notification.
4. Vendor signs back in — their dashboard role grants product/order access.

### 8.5 Products

URL: <http://127.0.0.1:8000/admin/products>

Products belong to a vendor and carry both a global `status` (active/inactive) and a per-vendor `vendor_status` (pending/approved/rejected). Admin approval is required before a vendor's product appears on the storefront.

**Create flow:**

1. Products → Add new product.
2. Tab 1 — basic info: name, slug, vendor, brand, category, price.
3. Tab 2 — media: upload images via the media selector (uses the `entity_media` pivot with `zone` = `images` / `featured_image`).
4. Tab 3 — variations: define option sets (size, color, …) and per-variant SKU/price/stock.
5. Tab 4 — attributes: free-form `attribute_name` → `attribute_value` rows.
6. Tab 5 — SEO / meta data.
7. Save → vendor sees `vendor_status=approved` (when admin-created) or `pending` (when vendor-created).

For new variant/option behavior reference see [`CRUD_PATTERN_DOCUMENTATION.md`](CRUD_PATTERN_DOCUMENTATION.md). For media zones see [`BRAND_HASMEDIA_MIGRATION.md`](BRAND_HASMEDIA_MIGRATION.md).

### 8.6 Orders

URL: <http://127.0.0.1:8000/admin/orders>

Customer orders are stored in `orders`. For commission tracking and per-vendor fulfilment, each line item is duplicated into `vendor_orders` rows (one per vendor involved in the order).

Available admin actions:

- View order detail at `/admin/orders/{order}`.
- Update status (`pending` → `processing` → `shipped` → `completed`, or `cancelled` / `refunded`).
- Bulk status update from the index page.
- Filter by payment method, customer, vendor, date range.
- Reports: `/admin/reports/sales` (daily / monthly / yearly), `/admin/reports/orders/abandoned-carts`.

### 8.7 Payouts and withdrawals

URLs:

- Vendor payouts: <http://127.0.0.1:8000/admin/vendor-payouts>
- Vendor withdrawals: <http://127.0.0.1:8000/admin/vendor-withdrawals>

**Earning model:**

- Each `vendor_orders` row records the gross line total and the platform's commission (using the vendor's commission rate at order time).
- The vendor's net balance = sum(vendor_orders.net) - sum(vendor_payouts.amount).
- **Payouts** are admin-initiated: open the vendor → Payouts → New payout → enter amount → choose method → record.
- **Withdrawals** are vendor-initiated: the vendor requests an amount from their dashboard, which lands as a `pending` row in `vendor_withdrawals`. Admin reviews and approves or rejects.

### 8.8 Translations

URLs:

- Translation Management (recommended): <http://127.0.0.1:8000/admin/translation-management>
- Translations: <http://127.0.0.1:8000/admin/translations>
- Language lines: <http://127.0.0.1:8000/admin/language-lines>

Every model that uses the `HasTranslations` trait (Product, Category, Brand, BlogPost, …) can carry a translated value per `(field, locale)` pair, stored polymorphically in the `translations` table:

```php
$product->setTranslation('name', 'Mango', 'en');
$product->setTranslation('name', 'ស្វាយ', 'km');
echo $product->getTranslation('name', 'km'); // ស្វាយ
```

The admin UI lets you:

- Browse translations by locale or by model.
- Import/export `.json` per locale.
- Find missing translations (`/admin/translation-management/missing`) for any model.
- Duplicate one locale's set as a starting point for a new locale.

Full architecture and API in [`TRANSLATION_SYSTEM_GUIDE.md`](TRANSLATION_SYSTEM_GUIDE.md).

### 8.9 Media library

URL: <http://127.0.0.1:8000/admin/media>

Centralised image / file library. Every uploaded file lives in the `media` table; owners attach files polymorphically through `entity_media` with a `zone` discriminator:

| Owner       | Zone             | Use                          |
| ----------- | ---------------- | ---------------------------- |
| Product     | `images`         | Gallery thumbnails           |
| Product     | `featured_image` | Listing card / OG image      |
| Brand       | `logo`           | Brand logo                   |
| Category    | `banner`         | Category page hero           |
| Vendor      | `cover`          | Vendor storefront banner     |
| BlogPost    | `featured_image` | Blog index thumbnails        |

Uploads go to `storage/app/public/`. Run `php artisan storage:link` once so the files are accessible at `/storage/...`. The `x-media-selector` Blade component is the standard picker used by every CRUD form.

### 8.10 Stock management

URLs:

- Product stocks: <http://127.0.0.1:8000/admin/product-stocks>
- Warehouses: <http://127.0.0.1:8000/admin/warehouses>
- Suppliers: <http://127.0.0.1:8000/admin/suppliers>
- Purchase orders: <http://127.0.0.1:8000/admin/purchase-orders>
- Goods receipts: <http://127.0.0.1:8000/admin/goods-receipts>
- Stock takes: <http://127.0.0.1:8000/admin/stock-takes>
- Stock transfers: <http://127.0.0.1:8000/admin/stock-transfers>
- Stock adjustments: <http://127.0.0.1:8000/admin/stock-adjustments>
- Stock movements (audit log): <http://127.0.0.1:8000/admin/stock-movements>

Flow:

1. Create one or more **warehouses**.
2. Create **suppliers**.
3. Issue a **purchase order** (PO) to a supplier.
4. When the goods arrive, log a **goods receipt** against the PO → quantities flow into `product_stocks` at the receiving warehouse.
5. **Stock takes** reconcile physical counts against system counts.
6. **Stock transfers** move stock between warehouses; **adjustments** correct for shrinkage, damage, etc.
7. Every change writes to **`stock_movements`** as an immutable audit log.

### 8.11 System settings

URL: <http://127.0.0.1:8000/admin/settings>

Subsections (each is its own page under `/admin/settings/<section>`):

| Section    | Purpose                                                  |
| ---------- | -------------------------------------------------------- |
| General    | Site name, default locale, default currency, time zone   |
| Mail       | SMTP credentials and "from" address                      |
| Payment    | Stripe, PayPal, COD, bank transfer toggles + credentials |
| Shipping   | Available shipping methods and zone-based rates          |
| Tax        | Default tax class, prices include/exclude tax            |
| SEO        | Site title template, default OG image, robots.txt rules  |
| Social     | Facebook / X / Instagram / YouTube URLs                  |
| Analytics  | Google Analytics / GTM IDs                               |

After changing settings, run `php artisan optimize:clear` if you've cached config in production.

---

## 9. Going to production

### 9.1 Server requirements

| Component        | Minimum                                                            |
| ---------------- | ------------------------------------------------------------------ |
| OS               | Ubuntu 22.04 LTS (or any modern Linux)                             |
| PHP              | 8.3 with extensions: `mbstring`, `xml`, `curl`, `zip`, `mysql`, `bcmath`, `gd`, `intl`, `redis` |
| Database         | MySQL 8.0+ or MariaDB 10.6+                                        |
| Web server       | Nginx (recommended) or Apache                                      |
| Process manager  | PHP-FPM 8.3                                                        |
| Cache / queue    | Redis 7.x                                                          |
| Node             | 20.x (only needed at deploy time for `npm run build`)              |
| Supervisor       | for keeping the queue worker alive                                 |
| Disk             | at least 5 GB for media storage; mount `storage/app/public/` separately if you expect heavy upload volume |

### 9.2 Deploy steps

```bash
# 1. Clone the production branch
git clone -b main https://github.com/oudampanha/laravel12-multivendor-fleetcart.git /var/www/fleetcart
cd /var/www/fleetcart

# 2. Install production dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 3. Configure .env (set APP_ENV=production, APP_DEBUG=false, real DB/Redis/SMTP)
cp .env.example .env
php artisan key:generate
# ...edit .env...

# 4. Run migrations (without --fresh in prod!)
php artisan migrate --force
php artisan db:seed --class=PermissionTableSeeder --force   # only the perms catalog
# Do NOT run --seed on prod after the first deploy — it would re-insert sample rows.

# 5. Storage link
php artisan storage:link

# 6. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. File permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 9.3 Nginx site config

Save as `/etc/nginx/sites-available/fleetcart.conf`:

```nginx
server {
    listen 80;
    server_name shop.example.com;
    root /var/www/fleetcart/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable, reload, then add HTTPS via certbot:

```bash
sudo ln -s /etc/nginx/sites-available/fleetcart.conf /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d shop.example.com
```

### 9.4 Queue worker (Supervisor)

Save as `/etc/supervisor/conf.d/fleetcart-worker.conf`:

```ini
[program:fleetcart-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/fleetcart/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/fleetcart/storage/logs/worker.log
stopwaitsecs=3600
```

Then:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start fleetcart-worker:*
```

### 9.5 Scheduled tasks (cron)

Add one line to the web user's crontab:

```bash
sudo -u www-data crontab -e
```

```cron
* * * * * cd /var/www/fleetcart && php artisan schedule:run >> /dev/null 2>&1
```

This drives any tasks registered in `routes/console.php` (cleanups, recurring reports, abandoned cart reminders, etc.).

### 9.6 Final checklist before going live

- [ ] `APP_DEBUG=false` and `APP_ENV=production` in `.env`
- [ ] `APP_URL` matches the public domain (including `https://`)
- [ ] Real DB credentials (not the seeded password)
- [ ] Real SMTP credentials
- [ ] Payment gateway credentials configured at `/admin/settings/payment`
- [ ] Shipping methods and rates configured at `/admin/settings/shipping`
- [ ] Default tax class set at `/admin/settings/tax`
- [ ] Super-admin password changed from `12345678` to something strong (Profile → Edit)
- [ ] `php artisan storage:link` run
- [ ] `php artisan config:cache && php artisan route:cache && php artisan view:cache` run
- [ ] Queue worker running under Supervisor (`sudo supervisorctl status`)
- [ ] Cron entry installed (`sudo -u www-data crontab -l`)
- [ ] HTTPS certificate installed and auto-renewing
- [ ] Backup strategy in place for the database AND `storage/app/public/`

When all boxes are ticked, your end users can register at `https://shop.example.com/register`, browse the storefront, place orders, and pay through the configured gateways.

---

## 10. Day-2 operations

### 10.1 Deploying updates

```bash
cd /var/www/fleetcart
git pull origin main
composer install --no-dev --optimize-autoloader
npm install
npm run build

php artisan down --message="Upgrading"   # optional maintenance mode
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan queue:restart                 # tell workers to pick up new code
php artisan up
```

### 10.2 Watching logs

```bash
# Production: standard Laravel log
tail -f storage/logs/laravel.log

# Local dev: nicer real-time tail
php artisan pail
```

### 10.3 Database backup

```bash
mysqldump -u fleetcart -p multivendor_fleetcart | gzip > backup-$(date +%Y%m%d).sql.gz
```

Restore:

```bash
gunzip -c backup-YYYYMMDD.sql.gz | mysql -u fleetcart -p multivendor_fleetcart
```

For SQLite (dev):

```bash
cp database/database.sqlite backup-$(date +%Y%m%d).sqlite
```

### 10.4 Clearing caches when things go weird

```bash
php artisan optimize:clear   # config + route + view + event in one shot
```

In dev you can also drop caches individually: `cache:clear`, `view:clear`, `route:clear`, `config:clear`.

### 10.5 Running the test suite

```bash
php artisan test                  # all 30 PHPUnit tests
php artisan test --coverage       # with coverage report
vendor/bin/pint                   # auto-fix code style
vendor/bin/pint --test            # CI-mode style check (must pass before merge)
```

---

## 11. Troubleshooting cheatsheet

| Symptom                                                              | Cause                                                        | Fix                                                                                  |
| -------------------------------------------------------------------- | ------------------------------------------------------------ | ------------------------------------------------------------------------------------ |
| `ViteManifestNotFoundException`                                      | Front-end assets not built                                   | `npm install && npm run build`                                                       |
| `View [admin.foo_bar.index] not found`                               | Snake-case path on a kebab-case dir                          | Use `view('admin.foo-bar.index')`. PR #17 fixed all known cases; see the audit notes |
| `SQLSTATE[HY000]: General error: 1 a GROUP BY clause is required ...`| SQLite reject of `HAVING` without `GROUP BY`                 | Add `->groupBy('id')` before `->having(...)`. PR #17 fixed all 6 known cases         |
| `SQLSTATE[HY000]: no such column: entity_media.media_id`             | morphToMany using default pivot keys                         | Specify `morphToMany(Media::class, 'entity', 'entity_media', 'entity_id', 'file_id')` |
| `UrlGenerationException: Missing required parameters for [Route: …]` | Blade form action references a parametric route incorrectly  | Change `route('foo.store')` to the correct named route, or pass the params           |
| `permission_denied` after login                                      | User has no role, or role missing permission                 | `/admin/users/{id}/edit` → assign a role; `/admin/roles/{id}/edit` → tick perms      |
| 419 PAGE EXPIRED on form submit                                      | Stale CSRF token / session                                   | Sign out and back in; in dev `php artisan session:flush`                             |
| `Class "Pusher\\..." not found`                                      | Broadcasting driver mismatch                                 | Set `BROADCAST_CONNECTION=log` if you don't use websockets, or install pusher-php-server |
| Storage upload returns 500                                           | `storage/app/public/` not writable                           | `sudo chown -R www-data:www-data storage && sudo chmod -R 775 storage`               |
| `/storage/...` URLs 404 in browser                                   | Missing storage symlink                                      | `php artisan storage:link`                                                           |
| Queue jobs never run                                                 | Worker not running                                           | `sudo supervisorctl status` and `start fleetcart-worker:*`                           |
| New code not picked up by queue worker                               | Worker still has old code in memory                          | `php artisan queue:restart`                                                          |
| Reports page shows wrong totals after upgrade                        | Cached config / route                                        | `php artisan optimize:clear` then re-cache                                           |

If you hit something not on this list, check `storage/logs/laravel.log` for the stack trace — that's almost always faster than guessing.

---

## 12. Where to go next

- [`README.md`](README.md) — high-level project overview and quick reference
- [`CLAUDE.md`](CLAUDE.md) — coding conventions, file structure rules, contribution guidelines
- [`CONTROLLER_PERMISSIONS.md`](CONTROLLER_PERMISSIONS.md) — full permission catalogue per controller
- [`PERMISSION_USAGE.md`](PERMISSION_USAGE.md) — how to use the permission system in your own controllers and Blade views
- [`TRANSLATION_SYSTEM_GUIDE.md`](TRANSLATION_SYSTEM_GUIDE.md) — multilingual content end to end
- [`CRUD_PATTERN_DOCUMENTATION.md`](CRUD_PATTERN_DOCUMENTATION.md) — standard pattern for adding a new resource
- [`BRAND_HASMEDIA_MIGRATION.md`](BRAND_HASMEDIA_MIGRATION.md) — how an existing model was migrated to the polymorphic media zone pattern
- [`CATEGORIES_FIX.md`](CATEGORIES_FIX.md) — category tree implementation notes

**Official docs:**

- Laravel 12 — <https://laravel.com/docs/12.x>
- Vite — <https://vitejs.dev/>
- Tailwind CSS — <https://tailwindcss.com/docs>
- Alpine.js — <https://alpinejs.dev/>

Welcome aboard.
