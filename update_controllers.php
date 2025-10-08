<?php

// Simple script to show what controllers have been updated and what still need updates
$controllersDir = 'app/Http/Controllers/Backend/';
$controllers = [
    'AttributeController.php' => 'attribute',
    'AttributeSetController.php' => 'attribute_set',
    'BlogCategoryController.php' => 'blog_category',
    'BlogPostController.php' => 'blog_post',
    'BlogTagController.php' => 'blog_tag',
    'CurrencyRateController.php' => 'currency_rate',
    'FlashSaleController.php' => 'flash_sale',
    'MenuController.php' => 'menu',
    'OptionController.php' => 'option',
    'PageController.php' => 'page',
    'SliderController.php' => 'slider',
    'TagController.php' => 'tag',
    'TransactionController.php' => 'transaction',
    'VariationController.php' => 'variation',
];

echo "Controllers that need basic permission updates:\n";
foreach ($controllers as $file => $resource) {
    echo "- {$file} (resource: {$resource})\n";
}

echo "\nControllers already updated with permissions:\n";
$updated = [
    'BaseController.php' => 'base controller',
    'UserController.php' => 'user management',
    'RoleController.php' => 'role management',
    'PermissionController.php' => 'permission management',
    'VendorController.php' => 'vendor management',
    'ProductController.php' => 'product management',
    'CategoryController.php' => 'category management',
    'BrandController.php' => 'brand management',
    'OrderController.php' => 'order management',
    'CouponController.php' => 'coupon management',
    'VendorPayoutController.php' => 'payout management',
    'VendorWithdrawalController.php' => 'withdrawal management',
    'DashboardController.php' => 'dashboard access',
    'MediaController.php' => 'media management',
    'ReviewController.php' => 'review management',
    'SettingController.php' => 'settings management',
    'TaxClassController.php' => 'tax class management',
    'TaxRateController.php' => 'tax rate management',
];

foreach ($updated as $file => $description) {
    echo "✓ {$file} ({$description})\n";
}
