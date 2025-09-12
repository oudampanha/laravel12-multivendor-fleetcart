# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 multi-vendor e-commerce platform inspired by FleetCart. The application provides a comprehensive marketplace solution with vendor management, product catalog, order processing, and payment handling capabilities.

## Development Commands

### Core Commands
```bash
# Start development server with all services
composer run dev

# Run Laravel server only
php artisan serve

# Build frontend assets
npm run build

# Watch frontend assets for changes
npm run dev

# Run tests
composer test
# or
php artisan test

# Run specific test
php artisan test --filter TestName

# Code formatting
./vendor/bin/pint

# Database operations
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# Clear all caches
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Queue worker
php artisan queue:listen --tries=1
```

## Architecture Overview

### Core Structure
The application follows Laravel's MVC architecture with additional layers for multi-vendor functionality:

1. **Multi-Vendor System**: The platform supports multiple vendors who can manage their own products, orders, and settings. Each vendor has a separate dashboard and commission structure.

2. **User & Permission System**: 
   - Role-based access control with permissions grouped by functionality
   - Separate authentication for customers and vendors
   - OTP verification for enhanced security

3. **Product Management**:
   - Products belong to vendors with approval workflow
   - Support for product variations, attributes, and options
   - Categories with hierarchical structure
   - Brand management
   - Tax classes and rates per region

4. **Order Processing**:
   - Orders split by vendor (vendor_orders)
   - Commission calculation per vendor
   - Order status tracking per vendor item
   - Support for coupons and discounts

5. **Payment & Withdrawal**:
   - Vendor balance tracking
   - Withdrawal requests with approval workflow
   - Multiple payout methods (bank transfer, PayPal, Stripe)
   - Transaction logging

### Key Database Tables

**Core Entities:**
- `users` - All users (customers, vendors, admins)
- `vendors` - Vendor-specific information linked to users
- `products` - Product catalog with vendor association
- `orders` - Customer orders
- `vendor_orders` - Vendor-specific order portions

**Product Relations:**
- `product_categories`, `product_attributes`, `product_variants` - Product metadata
- `variations`, `variation_values` - Global variation definitions
- `options`, `option_values` - Product customization options

**Financial:**
- `vendor_payouts` - Payout records to vendors
- `vendor_withdrawals` - Withdrawal requests from vendors
- `transactions` - Payment transaction records

### Authentication & Security

The application uses Laravel Breeze for authentication with custom extensions:
- OTP verification for sensitive operations
- Vendor verification workflow
- Permission-based access control
- Session persistence tracking

### Frontend Stack

- **Tailwind CSS** for styling
- **Alpine.js** for interactive components
- **Vite** for asset bundling
- Blade templating engine

## Key Patterns & Conventions

1. **Vendor Isolation**: Always filter queries by vendor_id when in vendor context
2. **Status Management**: Products and orders have vendor-specific status fields
3. **Commission Calculation**: Applied at order item level, not order level
4. **Translation Support**: Uses unified translations table for multi-language support
5. **Media Management**: Centralized media table with polymorphic relations via entity_media

## Architecture Patterns

### BaseController Pattern
All backend controllers extend `app/Http/Controllers/Backend/BaseController.php` which provides:
- Automatic authentication requirement (`auth` middleware)
- Dashboard access requirement (`permission:dashboard_access`)
- Resource-based CRUD permission mapping:
  - `index`, `show` → `{resource}_access`
  - `create`, `store` → `{resource}_create`
  - `edit`, `update` → `{resource}_edit`
  - `destroy` → `{resource}_delete`
- Additional permission support for complex controllers

### Permission System Architecture
The permission system uses three key components:
1. **PermissionMiddleware** (`app/Http/Middleware/PermissionMiddleware.php`) - Core permission checking logic
2. **HasPermissions Trait** (`app/Traits/HasPermissions.php`) - Helper methods for controllers
3. **BladeServiceProvider** - Custom Blade directives for view-level permission checks

**Usage in Controllers:**
```php
class ProductController extends BaseController 
{
    protected string $resource = 'product';
    protected array $additionalPermissions = ['product_management_access'];
}
```

**Usage in Views:**
```blade
@permission('user_create')
    <a href="{{ route('users.create') }}">Create User</a>
@endpermission

@cancrud('product', 'edit')
    <button>Edit Product</button>
@endcancrud
```

### Translation System Architecture
The application includes a comprehensive translation system using:
1. **HasTranslations Trait** (`app/Traits/HasTranslations.php`) - Makes models translatable
2. **Translation Model** - Polymorphic storage for all translations
3. **TranslationService** - Business logic for translation management

**Usage:**
```php
// In Model
class Product extends Model {
    use HasTranslations;
    protected array $translatable = ['name', 'description'];
}

// Setting/Getting translations
$product->setTranslation('name', 'Product Name', 'en');
$name = $product->getTranslation('name', 'es');
```

### Vendor Architecture Patterns
The multi-vendor system uses vendor isolation throughout:
1. **Vendor Model** linked to User model via `hasOne` relationship
2. **Products** belong to vendors with approval workflow (`vendor_status` field)
3. **Orders** split into `vendor_orders` for commission tracking
4. **Vendor-specific settings, payouts, withdrawals, and notifications**

## Testing Approach

Tests are organized in:
- `tests/Feature/` - Integration and feature tests
- `tests/Unit/` - Unit tests for individual components

Run tests with coverage: `php artisan test --coverage`

## Environment Setup

Ensure `.env` file contains:
- Database connection (SQLite by default)
- Mail configuration for notifications
- Queue driver (database recommended)
- Payment gateway credentials (when needed)

## Common Development Tasks

When adding new vendor features:
1. Check vendor isolation in queries
2. Add appropriate permission checks
3. Update vendor balance calculations if financial
4. Consider commission implications

When modifying products:
1. Maintain vendor_status field
2. Update search indexes
3. Clear relevant caches
4. Check variation/option relationships

When working with orders:
1. Update both orders and vendor_orders tables
2. Calculate commissions correctly
3. Track vendor-specific status
4. Handle notifications to vendors

## Important Files for Reference

- `CONTROLLER_PERMISSIONS.md` - Complete permission mapping for all controllers
- `PERMISSION_USAGE.md` - Detailed permission system usage guide
- `TRANSLATION_SYSTEM_GUIDE.md` - Comprehensive translation system documentation
- `database/migrations/2025_09_07_170807_create_laravel_multivendor_table.php` - Complete database schema
- `routes/web/admin.php` - All admin routes organized by functionality

# important-instruction-reminders
Do what has been asked; nothing more, nothing less.
NEVER create files unless they're absolutely necessary for achieving your goal.
ALWAYS prefer editing an existing file to creating a new one.
NEVER proactively create documentation files (*.md) or README files. Only create documentation files if explicitly requested by the User.