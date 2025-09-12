<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Http\Middleware\PermissionMiddleware;

class BladeServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    //
  }

  public function boot(): void
  {
    // Permission directive - check single permission
    Blade::if('permission', function (string $permission) {
      if (!auth()->check()) {
        return false;
      }

      $middleware = new PermissionMiddleware();
      return $middleware->hasPermission(auth()->user(), $permission);
    });

    // Any permission directive - check if user has any of the permissions
    Blade::if('anypermission', function (array $permissions) {
      if (!auth()->check()) {
        return false;
      }

      $middleware = new PermissionMiddleware();
      return $middleware->hasAnyPermission(auth()->user(), $permissions);
    });

    // All permissions directive - check if user has all permissions
    Blade::if('allpermissions', function (array $permissions) {
      if (!auth()->check()) {
        return false;
      }

      $middleware = new PermissionMiddleware();
      return $middleware->hasAllPermissions(auth()->user(), $permissions);
    });

    // Resource access directive
    Blade::if('canaccess', function (string $resource, string $action = 'access') {
      if (!auth()->check()) {
        return false;
      }

      $middleware = new PermissionMiddleware();
      return $middleware->canAccessResource(auth()->user(), $resource, $action);
    });

    // CRUD permission directive
    Blade::if('cancrud', function (string $resource, string $action) {
      if (!auth()->check()) {
        return false;
      }

      $middleware = new PermissionMiddleware();
      return $middleware->canCRUD(auth()->user(), $resource, $action);
    });

    // Super admin directive
    Blade::if('superadmin', function () {
      if (!auth()->check()) {
        return false;
      }

      $middleware = new PermissionMiddleware();
      return $middleware->isSuperAdmin(auth()->user());
    });

    // Dashboard access directive
    Blade::if('dashboard', function () {
      if (!auth()->check()) {
        return false;
      }

      $middleware = new PermissionMiddleware();
      return $middleware->canAccessDashboard(auth()->user());
    });

    // User management directive
    Blade::if('usermanager', function () {
      if (!auth()->check()) {
        return false;
      }

      $middleware = new PermissionMiddleware();
      return $middleware->canManageUsers(auth()->user());
    });
  }
}
