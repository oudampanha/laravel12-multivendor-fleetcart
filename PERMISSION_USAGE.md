# Permission Middleware Usage Guide

The PermissionMiddleware provides comprehensive permission control for the multi-vendor Laravel application.

## Components Created

1. **PermissionMiddleware** (`app/Http/Middleware/PermissionMiddleware.php`)
2. **HasPermissions Trait** (`app/Traits/HasPermissions.php`)
3. **BladeServiceProvider** (`app/Providers/BladeServiceProvider.php`)

## Middleware Registration

The middleware is registered in `bootstrap/app.php` with the alias `'permission'`.

## Basic Usage in Routes

### Single Permission
```php
Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware('permission:user_access');

Route::post('/admin/users', [UserController::class, 'store'])
    ->middleware('permission:user_create');
```

### Route Groups
```php
Route::middleware(['auth', 'permission:dashboard_access'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
    Route::resource('/admin/users', UserController::class);
});
```

### Multiple Route Protection
```php
Route::middleware(['auth', 'permission:user_management_access'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
    Route::get('/admin/users/create', [UserController::class, 'create'])
        ->middleware('permission:user_create');
    Route::post('/admin/users', [UserController::class, 'store'])
        ->middleware('permission:user_create');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:user_edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:user_edit');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:user_delete');
});
```

## Using the HasPermissions Trait in Controllers

Add the trait to your controllers for easy permission checking:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HasPermissions;

class UserController extends Controller
{
    use HasPermissions;

    public function index()
    {
        // Check permission manually
        $this->authorizePermission('user_access');
        
        // Or check resource access
        $this->authorizeResource('user', 'access');
        
        // Your controller logic...
    }

    public function create()
    {
        // Check if user can create users
        if (!$this->canCRUD('user', 'create')) {
            abort(403, 'You cannot create users.');
        }
        
        // Your controller logic...
    }

    public function adminPanel()
    {
        // Check multiple permissions (must have ALL)
        $this->authorizeAllPermissions([
            'dashboard_access',
            'user_management_access'
        ]);
        
        // Your controller logic...
    }

    public function moderatorPanel()
    {
        // Check multiple permissions (must have ANY)
        $this->authorizeAnyPermission([
            'user_management_access',
            'vendor_management_access',
            'product_management_access'
        ]);
        
        // Your controller logic...
    }
}
```

## Blade Directives Usage in Views

### Basic Permission Check
```blade
@permission('user_create')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        Create User
    </a>
@endpermission

@permission('user_edit')
    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
        Edit User
    </a>
@endpermission
```

### Any Permission Check
```blade
@anypermission(['user_edit', 'user_delete', 'user_show'])
    <div class="user-actions">
        @permission('user_show')
            <a href="{{ route('users.show', $user) }}">View</a>
        @endpermission
        
        @permission('user_edit')
            <a href="{{ route('users.edit', $user) }}">Edit</a>
        @endpermission
        
        @permission('user_delete')
            <form method="POST" action="{{ route('users.destroy', $user) }}">
                @csrf @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        @endpermission
    </div>
@endanypermission
```

### All Permissions Check
```blade
@allpermissions(['dashboard_access', 'user_management_access'])
    <div class="admin-panel">
        <h3>Admin Controls</h3>
        <!-- Admin-only content -->
    </div>
@endallpermissions
```

### Resource Access Check
```blade
@canaccess('user', 'create')
    <button class="create-user-btn">Create New User</button>
@endcanaccess

@canaccess('product', 'edit')
    <button class="edit-product-btn">Edit Product</button>
@endcanaccess
```

### CRUD Permission Check
```blade
@cancrud('user', 'create')
    <a href="{{ route('users.create') }}">Add User</a>
@endcancrud

@cancrud('product', 'delete')
    <button class="delete-btn" data-id="{{ $product->id }}">Delete</button>
@endcancrud
```

### Special Directives
```blade
@superadmin
    <div class="super-admin-panel">
        <p>Super Admin Panel</p>
        <!-- Super admin content -->
    </div>
@endsuperadmin

@dashboard
    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </nav>
@enddashboard

@usermanager
    <div class="user-management">
        <a href="{{ route('admin.users.index') }}">Manage Users</a>
    </div>
@endusermanager
```

## Available Permissions

Based on the migration, these permissions are available:

### Core Permissions
- `dashboard_access`
- `user_management_access`

### Permission Management
- `permission_create`
- `permission_edit`
- `permission_show`
- `permission_delete`
- `permission_access`

### Role Management
- `role_create`
- `role_edit`
- `role_show`
- `role_delete`
- `role_access`

### User Management
- `user_create`
- `user_edit`
- `user_show`
- `user_delete`
- `user_access`
- `user_profile_password_edit`
- `user_profile_password_show`
- `user_profile_password_delete`
- `user_profile_password_access`

### Author Management
- `author_create`
- `author_edit`
- `author_show`
- `author_delete`
- `author_access`

## Advanced Usage Examples

### Dynamic Permission Checking
```php
public function handleUserAction($userId, $action)
{
    $permissionMap = [
        'view' => 'user_show',
        'edit' => 'user_edit',
        'delete' => 'user_delete',
    ];
    
    if (!isset($permissionMap[$action])) {
        abort(400, 'Invalid action');
    }
    
    if (!$this->hasPermission($permissionMap[$action])) {
        abort(403, "You don't have permission to {$action} users");
    }
    
    // Perform action...
}
```

### Getting User's Permissions
```php
public function getUserPermissions()
{
    $permissions = $this->getUserPermissions();
    $accessibleResources = $this->getAccessibleResources();
    
    return response()->json([
        'permissions' => $permissions,
        'resources' => $accessibleResources,
    ]);
}
```

### Conditional Menu Generation
```blade
<ul class="nav">
    @dashboard
        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    @enddashboard
    
    @permission('user_access')
        <li><a href="{{ route('admin.users.index') }}">Users</a></li>
    @endpermission
    
    @anypermission(['role_access', 'permission_access'])
        <li class="dropdown">
            <a href="#">Permissions</a>
            <ul>
                @permission('role_access')
                    <li><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                @endpermission
                @permission('permission_access')
                    <li><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
                @endpermission
            </ul>
        </li>
    @endanypermission
</ul>
```

## Security Features

1. **Super Admin Detection**: Users with both `dashboard_access` and `user_management_access` are treated as super admins
2. **Role-based Permissions**: Supports permissions through roles and direct user permissions
3. **AJAX Support**: Returns JSON responses for AJAX requests
4. **Resource-based Checking**: Supports dynamic resource and action combinations
5. **Comprehensive Validation**: Checks both direct permissions and role-based permissions

## Error Handling

The middleware handles errors gracefully:
- Redirects to login for unauthenticated users
- Returns JSON for AJAX requests (403 status)
- Redirects back with error messages for regular requests
- Provides detailed error messages for debugging