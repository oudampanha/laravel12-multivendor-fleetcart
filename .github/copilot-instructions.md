# Copilot Instructions — Laravel 12 Multi-Vendor Platform (FleetCart)

Purpose: Provide concise, actionable guidance so an AI coding agent can be immediately productive in this codebase.

Quick Start (most common commands)
```bash
# Unified dev (server + queue + logs + vite)
composer run dev

# Individual services
php artisan serve
npm run dev
php artisan queue:listen --tries=1
php artisan pail --timeout=0   # project-specific real-time logs helper

# DB / reset
php artisan migrate:fresh --seed
php artisan optimize:clear

# Tests & formatting
composer test
./vendor/bin/pint
```

Key patterns (read these files first)
- BaseController: `app/Http/Controllers/Backend/BaseController.php` — centralizes auth and CRUD permission mapping.
- Permission middleware & trait: `app/Http/Middleware/PermissionMiddleware.php` and `app/Traits/HasPermissions.php` — permission model used by controllers and blade directives.
- Translations: `app/Traits/HasTranslations.php` and `translations` table — translations are polymorphic and stored per field+locale.
- Vendor isolation: always filter by `vendor_id` when operating in vendor contexts (see `app/Models/Product.php`, `vendor_orders` logic).

Developer workflows & conventions
- Controllers serving admin UI typically return JSON for AJAX; index/list endpoints are server-side DataTables.
  Example: index views in `resources/views/admin/*/index.blade.php` use AJAX to `routes/admin.php` routes.
- Image/media handling: use `ImageUploadTrait` and the `x-media-selector` component pattern (look under `resources/views` and `app/Traits`).
- Asset helper: use `assetUrl()` for backend assets (consistent asset path across views).

Important invariants & pitfalls
- Vendor safety: never omit `vendor_id` filter in vendor-scoped queries — it causes data leakage across vendors.
- Product visibility: check `vendor_status` (pending/approved/rejected) before showing products to customers.
- Permissions: backend routes require `auth` + `dashboard_access`; controllers rely on resource-specific `{resource}_access/create/edit/delete` permissions.

Where to look for examples
- CRUD controller template: `app/Http/Controllers/Backend/*Controller.php` (extends BaseController).
- Routes: `routes/admin.php` (admin route grouping and middleware).
- Tests: `tests/Feature/` and `tests/Unit/` (run with `composer test`).
- Docs: `CRUD_PATTERN_DOCUMENTATION.md`, `CONTROLLER_PERMISSIONS.md`, `TRANSLATION_SYSTEM_GUIDE.md`, `PERMISSION_USAGE.md` in repo root.

How to propose changes (agent behavior)
- Keep edits minimal and follow existing style. Update only relevant files.
- Run `composer test` and `./vendor/bin/pint` before opening a PR.
- When adding a new resource, follow the documented CRUD pattern: migration → model → controller (extend `BaseController`) → views → routes → permissions seeding.

If stuck: point to these entry points in your response so the human reviewer can validate your change quickly.

Feedback: If any critical behaviours or tooling are missing from this file, tell me which area you want expanded (dev commands, testing, or security patterns).
# Copilot Instructions - Laravel 12 Multi-Vendor E-Commerce Platform

## Project Overview
Laravel 12 multi-vendor marketplace (FleetCart-inspired) with vendor management, product catalog, order processing, and commission-based payouts. Uses Laravel Breeze authentication, Tailwind CSS, Alpine.js, and Vite.

## Development Workflow

### Start Development
```bash
# Unified development server (server + queue + logs + vite)
composer run dev

# Individual services
php artisan serve              # Laravel only
npm run dev                   # Vite assets
php artisan queue:listen --tries=1
php artisan pail --timeout=0  # Real-time logs
```

### Database Operations
```bash
php artisan migrate:fresh --seed  # Reset with seeders
php artisan optimize:clear        # Clear all caches (config/route/view/cache)
```

### Code Quality
```bash
composer test           # PHPUnit tests
./vendor/bin/pint      # Code formatting (Laravel Pint)
```

## Architecture Patterns

### 1. BaseController Pattern
**All backend controllers extend `app/Http/Controllers/Backend/BaseController.php`** which provides:
- Automatic authentication (`auth` middleware)
- Required `dashboard_access` permission
- Resource-based CRUD permission mapping:
  - `index`, `show` → `{resource}_access`
  - `create`, `store` → `{resource}_create`
  - `edit`, `update` → `{resource}_edit`
  - `destroy` → `{resource}_delete`

**Example:**
```php
class ProductController extends BaseController {
    protected string $resource = 'product';
    protected array $additionalPermissions = ['product_management_access'];
    
    // Custom method permissions
    public function __construct() {
        parent::__construct();
        $this->applyMethodPermission('product_edit', ['approve', 'reject']);
    }
}
```

### 2. Permission System (3-Layer Architecture)
- **PermissionMiddleware** (`app/Http/Middleware/PermissionMiddleware.php`) - Core permission logic
- **HasPermissions Trait** (`app/Traits/HasPermissions.php`) - Controller helper methods
- **BladeServiceProvider** - Custom Blade directives

**Controller Usage:**
```php
$this->authorizePermission('user_edit');
$this->canCRUD('product', 'create');
```

**View Usage:**
```blade
@permission('user_create')
    <button>Create User</button>
@endpermission

@cancrud('product', 'edit')
    <button>Edit Product</button>
@endcancrud
```

### 3. Translation System (Polymorphic)
**HasTranslations Trait** (`app/Traits/HasTranslations.php`) makes models multilingual:

```php
class Product extends Model {
    use HasTranslations;
    protected array $translatable = ['name', 'description', 'meta_title'];
}

// Usage
$product->setTranslation('name', 'Product Name', 'en');
$product->getTranslation('name', 'es'); // Auto-fallback to default locale
```

**Translations stored in `translations` table (polymorphic):**
- `translatable_type` - Model class
- `translatable_id` - Model ID
- `locale` - Language code
- `field` - Field name
- `value` - Translated content

### 4. Multi-Vendor Isolation
**Critical Pattern:** Always filter by `vendor_id` in vendor contexts:
```php
// Products belong to vendors
Product::where('vendor_id', $vendor->id)->get();

// Orders split into vendor_orders for commission tracking
$vendorOrder->commission = $orderItem->price * ($vendor->commission_rate / 100);
```

**Key Tables:**
- `vendors` - Vendor profiles (linked to `users`)
- `products` - Has `vendor_status` field (pending/approved/rejected)
- `vendor_orders` - Vendor-specific order portions
- `vendor_payouts` - Commission payouts to vendors
- `vendor_withdrawals` - Withdrawal requests

## CRUD Pattern (Standardized)

### Controller Structure
```php
// ImageUploadTrait for consistent uploads
use ImageUploadTrait;

// Store/Update: Handle both direct upload and media selector
if ($request->hasFile('image')) {
    $data['image'] = $this->uploadImage($request, 'image', 'uploads/products', 'product_');
} elseif ($request->filled('image_url')) {
    // Media selector URL - convert to relative path
    $data['image'] = str_replace(url('/storage/'), '', $request->image_url);
}
```

### View Pattern (DataTables Server-Side)
All index views use server-side DataTables with AJAX for performance:
```javascript
$('#resourceTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("admin.resources.index") }}',
    // ... column definitions
});
```

### Media Selector Component
Reusable component for image uploads/selection:
```blade
<x-media-selector 
    name="image" 
    :value="$model->image"
    :show_gallery="true" 
    :show_upload="true"
    preview_height="200px" />
```

## Key File Patterns

### Controllers
- Location: `app/Http/Controllers/Backend/{Resource}Controller.php`
- Must extend `BaseController`
- Use `ImageUploadTrait` for uploads
- Return JSON for AJAX requests

### Views
- Location: `resources/views/admin/{resources}/index.blade.php`
- Extends: `admin.layouts.master_layout`
- Use modals for create/edit (single page experience)
- SweetAlert2 for notifications

### Models
- Location: `app/Models/{Resource}.php`
- Add `HasTranslations` trait if multilingual
- Define `$translatable` array for translated fields
- Use `SoftDeletes` for recoverable deletions

### Routes
- Admin routes: `routes/admin.php`
- Pattern: `Route::prefix('admin')->middleware(['auth', 'permission:dashboard_access'])`
- Group by functionality (users, products, orders, etc.)

## Important Conventions

### 1. Vendor Status Flow
Products have approval workflow:
- `vendor_status`: pending → approved/rejected
- `vendor_rejection_reason` field for rejection notes
- Only approved products visible to customers

### 2. Order Commission Calculation
Applied at **order item level**, not order level:
```php
$commission = $orderItem->price * ($vendor->commission_rate / 100);
$vendor->balance += ($orderItem->price - $commission);
```

### 3. Validation & Error Handling
```php
// Server-side validation errors shown via AJAX
if (xhr.status === 422) {
    const errors = xhr.responseJSON?.errors || {};
    Object.keys(errors).forEach(key => {
        $(`[name="${key}"]`).addClass('is-invalid')
            .next('.invalid-feedback').text(errors[key][0]);
    });
}
```

### 4. Asset Helper
Use `assetUrl()` helper for backend assets:
```blade
<link href="{{ assetUrl() }}assets/backend/lib/datatables/css/dataTables.bootstrap4.min.css">
```

## Common Tasks

### Adding New CRUD Resource
1. Create migration with fields + `is_active`, `created_at`, `updated_at`
2. Create model extending `Model` (add `HasTranslations` if needed)
3. Create controller extending `BaseController` with `$resource` property
4. Define permissions in seeder: `{resource}_access/create/edit/delete`
5. Create views following pattern in `CRUD_PATTERN_DOCUMENTATION.md`
6. Add routes in `routes/admin.php`

### Adding Translation Support
1. Add `use HasTranslations;` to model
2. Define `protected array $translatable = ['field1', 'field2'];`
3. Use `setTranslation()` in controller store/update methods
4. Use `getTranslation()` in views

### Working with Vendor Data
1. Always filter by `vendor_id` in queries
2. Check `vendor_status` for product approval state
3. Update `vendor.balance` when processing orders
4. Create `vendor_orders` record for commission tracking

## Security Reminders
- All backend routes require `auth` + `dashboard_access` permission
- Use resource-specific permissions via `BaseController`
- Validate vendor ownership before operations
- Never expose vendor commission calculations to frontend

## Testing
- Feature tests in `tests/Feature/`
- Unit tests in `tests/Unit/`
- Run with `php artisan test` or `composer test`
- Coverage: `php artisan test --coverage`

## Additional Documentation
- `CONTROLLER_PERMISSIONS.md` - Complete permission mapping
- `CRUD_PATTERN_DOCUMENTATION.md` - Detailed CRUD implementation guide
- `TRANSLATION_SYSTEM_GUIDE.md` - Translation system deep dive
- `PERMISSION_USAGE.md` - Permission middleware usage examples
