---
name: testing-auth-profile
description: Test register / login / profile / password flows in this multivendor Laravel 12 app. Use whenever you change the User model, ProfileUpdateRequest, AuthenticatedSessionController, RegisteredUserController, PasswordController, or any of the auth blade views.
---

# Testing auth + profile flows

This project is Laravel 12 + Breeze, but the `users` schema has been customized away from Breeze defaults. If you touch anything in `app/Http/Controllers/Auth/`, `app/Http/Requests/ProfileUpdateRequest.php`, `app/Models/User.php`, or `resources/views/auth|profile/`, run through this skill before merging.

## Schema gotcha (the bug that took down all tests at one point)

The `users` table uses `first_name`, `last_name`, `username` (nullable unique), `phone_no` (nullable), NOT Breeze's default `name`. See <ref_file file="database/migrations/0001_01_01_000000_create_users_table.php" />.

If you bring in any Breeze-style code (factory, controller, view, test) that still references `name`, every Feature test will explode with `table users has no column named name`. Always check:

- `database/factories/UserFactory.php` uses `first_name`/`last_name`/`username`/`phone_no`/`is_verified`/`status`
- `resources/views/auth/register.blade.php` and `resources/views/profile/partials/update-profile-information-form.blade.php` reference `$user->first_name` + `$user->last_name`, NOT `$user->name`
- `app/Http/Requests/ProfileUpdateRequest.php` validates `first_name` + `last_name`

## Password hashing — watch for double-hash

`User::casts()` declares `'password' => 'hashed'`. That cast already hashes on assign. Do NOT add a `setPasswordAttribute($value)` mutator that calls `bcrypt($value)` — that's a double-hash and breaks every login-after-password-change flow.

Verify a stored hash is single-bcrypt with tinker:

```php
$u = \DB::table('users')->where('email', 'someone@example.com')->first();
echo substr($u->password, 0, 4);          // expect $2y$
echo strlen($u->password);                 // expect 60
echo \Hash::check('plaintext', $u->password) ? 'ok' : 'fail';
```

A double-hashed value is still `$2y$` length 60 — the only way to detect it is `Hash::check` returning `false` for the plaintext you know you set.

## Seeded users

`UserTableSeeder` creates exactly one user:

- `superadmin@gmail.com` / `12345678` — has the admin role (assigned by `RoleUserTableSeeder`)

Use this account when you need to test anything inside `/admin/*`. The password was inserted via `User::insert()` (which bypasses model events/casts) using `bcrypt('12345678')`, so the stored value is a single-bcrypt hash.

## Routing quirks that look like bugs but aren't

1. **Login goes to `/admin`, register goes to `/dashboard`.**
   - `AuthenticatedSessionController::store` → `redirect()->intended(route('admin.dashboard'))` → `/admin`
   - `RegisteredUserController::store` → `redirect(route('dashboard'))` → `/dashboard`
   - If you're aligning tests, the assertion for login is `assertRedirect(route('admin.dashboard'))`.

2. **A newly-registered user can log in but bounces from `/admin` → `/dashboard` with a flashed "You do not have permission" toast.**
   - This is the admin permission middleware on `/admin`. Registration doesn't auto-assign any role. The login still *intends* `/admin` (302 Location header proves it).
   - When asserting login behavior in tests, use either the freshly-seeded `superadmin` (lands on `/admin` cleanly) OR factory a user with the admin role.

3. **`/profile`, `/password`, `/logout` are accessible to any authenticated user**, regardless of role. They're scoped to `auth` middleware in `routes/web.php` + `routes/auth.php`, not `admin`.

## Quick test recipes

### Run the full PHPUnit suite
```bash
php artisan test
```
Should be 25/25 passing on `main`. If anything in Feature/Auth/ or Feature/Profile fails, suspect the schema gotcha above.

### Reset DB to a known state
```bash
php artisan migrate:fresh --seed --force
```
This re-seeds the superadmin user. Safe to run between test iterations.

### Drive the UI flow end-to-end
```bash
php artisan serve --host=127.0.0.1 --port=8000 &
google-chrome http://127.0.0.1:8000/register
```
Then click through: register → /dashboard → /profile → update profile → /password → log out → log in. The DB-level assertion to add at each step is documented in the test plan template (see `/tmp/test-plan.md` in past Devin sessions).

### Login redirect smoke test via curl (no browser needed)
```bash
TOKEN=$(curl -s -c /tmp/c.txt http://127.0.0.1:8000/login | grep -oP 'name="_token" value="\K[^"]+' | head -1)
curl -s -i -X POST http://127.0.0.1:8000/login \
  -c /tmp/c.txt -b /tmp/c.txt \
  --data-urlencode "_token=$TOKEN" \
  --data-urlencode "email=superadmin@gmail.com" \
  --data-urlencode "password=12345678" | grep -iE "^HTTP|^Location"
# Expected:
# HTTP/1.1 302 Found
# Location: http://127.0.0.1:8000/admin
```

### Lint + style
```bash
./vendor/bin/pint --test       # check
./vendor/bin/pint              # auto-fix
```
Known gotcha: PHP-CS-Fixer can't parse comments inside grouped `use {}` statements (`use Foo\{ /* comment */ A, B }`). If Pint suddenly fails to parse a file that PHP itself accepts (`php -l` is clean), look for inline comments inside grouped `use` first.

### Security audits
```bash
composer audit          # expect: "No security vulnerability advisories found."
npm audit               # expect: "found 0 vulnerabilities"
```
When these surface advisories, prefer `composer update <package> --with-all-dependencies` and `npm audit fix` over manually pinning versions — Laravel/Symfony point releases stay in `^` range.

## Devin Secrets Needed

None for local testing. All flows can be exercised against the seeded sqlite DB without any external credentials.
