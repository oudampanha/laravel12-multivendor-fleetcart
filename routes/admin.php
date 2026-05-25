<?php

use App\Http\Controllers\Backend\ActivationController;
use App\Http\Controllers\Backend\AddressController;
use App\Http\Controllers\Backend\AttributeController;
use App\Http\Controllers\Backend\AttributeSetController;
use App\Http\Controllers\Backend\BlogCategoryController;
use App\Http\Controllers\Backend\BlogPostController;
use App\Http\Controllers\Backend\BlogTagController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CartController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\CouponController;
use App\Http\Controllers\Backend\CurrencyRateController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DefaultAddressController;
use App\Http\Controllers\Backend\EntityMediaController;
use App\Http\Controllers\Backend\FlashSaleController;
use App\Http\Controllers\Backend\FlashSaleProductController;
use App\Http\Controllers\Backend\GoodsReceiptController;
use App\Http\Controllers\Backend\InventoryDashboardController;
use App\Http\Controllers\Backend\LanguageLineController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\MenuItemController;
use App\Http\Controllers\Backend\MetaDataController;
use App\Http\Controllers\Backend\OptionController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\OtpVerificationController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\PersistenceController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ProductStockController;
use App\Http\Controllers\Backend\PurchaseOrderController;
use App\Http\Controllers\Backend\ReminderController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\ReviewController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SearchTermController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SliderController;
use App\Http\Controllers\Backend\SliderSlideController;
use App\Http\Controllers\Backend\StockAdjustmentController;
use App\Http\Controllers\Backend\StockMovementController;
use App\Http\Controllers\Backend\StockTakeController;
use App\Http\Controllers\Backend\StockTransferController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\TagController;
use App\Http\Controllers\Backend\TaxClassController;
use App\Http\Controllers\Backend\TaxRateController;
use App\Http\Controllers\Backend\ThrottleController;
use App\Http\Controllers\Backend\TransactionController;
use App\Http\Controllers\Backend\TranslationController;
use App\Http\Controllers\Backend\TranslationManagementController;
use App\Http\Controllers\Backend\UpdaterScriptController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\VariationController;
use App\Http\Controllers\Backend\VendorController;
use App\Http\Controllers\Backend\VendorNotificationController;
use App\Http\Controllers\Backend\VendorOrderController;
use App\Http\Controllers\Backend\VendorPayoutController;
use App\Http\Controllers\Backend\VendorReviewController;
use App\Http\Controllers\Backend\VendorSettingController;
use App\Http\Controllers\Backend\VendorShippingZoneController;
use App\Http\Controllers\Backend\VendorWithdrawalController;
use App\Http\Controllers\Backend\WarehouseController;
use App\Http\Controllers\Backend\WishListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are all the admin routes for the Laravel 12 multi-vendor platform.
| These routes are organized by functionality and include proper middleware
| for authentication and permission checking.
|
*/

Route::prefix('admin/media')->name('admin.media.')->group(function () {
    // Main page and list API
    Route::get('/', [MediaController::class, 'index'])->name('index');
    Route::get('/list', [MediaController::class, 'list'])->name('list');
    Route::get('/folders', [MediaController::class, 'getFolders'])->name('folders');

    // Folder operations
    Route::post('/create-folder', [MediaController::class, 'createFolder'])->name('create-folder');
    Route::post('/rename-folder', [MediaController::class, 'renameFolder'])->name('rename-folder');
    Route::delete('/delete-folder', [MediaController::class, 'deleteFolder'])->name('delete-folder');

    // File operations
    Route::post('/upload', [MediaController::class, 'upload'])->name('upload');
    Route::post('/bulk-upload', [MediaController::class, 'bulkUpload'])->name('bulk-upload');
    Route::post('/rename-file', [MediaController::class, 'renameFile'])->name('rename-file');
    Route::delete('/delete/{id}', [MediaController::class, 'delete'])->name('delete');

    // Move and copy operations
    Route::post('/move-to-folder', [MediaController::class, 'moveToFolder'])->name('move-to-folder');
    Route::post('/copy-to-folder', [MediaController::class, 'copyToFolder'])->name('copy-to-folder');

    // Bulk operations
    Route::post('/bulk-move-to-folder', [MediaController::class, 'bulkMoveToFolder'])->name('bulk-move-to-folder');
    Route::post('/bulk-copy-to-folder', [MediaController::class, 'bulkCopyToFolder'])->name('bulk-copy-to-folder');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'permission:dashboard_access'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // =============================================================================
    // USER MANAGEMENT
    // =============================================================================

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
    Route::get('users/{user}/login-history', [UserController::class, 'loginHistory'])->name('users.login-history');

    // Roles
    Route::resource('roles', RoleController::class);
    Route::post('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // Permissions
    Route::get('permissions/groups', [PermissionController::class, 'groups'])->name('permissions.groups');
    Route::resource('permissions', PermissionController::class);
    Route::post('permissions/{permission}/toggle-status', [PermissionController::class, 'toggleStatus'])->name('permissions.toggle-status');

    // OTP Verifications
    Route::get('otp-verifications', [OtpVerificationController::class, 'index'])->name('otp-verifications.index');
    Route::delete('otp-verifications/{otpVerification}', [OtpVerificationController::class, 'destroy'])->name('otp-verifications.destroy');
    Route::post('otp-verifications/cleanup', [OtpVerificationController::class, 'cleanup'])->name('otp-verifications.cleanup');

    // =============================================================================
    // VENDOR MANAGEMENT
    // =============================================================================

    // Vendors
    Route::resource('vendors', VendorController::class);
    Route::post('vendors/{vendor}/verify', [VendorController::class, 'verify'])->name('vendors.verify');
    Route::post('vendors/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendors.toggle-status');
    Route::get('vendors/{vendor}/balance', [VendorController::class, 'balance'])->name('vendors.balance');
    Route::post('vendors/{vendor}/adjust-balance', [VendorController::class, 'adjustBalance'])->name('vendors.adjust-balance');
    Route::get('vendors/{vendor}/settings', [VendorController::class, 'settings'])->name('vendors.settings');
    Route::post('vendors/{vendor}/settings', [VendorController::class, 'updateSettings'])->name('vendors.settings.update');
    Route::get('vendors/{vendor}/products', [VendorController::class, 'products'])->name('vendors.products');
    Route::get('vendors/{vendor}/orders', [VendorController::class, 'orders'])->name('vendors.orders');
    Route::get('vendors/{vendor}/reviews', [VendorController::class, 'reviews'])->name('vendors.reviews');
    Route::get('vendors/{vendor}/notifications', [VendorController::class, 'notifications'])->name('vendors.notifications');
    Route::post('vendors/{vendor}/notify', [VendorController::class, 'notify'])->name('vendors.notify');

    // Vendor Settings
    Route::get('vendor-settings', [VendorSettingController::class, 'index'])->name('vendor-settings.index');
    Route::get('vendor-settings/{vendor}', [VendorSettingController::class, 'show'])->name('vendor-settings.show');
    Route::post('vendor-settings/{vendor}', [VendorSettingController::class, 'update'])->name('vendor-settings.update');

    // Vendor Payouts (static GETs declared first so they win over resource show route)
    Route::get('vendor-payouts/pending', [VendorPayoutController::class, 'pending'])->name('vendor-payouts.pending');
    Route::get('vendor-payouts/completed', [VendorPayoutController::class, 'completed'])->name('vendor-payouts.completed');
    Route::resource('vendor-payouts', VendorPayoutController::class);
    Route::post('vendor-payouts/{vendorPayout}/approve', [VendorPayoutController::class, 'approve'])->name('vendor-payouts.approve');
    Route::post('vendor-payouts/{vendorPayout}/reject', [VendorPayoutController::class, 'reject'])->name('vendor-payouts.reject');
    Route::post('vendor-payouts/{vendorPayout}/mark-paid', [VendorPayoutController::class, 'markPaid'])->name('vendor-payouts.mark-paid');

    // Vendor Withdrawals (static GETs declared first so they win over resource show route)
    Route::get('vendor-withdrawals/pending', [VendorWithdrawalController::class, 'pending'])->name('vendor-withdrawals.pending');
    Route::get('vendor-withdrawals/processed', [VendorWithdrawalController::class, 'processed'])->name('vendor-withdrawals.processed');
    Route::resource('vendor-withdrawals', VendorWithdrawalController::class)->except(['create', 'store']);
    Route::post('vendor-withdrawals/{vendorWithdrawal}/approve', [VendorWithdrawalController::class, 'approve'])->name('vendor-withdrawals.approve');
    Route::post('vendor-withdrawals/{vendorWithdrawal}/reject', [VendorWithdrawalController::class, 'reject'])->name('vendor-withdrawals.reject');
    Route::post('vendor-withdrawals/{vendorWithdrawal}/process', [VendorWithdrawalController::class, 'process'])->name('vendor-withdrawals.process');

    // Vendor Orders
    Route::get('vendor-orders', [VendorOrderController::class, 'index'])->name('vendor-orders.index');
    Route::get('vendor-orders/{vendorOrder}', [VendorOrderController::class, 'show'])->name('vendor-orders.show');
    Route::post('vendor-orders/{vendorOrder}/update-status', [VendorOrderController::class, 'updateStatus'])->name('vendor-orders.update-status');
    Route::get('vendor-orders/by-vendor/{vendor}', [VendorOrderController::class, 'byVendor'])->name('vendor-orders.by-vendor');
    Route::get('vendor-orders/by-status/{status}', [VendorOrderController::class, 'byStatus'])->name('vendor-orders.by-status');

    // Vendor Notifications
    Route::resource('vendor-notifications', VendorNotificationController::class)->except(['create', 'store']);
    Route::post('vendor-notifications/{vendorNotification}/mark-read', [VendorNotificationController::class, 'markRead'])->name('vendor-notifications.mark-read');
    Route::post('vendor-notifications/mark-all-read/{vendor}', [VendorNotificationController::class, 'markAllRead'])->name('vendor-notifications.mark-all-read');
    Route::get('vendor-notifications/by-vendor/{vendor}', [VendorNotificationController::class, 'byVendor'])->name('vendor-notifications.by-vendor');

    // Vendor Shipping Zones
    Route::get('vendor-shipping-zones', [VendorShippingZoneController::class, 'index'])->name('vendor-shipping-zones.index');
    Route::get('vendor-shipping-zones/{vendorShippingZone}', [VendorShippingZoneController::class, 'show'])->name('vendor-shipping-zones.show');
    Route::post('vendor-shipping-zones/{vendorShippingZone}/toggle-status', [VendorShippingZoneController::class, 'toggleStatus'])->name('vendor-shipping-zones.toggle-status');
    Route::get('vendor-shipping-zones/by-vendor/{vendor}', [VendorShippingZoneController::class, 'byVendor'])->name('vendor-shipping-zones.by-vendor');

    // Vendor Reviews (static GETs declared first so they win over resource show route)
    Route::get('vendor-reviews/pending', [VendorReviewController::class, 'pending'])->name('vendor-reviews.pending');
    Route::get('vendor-reviews/approved', [VendorReviewController::class, 'approved'])->name('vendor-reviews.approved');
    Route::resource('vendor-reviews', VendorReviewController::class)->except(['create', 'store']);
    Route::post('vendor-reviews/{vendorReview}/approve', [VendorReviewController::class, 'approve'])->name('vendor-reviews.approve');
    Route::post('vendor-reviews/{vendorReview}/reject', [VendorReviewController::class, 'reject'])->name('vendor-reviews.reject');
    Route::get('vendor-reviews/by-vendor/{vendor}', [VendorReviewController::class, 'byVendor'])->name('vendor-reviews.by-vendor');

    // =============================================================================
    // PRODUCT MANAGEMENT
    // =============================================================================

    // Products (static GETs declared first so they win over resource show route)
    Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('products/pending-approval', [ProductController::class, 'pendingApproval'])->name('products.pending-approval');
    Route::get('products/approved', [ProductController::class, 'approved'])->name('products.approved');
    Route::get('products/rejected', [ProductController::class, 'rejected'])->name('products.rejected');
    Route::resource('products', ProductController::class);

    // Categories
    Route::get('categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');
    Route::get('categories/search', [CategoryController::class, 'search'])->name('categories.search');
    Route::get('categories/parents', [CategoryController::class, 'getParentCategories'])->name('categories.parents');
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::post('categories/update-position', [CategoryController::class, 'updatePosition'])->name('categories.update-position');
    Route::resource('categories', CategoryController::class);
    Route::get('categories/{category}/products', [CategoryController::class, 'products'])->name('categories.products');
    Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::post('categories/{category}/toggle-searchable', [CategoryController::class, 'toggleSearchable'])->name('categories.toggle-searchable');

    // Brands
    Route::get('brands/search', [BrandController::class, 'search'])->name('brands.search');
    Route::get('brands/by-status', [BrandController::class, 'byStatus'])->name('brands.by-status');
    Route::resource('brands', BrandController::class);
    Route::get('brands/{brand}/products', [BrandController::class, 'products'])->name('brands.products');
    Route::post('brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggle-status');

    // Attribute Sets
    Route::get('attribute-sets/search', [AttributeSetController::class, 'search'])->name('attribute-sets.search');
    Route::resource('attribute-sets', AttributeSetController::class);
    Route::get('attribute-sets/{attributeSet}/attributes', [AttributeSetController::class, 'attributes'])->name('attribute-sets.attributes');
    Route::post('attribute-sets/{attributeSet}/attributes', [AttributeSetController::class, 'attachAttribute'])->name('attribute-sets.attributes.attach');
    Route::delete('attribute-sets/{attributeSet}/attributes/{attribute}', [AttributeSetController::class, 'detachAttribute'])->name('attribute-sets.attributes.detach');

    // Attributes
    Route::get('attributes/categories', [AttributeController::class, 'getCategories'])->name('attributes.categories');
    Route::post('attributes/reorder-values', [AttributeController::class, 'reorderValues'])->name('attributes.reorder-values');
    Route::resource('attributes', AttributeController::class);
    Route::get('attributes/{attribute}/values', [AttributeController::class, 'values'])->name('attributes.values');
    Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])->name('attributes.values.store');
    Route::delete('attributes/{attribute}/values/{attributeValue}', [AttributeController::class, 'destroyValue'])->name('attributes.values.destroy');
    Route::post('attributes/{attribute}/toggle-filterable', [AttributeController::class, 'toggleFilterable'])->name('attributes.toggle-filterable');

    // Variations
    Route::get('variations/search', [VariationController::class, 'search'])->name('variations.search');
    Route::post('variations/reorder-values', [VariationController::class, 'reorderValues'])->name('variations.reorder-values');
    Route::resource('variations', VariationController::class);
    Route::get('variations/{variation}/values', [VariationController::class, 'values'])->name('variations.values');
    Route::post('variations/{variation}/values', [VariationController::class, 'storeValue'])->name('variations.values.store');
    Route::delete('variations/{variation}/values/{variationValueId}', [VariationController::class, 'destroyValue'])->name('variations.values.destroy');
    Route::post('variations/{variation}/toggle-global', [VariationController::class, 'toggleGlobal'])->name('variations.toggle-global');

    // Options
    Route::resource('options', OptionController::class);
    Route::get('options/{option}/values', [OptionController::class, 'values'])->name('options.values');
    Route::post('options/{option}/values', [OptionController::class, 'storeValue'])->name('options.values.store');
    Route::delete('options/{option}/values/{optionValueId}', [OptionController::class, 'destroyValue'])->name('options.values.destroy');
    Route::post('options/{option}/toggle-global', [OptionController::class, 'toggleGlobal'])->name('options.toggle-global');
    Route::post('options/{option}/toggle-required', [OptionController::class, 'toggleRequired'])->name('options.toggle-required');

    // Tags
    Route::resource('tags', TagController::class);
    Route::get('tags/{tag}/products', [TagController::class, 'products'])->name('tags.products');
    Route::post('tags/merge', [TagController::class, 'merge'])->name('tags.merge');

    // =============================================================================
    // ORDER MANAGEMENT
    // =============================================================================

    // Orders (static GETs declared first so they win over resource show route)
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/by-status/{status}', [OrderController::class, 'byStatus'])->name('orders.by-status');
    Route::get('orders/by-payment-method/{paymentMethod}', [OrderController::class, 'byPaymentMethod'])->name('orders.by-payment-method');
    Route::get('orders/vendor-orders', [OrderController::class, 'vendorOrders'])->name('orders.vendor-orders');
    Route::get('orders/vendor-orders/{vendorOrder}', [OrderController::class, 'showVendorOrder'])->name('orders.vendor-orders.show');
    Route::post('orders/vendor-orders/{vendorOrder}/status', [OrderController::class, 'updateVendorOrderStatus'])->name('orders.vendor-orders.status');
    Route::post('orders/bulk-update-status', [OrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-update-status');
    Route::resource('orders', OrderController::class);
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
    Route::post('orders/{order}/invoice/send', [OrderController::class, 'sendInvoice'])->name('orders.invoice.send');
    Route::get('orders/{order}/tracking', [OrderController::class, 'tracking'])->name('orders.tracking');
    Route::post('orders/{order}/tracking', [OrderController::class, 'updateTracking'])->name('orders.tracking.update');

    // Transactions (static GETs declared first so they win over resource show route)
    Route::get('transactions/failed', [TransactionController::class, 'failed'])->name('transactions.failed');
    Route::get('transactions/refunded', [TransactionController::class, 'refunded'])->name('transactions.refunded');
    Route::resource('transactions', TransactionController::class)->except(['create', 'store', 'edit', 'update']);
    Route::get('transactions/{transaction}/details', [TransactionController::class, 'details'])->name('transactions.details');
    Route::post('transactions/{transaction}/refund', [TransactionController::class, 'refund'])->name('transactions.refund');
    Route::get('transactions/by-payment-method/{method}', [TransactionController::class, 'byPaymentMethod'])->name('transactions.by-payment-method');

    // =============================================================================
    // DISCOUNT & COUPON MANAGEMENT
    // =============================================================================

    // Coupons (static GETs declared first so they win over resource show route)
    Route::get('coupons/expired', [CouponController::class, 'expired'])->name('coupons.expired');
    Route::get('coupons/active', [CouponController::class, 'active'])->name('coupons.active');
    Route::resource('coupons', CouponController::class);
    Route::post('coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
    Route::get('coupons/{coupon}/usage', [CouponController::class, 'usage'])->name('coupons.usage');
    Route::post('coupons/{coupon}/reset-usage', [CouponController::class, 'resetUsage'])->name('coupons.reset-usage');
    Route::get('coupons/by-vendor/{vendor}', [CouponController::class, 'byVendor'])->name('coupons.by-vendor');
    Route::post('coupons/{coupon}/duplicate', [CouponController::class, 'duplicate'])->name('coupons.duplicate');

    // Flash Sales
    Route::resource('flash-sales', FlashSaleController::class);
    Route::get('flash-sales/{flashSale}/products', [FlashSaleController::class, 'products'])->name('flash-sales.products');
    Route::post('flash-sales/{flashSale}/products', [FlashSaleController::class, 'addProduct'])->name('flash-sales.products.add');
    Route::delete('flash-sales/{flashSale}/products/{product}', [FlashSaleController::class, 'removeProduct'])->name('flash-sales.products.remove');
    Route::post('flash-sales/{flashSale}/products/reorder', [FlashSaleController::class, 'reorderProducts'])->name('flash-sales.products.reorder');
    Route::get('flash-sales/{flashSale}/orders', [FlashSaleController::class, 'orders'])->name('flash-sales.orders');

    // Flash Sale Products
    Route::resource('flash-sale-products', FlashSaleProductController::class);

    // =============================================================================
    // TAX MANAGEMENT
    // =============================================================================

    // Tax Classes
    Route::resource('tax-classes', TaxClassController::class);
    Route::get('tax-classes/{taxClass}/rates', [TaxClassController::class, 'rates'])->name('tax-classes.rates');
    Route::post('tax-classes/{taxClass}/rates', [TaxClassController::class, 'addRate'])->name('tax-classes.rates.add');

    // Tax Rates (static GETs declared first so they win over resource show route)
    Route::get('tax-rates/calculator', [TaxRateController::class, 'calculator'])->name('tax-rates.calculator');
    Route::resource('tax-rates', TaxRateController::class);
    Route::post('tax-rates/reorder', [TaxRateController::class, 'reorder'])->name('tax-rates.reorder');
    Route::get('tax-rates/by-country/{country}', [TaxRateController::class, 'byCountry'])->name('tax-rates.by-country');
    Route::post('tax-rates/calculate', [TaxRateController::class, 'calculate'])->name('tax-rates.calculate');

    // =============================================================================
    // REVIEW MANAGEMENT
    // =============================================================================

    // Product Reviews (static GETs declared first so they win over resource show route)
    Route::get('reviews/pending', [ReviewController::class, 'pending'])->name('reviews.pending');
    Route::get('reviews/approved', [ReviewController::class, 'approved'])->name('reviews.approved');
    Route::resource('reviews', ReviewController::class)->except(['create', 'store']);
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::get('reviews/by-product/{product}', [ReviewController::class, 'byProduct'])->name('reviews.by-product');
    Route::get('reviews/by-rating/{rating}', [ReviewController::class, 'byRating'])->name('reviews.by-rating');
    Route::post('reviews/bulk-approve', [ReviewController::class, 'bulkApprove'])->name('reviews.bulk-approve');
    Route::post('reviews/bulk-reject', [ReviewController::class, 'bulkReject'])->name('reviews.bulk-reject');

    // =============================================================================
    // CONTENT MANAGEMENT
    // =============================================================================

    // Blog Categories
    Route::resource('blog-categories', BlogCategoryController::class);
    Route::get('blog-categories/{blogCategory}/posts', [BlogCategoryController::class, 'posts'])->name('blog-categories.posts');

    // Blog Tags
    Route::resource('blog-tags', BlogTagController::class);
    Route::get('blog-tags/{blogTag}/posts', [BlogTagController::class, 'posts'])->name('blog-tags.posts');
    Route::post('blog-tags/merge', [BlogTagController::class, 'merge'])->name('blog-tags.merge');

    // Blog Posts (static GETs declared first so they win over resource show route)
    Route::get('blog-posts/published', [BlogPostController::class, 'published'])->name('blog-posts.published');
    Route::get('blog-posts/draft', [BlogPostController::class, 'draft'])->name('blog-posts.draft');
    Route::resource('blog-posts', BlogPostController::class);
    Route::post('blog-posts/{blogPost}/publish', [BlogPostController::class, 'publish'])->name('blog-posts.publish');
    Route::post('blog-posts/{blogPost}/unpublish', [BlogPostController::class, 'unpublish'])->name('blog-posts.unpublish');
    Route::post('blog-posts/{blogPost}/duplicate', [BlogPostController::class, 'duplicate'])->name('blog-posts.duplicate');
    Route::get('blog-posts/by-author/{user}', [BlogPostController::class, 'byAuthor'])->name('blog-posts.by-author');

    // Pages (static GETs declared first so they win over resource show route)
    Route::get('pages/active', [PageController::class, 'active'])->name('pages.active');
    Route::get('pages/inactive', [PageController::class, 'inactive'])->name('pages.inactive');
    Route::resource('pages', PageController::class);
    Route::post('pages/{page}/toggle-status', [PageController::class, 'toggleStatus'])->name('pages.toggle-status');
    Route::post('pages/{page}/duplicate', [PageController::class, 'duplicate'])->name('pages.duplicate');

    // =============================================================================
    // NAVIGATION MANAGEMENT
    // =============================================================================

    // Menus
    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('menus.toggle-status');
    Route::get('menus/{menu}/items', [MenuController::class, 'items'])->name('menus.items');
    Route::post('menus/{menu}/items/reorder', [MenuController::class, 'reorderItems'])->name('menus.items.reorder');

    // Menu Items
    Route::resource('menu-items', MenuItemController::class);
    Route::post('menu-items/{menuItem}/toggle-status', [MenuItemController::class, 'toggleStatus'])->name('menu-items.toggle-status');
    Route::post('menu-items/reorder', [MenuItemController::class, 'reorder'])->name('menu-items.reorder');
    Route::get('menu-items/by-menu/{menu}', [MenuItemController::class, 'byMenu'])->name('menu-items.by-menu');
    Route::get('menu-items/tree/{menu}', [MenuItemController::class, 'tree'])->name('menu-items.tree');

    // =============================================================================
    // SLIDER MANAGEMENT
    // =============================================================================

    // Sliders
    Route::resource('sliders', SliderController::class);
    Route::get('sliders/{slider}/slides', [SliderController::class, 'slides'])->name('sliders.slides');
    Route::post('sliders/{slider}/slides/reorder', [SliderController::class, 'reorderSlides'])->name('sliders.slides.reorder');
    Route::post('sliders/{slider}/duplicate', [SliderController::class, 'duplicate'])->name('sliders.duplicate');

    // Slider Slides
    Route::resource('slider-slides', SliderSlideController::class);
    Route::post('slider-slides/reorder', [SliderSlideController::class, 'reorder'])->name('slider-slides.reorder');
    Route::get('slider-slides/by-slider/{slider}', [SliderSlideController::class, 'bySlider'])->name('slider-slides.by-slider');

    // =============================================================================
    // MEDIA MANAGEMENT
    // =============================================================================

    // Media
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('media/{media}/download', [MediaController::class, 'download'])->name('media.download');
    Route::post('media/bulk-delete', [MediaController::class, 'bulkDelete'])->name('media.bulk-delete');
    Route::get('media/by-type/{type}', [MediaController::class, 'byType'])->name('media.by-type');
    Route::get('media/by-folder/{folder}', [MediaController::class, 'byFolder'])->name('media.by-folder');
    Route::post('media/organize', [MediaController::class, 'organize'])->name('media.organize');

    // Entity Media
    Route::post('entity-media/{entityType}/{entityId}/bulk-attach', [EntityMediaController::class, 'bulkAttach'])->name('entity-media.bulk-attach');
    Route::delete('entity-media/{entityType}/{entityId}/clear-all', [EntityMediaController::class, 'clearAll'])->name('entity-media.clear-all');
    Route::patch('entity-media/{entityMedia}/zone', [EntityMediaController::class, 'updateZone'])->name('entity-media.update-zone');
    Route::get('entity-media/{entityType}/{entityId}/by-zone/{zone}', [EntityMediaController::class, 'byZone'])->name('entity-media.by-zone');
    Route::get('entity-media/{entityType}/{entityId}', [EntityMediaController::class, 'index'])->name('entity-media.index');
    Route::post('entity-media/{entityType}/{entityId}', [EntityMediaController::class, 'store'])->name('entity-media.store');
    Route::delete('entity-media/{entityMedia}', [EntityMediaController::class, 'destroy'])->name('entity-media.destroy');

    // =============================================================================
    // CUSTOMER MANAGEMENT
    // =============================================================================

    // Addresses
    Route::resource('addresses', AddressController::class)->except(['create', 'store']);
    Route::get('addresses/by-customer/{customer}', [AddressController::class, 'byCustomer'])->name('addresses.by-customer');
    Route::get('addresses/{address}/orders', [AddressController::class, 'orders'])->name('addresses.orders');

    // Default Addresses
    Route::get('default-addresses', [DefaultAddressController::class, 'index'])->name('default-addresses.index');
    Route::post('default-addresses/{customer}', [DefaultAddressController::class, 'store'])->name('default-addresses.store');
    Route::delete('default-addresses/{defaultAddress}', [DefaultAddressController::class, 'destroy'])->name('default-addresses.destroy');

    // Wish Lists
    Route::get('wish-lists', [WishListController::class, 'index'])->name('wish-lists.index');
    Route::get('wish-lists/popular-products', [WishListController::class, 'popularProducts'])->name('wish-lists.popular-products');
    Route::get('wish-lists/by-customer/{customer}', [WishListController::class, 'byCustomer'])->name('wish-lists.by-customer');
    Route::get('wish-lists/by-product/{product}', [WishListController::class, 'byProduct'])->name('wish-lists.by-product');
    Route::delete('wish-lists/{customer}/{product}', [WishListController::class, 'destroy'])->name('wish-lists.destroy');

    // Carts
    Route::get('carts', [CartController::class, 'index'])->name('carts.index');
    Route::get('carts/abandoned', [CartController::class, 'abandoned'])->name('carts.abandoned');
    Route::get('carts/statistics', [CartController::class, 'statistics'])->name('carts.statistics');
    Route::post('carts/cleanup-abandoned', [CartController::class, 'cleanupAbandoned'])->name('carts.cleanup-abandoned');
    Route::get('carts/{cart}', [CartController::class, 'show'])->name('carts.show');
    Route::delete('carts/{cart}', [CartController::class, 'destroy'])->name('carts.destroy');

    // =============================================================================
    // SYSTEM SETTINGS
    // =============================================================================

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/update-single', [SettingController::class, 'updateSingle'])->name('settings.update-single');
    Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
    Route::get('settings/mail', [SettingController::class, 'mail'])->name('settings.mail');
    Route::get('settings/payment', [SettingController::class, 'payment'])->name('settings.payment');
    Route::get('settings/shipping', [SettingController::class, 'shipping'])->name('settings.shipping');
    Route::get('settings/tax', [SettingController::class, 'tax'])->name('settings.tax');
    Route::get('settings/seo', [SettingController::class, 'seo'])->name('settings.seo');
    Route::get('settings/social', [SettingController::class, 'social'])->name('settings.social');
    Route::get('settings/analytics', [SettingController::class, 'analytics'])->name('settings.analytics');
    Route::post('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
    Route::post('settings/test-mail', [SettingController::class, 'testMail'])->name('settings.test-mail');

    // Currency Rates (static GETs declared first so they win over resource show route)
    Route::get('currency-rates/history', [CurrencyRateController::class, 'history'])->name('currency-rates.history');
    Route::resource('currency-rates', CurrencyRateController::class);
    Route::post('currency-rates/update-rates', [CurrencyRateController::class, 'updateRates'])->name('currency-rates.update-rates');
    Route::post('currency-rates/auto-update', [CurrencyRateController::class, 'autoUpdate'])->name('currency-rates.auto-update');

    // Meta Data
    Route::get('meta-data/{entityType}/{entityId}', [MetaDataController::class, 'show'])->name('meta-data.show');
    Route::post('meta-data/{entityType}/{entityId}', [MetaDataController::class, 'store'])->name('meta-data.store');
    Route::put('meta-data/{metaData}', [MetaDataController::class, 'update'])->name('meta-data.update');
    Route::delete('meta-data/{metaData}', [MetaDataController::class, 'destroy'])->name('meta-data.destroy');

    // =============================================================================
    // TRANSLATIONS
    // =============================================================================

    // Translations
    Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::get('translations/{entityType}/{entityId}', [TranslationController::class, 'show'])->name('translations.show');
    Route::post('translations/{entityType}/{entityId}', [TranslationController::class, 'store'])->name('translations.store');
    Route::put('translations/{translation}', [TranslationController::class, 'update'])->name('translations.update');
    Route::delete('translations/{translation}', [TranslationController::class, 'destroy'])->name('translations.destroy');
    Route::get('translations/missing', [TranslationController::class, 'missing'])->name('translations.missing');
    Route::post('translations/sync', [TranslationController::class, 'sync'])->name('translations.sync');
    Route::get('translations/export', [TranslationController::class, 'export'])->name('translations.export');
    Route::post('translations/import', [TranslationController::class, 'import'])->name('translations.import');

    // Translation Management (advanced UI backed by TranslationService)
    Route::get('translation-management', [TranslationManagementController::class, 'index'])->name('translation-management.index');
    Route::get('translation-management/create', [TranslationManagementController::class, 'create'])->name('translation-management.create');
    Route::post('translation-management', [TranslationManagementController::class, 'store'])->name('translation-management.store');
    Route::get('translation-management/stats', [TranslationManagementController::class, 'stats'])->name('translation-management.stats');
    Route::get('translation-management/missing', [TranslationManagementController::class, 'missing'])->name('translation-management.missing');
    Route::get('translation-management/import', [TranslationManagementController::class, 'import'])->name('translation-management.import');
    Route::post('translation-management/import', [TranslationManagementController::class, 'processImport'])->name('translation-management.import.process');
    Route::get('translation-management/export', [TranslationManagementController::class, 'export'])->name('translation-management.export');
    Route::post('translation-management/bulk-update', [TranslationManagementController::class, 'bulkUpdate'])->name('translation-management.bulk-update');
    Route::post('translation-management/cleanup', [TranslationManagementController::class, 'cleanup'])->name('translation-management.cleanup');
    Route::post('translation-management/duplicate-locale', [TranslationManagementController::class, 'duplicateLocale'])->name('translation-management.duplicate-locale');
    Route::get('translation-management/{translation}', [TranslationManagementController::class, 'show'])->name('translation-management.show');
    Route::get('translation-management/{translation}/edit', [TranslationManagementController::class, 'edit'])->name('translation-management.edit');
    Route::put('translation-management/{translation}', [TranslationManagementController::class, 'update'])->name('translation-management.update');
    Route::delete('translation-management/{translation}', [TranslationManagementController::class, 'destroy'])->name('translation-management.destroy');

    // Language Lines
    Route::resource('language-lines', LanguageLineController::class);
    Route::get('language-lines/by-group/{group}', [LanguageLineController::class, 'byGroup'])->name('language-lines.by-group');
    Route::post('language-lines/sync-from-files', [LanguageLineController::class, 'syncFromFiles'])->name('language-lines.sync-from-files');
    Route::get('language-lines/export/{group}', [LanguageLineController::class, 'export'])->name('language-lines.export');
    Route::post('language-lines/import', [LanguageLineController::class, 'import'])->name('language-lines.import');

    // =============================================================================
    // SEARCH & ANALYTICS
    // =============================================================================

    // Search Terms
    Route::get('search-terms', [SearchTermController::class, 'index'])->name('search-terms.index');
    Route::get('search-terms/popular', [SearchTermController::class, 'popular'])->name('search-terms.popular');
    Route::get('search-terms/no-results', [SearchTermController::class, 'noResults'])->name('search-terms.no-results');
    Route::get('search-terms/export', [SearchTermController::class, 'export'])->name('search-terms.export');
    Route::post('search-terms/cleanup', [SearchTermController::class, 'cleanup'])->name('search-terms.cleanup');
    Route::get('search-terms/{searchTerm}', [SearchTermController::class, 'show'])->name('search-terms.show');
    Route::delete('search-terms/{searchTerm}', [SearchTermController::class, 'destroy'])->name('search-terms.destroy');

    // =============================================================================
    // AUTHENTICATION & SECURITY
    // =============================================================================

    // Activations
    Route::get('activations', [ActivationController::class, 'index'])->name('activations.index');
    Route::get('activations/pending', [ActivationController::class, 'pending'])->name('activations.pending');
    Route::get('activations/completed', [ActivationController::class, 'completed'])->name('activations.completed');
    Route::get('activations/{activation}', [ActivationController::class, 'show'])->name('activations.show');
    Route::delete('activations/{activation}', [ActivationController::class, 'destroy'])->name('activations.destroy');
    Route::post('activations/cleanup', [ActivationController::class, 'cleanup'])->name('activations.cleanup');

    // Persistences
    Route::get('persistences', [PersistenceController::class, 'index'])->name('persistences.index');
    Route::delete('persistences/{persistence}', [PersistenceController::class, 'destroy'])->name('persistences.destroy');
    Route::get('persistences/by-user/{user}', [PersistenceController::class, 'byUser'])->name('persistences.by-user');
    Route::post('persistences/cleanup', [PersistenceController::class, 'cleanup'])->name('persistences.cleanup');
    Route::post('persistences/revoke-all/{user}', [PersistenceController::class, 'revokeAll'])->name('persistences.revoke-all');

    // Reminders
    Route::get('reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::delete('reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');
    Route::get('reminders/pending', [ReminderController::class, 'pending'])->name('reminders.pending');
    Route::get('reminders/completed', [ReminderController::class, 'completed'])->name('reminders.completed');
    Route::post('reminders/cleanup', [ReminderController::class, 'cleanup'])->name('reminders.cleanup');

    // Throttle
    Route::get('throttle', [ThrottleController::class, 'index'])->name('throttle.index');
    Route::delete('throttle/{throttle}', [ThrottleController::class, 'destroy'])->name('throttle.destroy');
    Route::get('throttle/by-ip/{ip}', [ThrottleController::class, 'byIp'])->name('throttle.by-ip');
    Route::get('throttle/by-user/{user}', [ThrottleController::class, 'byUser'])->name('throttle.by-user');
    Route::post('throttle/cleanup', [ThrottleController::class, 'cleanup'])->name('throttle.cleanup');
    Route::post('throttle/reset/{user}', [ThrottleController::class, 'reset'])->name('throttle.reset');

    // =============================================================================
    // SYSTEM MAINTENANCE
    // =============================================================================

    // Updater Scripts
    Route::get('updater-scripts', [UpdaterScriptController::class, 'index'])->name('updater-scripts.index');
    Route::post('updater-scripts/{updaterScript}/run', [UpdaterScriptController::class, 'run'])->name('updater-scripts.run');
    Route::get('updater-scripts/logs', [UpdaterScriptController::class, 'logs'])->name('updater-scripts.logs');
    Route::post('updater-scripts/cleanup-logs', [UpdaterScriptController::class, 'cleanupLogs'])->name('updater-scripts.cleanup-logs');

    // =============================================================================
    // REPORTS & ANALYTICS
    // =============================================================================

    // Sales Reports
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/sales/daily', [ReportController::class, 'dailySales'])->name('reports.sales.daily');
    Route::get('reports/sales/monthly', [ReportController::class, 'monthlySales'])->name('reports.sales.monthly');
    Route::get('reports/sales/yearly', [ReportController::class, 'yearlySales'])->name('reports.sales.yearly');
    Route::get('reports/sales/by-vendor', [ReportController::class, 'salesByVendor'])->name('reports.sales.by-vendor');
    Route::get('reports/sales/by-product', [ReportController::class, 'salesByProduct'])->name('reports.sales.by-product');
    Route::get('reports/sales/by-category', [ReportController::class, 'salesByCategory'])->name('reports.sales.by-category');

    // Product Reports
    Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('reports/products/best-selling', [ReportController::class, 'bestSellingProducts'])->name('reports.products.best-selling');
    Route::get('reports/products/low-stock', [ReportController::class, 'lowStockProducts'])->name('reports.products.low-stock');
    Route::get('reports/products/out-of-stock', [ReportController::class, 'outOfStockProducts'])->name('reports.products.out-of-stock');
    Route::get('reports/products/most-viewed', [ReportController::class, 'mostViewedProducts'])->name('reports.products.most-viewed');
    Route::get('reports/products/most-wished', [ReportController::class, 'mostWishedProducts'])->name('reports.products.most-wished');

    // Customer Reports
    Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
    Route::get('reports/customers/new', [ReportController::class, 'newCustomers'])->name('reports.customers.new');
    Route::get('reports/customers/top-spenders', [ReportController::class, 'topSpenders'])->name('reports.customers.top-spenders');
    Route::get('reports/customers/by-location', [ReportController::class, 'customersByLocation'])->name('reports.customers.by-location');

    // Vendor Reports
    Route::get('reports/vendors', [ReportController::class, 'vendors'])->name('reports.vendors');
    Route::get('reports/vendors/top-earners', [ReportController::class, 'topEarningVendors'])->name('reports.vendors.top-earners');
    Route::get('reports/vendors/commission', [ReportController::class, 'vendorCommission'])->name('reports.vendors.commission');
    Route::get('reports/vendors/performance', [ReportController::class, 'vendorPerformance'])->name('reports.vendors.performance');

    // Order Reports
    Route::get('reports/orders', [ReportController::class, 'orders'])->name('reports.orders');
    Route::get('reports/orders/by-status', [ReportController::class, 'ordersByStatus'])->name('reports.orders.by-status');
    Route::get('reports/orders/by-payment-method', [ReportController::class, 'ordersByPaymentMethod'])->name('reports.orders.by-payment-method');
    Route::get('reports/orders/abandoned-carts', [ReportController::class, 'abandonedCarts'])->name('reports.orders.abandoned-carts');

    // Tax Reports
    Route::get('reports/taxes', [ReportController::class, 'taxes'])->name('reports.taxes');
    Route::get('reports/taxes/collected', [ReportController::class, 'taxesCollected'])->name('reports.taxes.collected');
    Route::get('reports/taxes/by-region', [ReportController::class, 'taxesByRegion'])->name('reports.taxes.by-region');

    // Review Reports
    Route::get('reports/reviews', [ReportController::class, 'reviews'])->name('reports.reviews');
    Route::get('reports/reviews/pending', [ReportController::class, 'pendingReviews'])->name('reports.reviews.pending');
    Route::get('reports/reviews/by-rating', [ReportController::class, 'reviewsByRating'])->name('reports.reviews.by-rating');

    // Export Routes
    Route::post('reports/export/sales', [ReportController::class, 'exportSales'])->name('reports.export.sales');
    Route::post('reports/export/products', [ReportController::class, 'exportProducts'])->name('reports.export.products');
    Route::post('reports/export/customers', [ReportController::class, 'exportCustomers'])->name('reports.export.customers');
    Route::post('reports/export/vendors', [ReportController::class, 'exportVendors'])->name('reports.export.vendors');
    Route::post('reports/export/orders', [ReportController::class, 'exportOrders'])->name('reports.export.orders');

    // =============================================================================
    // INVENTORY / STOCK MANAGEMENT
    // =============================================================================

    // Inventory Dashboard
    Route::get('inventory', [InventoryDashboardController::class, 'index'])->name('inventory.dashboard');

    // Warehouses
    Route::resource('warehouses', WarehouseController::class);

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Product Stocks (on-hand)
    Route::get('product-stocks/low-stock', [ProductStockController::class, 'lowStock'])->name('product-stocks.low-stock');
    Route::get('product-stocks/out-of-stock', [ProductStockController::class, 'outOfStock'])->name('product-stocks.out-of-stock');
    Route::get('product-stocks', [ProductStockController::class, 'index'])->name('product-stocks.index');
    Route::get('product-stocks/{productStock}', [ProductStockController::class, 'show'])->name('product-stocks.show');
    Route::get('product-stocks/{productStock}/edit', [ProductStockController::class, 'edit'])->name('product-stocks.edit');
    Route::put('product-stocks/{productStock}', [ProductStockController::class, 'update'])->name('product-stocks.update');

    // Stock Movements (read-only ledger)
    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('stock-movements/{stockMovement}', [StockMovementController::class, 'show'])->name('stock-movements.show');

    // Stock Adjustments
    Route::post('stock-adjustments/{stockAdjustment}/post', [StockAdjustmentController::class, 'post'])->name('stock-adjustments.post');
    Route::post('stock-adjustments/{stockAdjustment}/cancel', [StockAdjustmentController::class, 'cancel'])->name('stock-adjustments.cancel');
    Route::resource('stock-adjustments', StockAdjustmentController::class);

    // Stock Transfers
    Route::post('stock-transfers/{stockTransfer}/ship', [StockTransferController::class, 'ship'])->name('stock-transfers.ship');
    Route::post('stock-transfers/{stockTransfer}/receive', [StockTransferController::class, 'receive'])->name('stock-transfers.receive');
    Route::post('stock-transfers/{stockTransfer}/cancel', [StockTransferController::class, 'cancel'])->name('stock-transfers.cancel');
    Route::resource('stock-transfers', StockTransferController::class);

    // Purchase Orders
    Route::post('purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])->name('purchase-orders.send');
    Route::post('purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    // Goods Receipts (GRN)
    Route::post('goods-receipts/{goodsReceipt}/post', [GoodsReceiptController::class, 'post'])->name('goods-receipts.post');
    Route::post('goods-receipts/{goodsReceipt}/cancel', [GoodsReceiptController::class, 'cancel'])->name('goods-receipts.cancel');
    Route::resource('goods-receipts', GoodsReceiptController::class);

    // Stock Takes / Cycle Counts
    Route::post('stock-takes/{stockTake}/complete', [StockTakeController::class, 'complete'])->name('stock-takes.complete');
    Route::post('stock-takes/{stockTake}/cancel', [StockTakeController::class, 'cancel'])->name('stock-takes.cancel');
    Route::resource('stock-takes', StockTakeController::class);
});
