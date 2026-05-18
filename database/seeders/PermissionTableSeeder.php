<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard & Core Access
            ['title' => 'dashboard_access', 'group' => 'Dashboard'],

            // User Management
            ['title' => 'user_management_access', 'group' => 'User Management'],
            ['title' => 'user_create', 'group' => 'User'],
            ['title' => 'user_edit', 'group' => 'User'],
            ['title' => 'user_show', 'group' => 'User'],
            ['title' => 'user_delete', 'group' => 'User'],
            ['title' => 'user_access', 'group' => 'User'],

            // Role Management
            ['title' => 'role_create', 'group' => 'Role'],
            ['title' => 'role_edit', 'group' => 'Role'],
            ['title' => 'role_show', 'group' => 'Role'],
            ['title' => 'role_delete', 'group' => 'Role'],
            ['title' => 'role_access', 'group' => 'Role'],

            // Permission Management
            ['title' => 'permission_create', 'group' => 'Permission'],
            ['title' => 'permission_edit', 'group' => 'Permission'],
            ['title' => 'permission_show', 'group' => 'Permission'],
            ['title' => 'permission_delete', 'group' => 'Permission'],
            ['title' => 'permission_access', 'group' => 'Permission'],

            // Vendor Management
            ['title' => 'vendor_management_access', 'group' => 'Vendor Management'],
            ['title' => 'vendor_create', 'group' => 'Vendor'],
            ['title' => 'vendor_edit', 'group' => 'Vendor'],
            ['title' => 'vendor_show', 'group' => 'Vendor'],
            ['title' => 'vendor_delete', 'group' => 'Vendor'],
            ['title' => 'vendor_access', 'group' => 'Vendor'],

            // Vendor Payouts
            ['title' => 'vendor_payout_create', 'group' => 'Vendor Payout'],
            ['title' => 'vendor_payout_edit', 'group' => 'Vendor Payout'],
            ['title' => 'vendor_payout_show', 'group' => 'Vendor Payout'],
            ['title' => 'vendor_payout_delete', 'group' => 'Vendor Payout'],
            ['title' => 'vendor_payout_access', 'group' => 'Vendor Payout'],

            // Vendor Withdrawals
            ['title' => 'vendor_withdrawal_create', 'group' => 'Vendor Withdrawal'],
            ['title' => 'vendor_withdrawal_edit', 'group' => 'Vendor Withdrawal'],
            ['title' => 'vendor_withdrawal_show', 'group' => 'Vendor Withdrawal'],
            ['title' => 'vendor_withdrawal_delete', 'group' => 'Vendor Withdrawal'],
            ['title' => 'vendor_withdrawal_access', 'group' => 'Vendor Withdrawal'],

            // Product Management
            ['title' => 'product_management_access', 'group' => 'Product Management'],
            ['title' => 'product_create', 'group' => 'Product'],
            ['title' => 'product_edit', 'group' => 'Product'],
            ['title' => 'product_show', 'group' => 'Product'],
            ['title' => 'product_delete', 'group' => 'Product'],
            ['title' => 'product_access', 'group' => 'Product'],

            // Category Management
            ['title' => 'category_create', 'group' => 'Category'],
            ['title' => 'category_edit', 'group' => 'Category'],
            ['title' => 'category_show', 'group' => 'Category'],
            ['title' => 'category_delete', 'group' => 'Category'],
            ['title' => 'category_access', 'group' => 'Category'],

            // Brand Management
            ['title' => 'brand_create', 'group' => 'Brand'],
            ['title' => 'brand_edit', 'group' => 'Brand'],
            ['title' => 'brand_show', 'group' => 'Brand'],
            ['title' => 'brand_delete', 'group' => 'Brand'],
            ['title' => 'brand_access', 'group' => 'Brand'],

            // Attribute Management
            ['title' => 'attribute_create', 'group' => 'Attribute'],
            ['title' => 'attribute_edit', 'group' => 'Attribute'],
            ['title' => 'attribute_show', 'group' => 'Attribute'],
            ['title' => 'attribute_delete', 'group' => 'Attribute'],
            ['title' => 'attribute_access', 'group' => 'Attribute'],

            // Attribute Set Management
            ['title' => 'attribute_set_create', 'group' => 'Attribute Set'],
            ['title' => 'attribute_set_edit', 'group' => 'Attribute Set'],
            ['title' => 'attribute_set_show', 'group' => 'Attribute Set'],
            ['title' => 'attribute_set_delete', 'group' => 'Attribute Set'],
            ['title' => 'attribute_set_access', 'group' => 'Attribute Set'],

            // Variation Management
            ['title' => 'variation_create', 'group' => 'Variation'],
            ['title' => 'variation_edit', 'group' => 'Variation'],
            ['title' => 'variation_show', 'group' => 'Variation'],
            ['title' => 'variation_delete', 'group' => 'Variation'],
            ['title' => 'variation_access', 'group' => 'Variation'],

            // Option Management
            ['title' => 'option_create', 'group' => 'Option'],
            ['title' => 'option_edit', 'group' => 'Option'],
            ['title' => 'option_show', 'group' => 'Option'],
            ['title' => 'option_delete', 'group' => 'Option'],
            ['title' => 'option_access', 'group' => 'Option'],

            // Tag Management
            ['title' => 'tag_create', 'group' => 'Tag'],
            ['title' => 'tag_edit', 'group' => 'Tag'],
            ['title' => 'tag_show', 'group' => 'Tag'],
            ['title' => 'tag_delete', 'group' => 'Tag'],
            ['title' => 'tag_access', 'group' => 'Tag'],

            // Order Management
            ['title' => 'order_management_access', 'group' => 'Order Management'],
            ['title' => 'order_create', 'group' => 'Order'],
            ['title' => 'order_edit', 'group' => 'Order'],
            ['title' => 'order_show', 'group' => 'Order'],
            ['title' => 'order_delete', 'group' => 'Order'],
            ['title' => 'order_access', 'group' => 'Order'],

            // Transaction Management
            ['title' => 'transaction_create', 'group' => 'Transaction'],
            ['title' => 'transaction_edit', 'group' => 'Transaction'],
            ['title' => 'transaction_show', 'group' => 'Transaction'],
            ['title' => 'transaction_delete', 'group' => 'Transaction'],
            ['title' => 'transaction_access', 'group' => 'Transaction'],

            // Coupon Management
            ['title' => 'promotion_management_access', 'group' => 'Promotion Management'],
            ['title' => 'coupon_create', 'group' => 'Coupon'],
            ['title' => 'coupon_edit', 'group' => 'Coupon'],
            ['title' => 'coupon_show', 'group' => 'Coupon'],
            ['title' => 'coupon_delete', 'group' => 'Coupon'],
            ['title' => 'coupon_access', 'group' => 'Coupon'],

            // Flash Sale Management
            ['title' => 'flash_sale_create', 'group' => 'Flash Sale'],
            ['title' => 'flash_sale_edit', 'group' => 'Flash Sale'],
            ['title' => 'flash_sale_show', 'group' => 'Flash Sale'],
            ['title' => 'flash_sale_delete', 'group' => 'Flash Sale'],
            ['title' => 'flash_sale_access', 'group' => 'Flash Sale'],

            // Review Management
            ['title' => 'review_management_access', 'group' => 'Review Management'],
            ['title' => 'review_create', 'group' => 'Review'],
            ['title' => 'review_edit', 'group' => 'Review'],
            ['title' => 'review_show', 'group' => 'Review'],
            ['title' => 'review_delete', 'group' => 'Review'],
            ['title' => 'review_access', 'group' => 'Review'],

            // Content Management
            ['title' => 'content_management_access', 'group' => 'Content Management'],

            // Blog Management
            ['title' => 'blog_access', 'group' => 'Blog'],
            ['title' => 'blog_create', 'group' => 'Blog'],
            ['title' => 'blog_edit', 'group' => 'Blog'],
            ['title' => 'blog_show', 'group' => 'Blog'],
            ['title' => 'blog_delete', 'group' => 'Blog'],

            // Page Management
            ['title' => 'page_create', 'group' => 'Page'],
            ['title' => 'page_edit', 'group' => 'Page'],
            ['title' => 'page_show', 'group' => 'Page'],
            ['title' => 'page_delete', 'group' => 'Page'],
            ['title' => 'page_access', 'group' => 'Page'],

            // Menu Management
            ['title' => 'menu_create', 'group' => 'Menu'],
            ['title' => 'menu_edit', 'group' => 'Menu'],
            ['title' => 'menu_show', 'group' => 'Menu'],
            ['title' => 'menu_delete', 'group' => 'Menu'],
            ['title' => 'menu_access', 'group' => 'Menu'],

            // Slider Management
            ['title' => 'slider_create', 'group' => 'Slider'],
            ['title' => 'slider_edit', 'group' => 'Slider'],
            ['title' => 'slider_show', 'group' => 'Slider'],
            ['title' => 'slider_delete', 'group' => 'Slider'],
            ['title' => 'slider_access', 'group' => 'Slider'],

            // Media Management
            ['title' => 'media_management_access', 'group' => 'Media Management'],
            ['title' => 'media_create', 'group' => 'Media'],
            ['title' => 'media_edit', 'group' => 'Media'],
            ['title' => 'media_show', 'group' => 'Media'],
            ['title' => 'media_delete', 'group' => 'Media'],
            ['title' => 'media_access', 'group' => 'Media'],

            // Tax Management
            ['title' => 'tax_management_access', 'group' => 'Tax Management'],
            ['title' => 'tax_class_create', 'group' => 'Tax Class'],
            ['title' => 'tax_class_edit', 'group' => 'Tax Class'],
            ['title' => 'tax_class_show', 'group' => 'Tax Class'],
            ['title' => 'tax_class_delete', 'group' => 'Tax Class'],
            ['title' => 'tax_class_access', 'group' => 'Tax Class'],

            ['title' => 'tax_rate_create', 'group' => 'Tax Rate'],
            ['title' => 'tax_rate_edit', 'group' => 'Tax Rate'],
            ['title' => 'tax_rate_show', 'group' => 'Tax Rate'],
            ['title' => 'tax_rate_delete', 'group' => 'Tax Rate'],
            ['title' => 'tax_rate_access', 'group' => 'Tax Rate'],

            // System Settings
            ['title' => 'system_management_access', 'group' => 'System Management'],
            ['title' => 'setting_create', 'group' => 'Setting'],
            ['title' => 'setting_edit', 'group' => 'Setting'],
            ['title' => 'setting_show', 'group' => 'Setting'],
            ['title' => 'setting_delete', 'group' => 'Setting'],
            ['title' => 'setting_access', 'group' => 'Setting'],

            // Currency Rate Management
            ['title' => 'currency_rate_create', 'group' => 'Currency Rate'],
            ['title' => 'currency_rate_edit', 'group' => 'Currency Rate'],
            ['title' => 'currency_rate_show', 'group' => 'Currency Rate'],
            ['title' => 'currency_rate_delete', 'group' => 'Currency Rate'],
            ['title' => 'currency_rate_access', 'group' => 'Currency Rate'],

            // Profile Management
            ['title' => 'profile_password_edit', 'group' => 'Profile'],
            ['title' => 'profile_edit', 'group' => 'Profile'],
            ['title' => 'profile_show', 'group' => 'Profile'],

            // Blog Category Management
            ['title' => 'blog_category_create', 'group' => 'Blog Category'],
            ['title' => 'blog_category_edit', 'group' => 'Blog Category'],
            ['title' => 'blog_category_show', 'group' => 'Blog Category'],
            ['title' => 'blog_category_delete', 'group' => 'Blog Category'],
            ['title' => 'blog_category_access', 'group' => 'Blog Category'],

            // Blog Tag Management
            ['title' => 'blog_tag_create', 'group' => 'Blog Tag'],
            ['title' => 'blog_tag_edit', 'group' => 'Blog Tag'],
            ['title' => 'blog_tag_show', 'group' => 'Blog Tag'],
            ['title' => 'blog_tag_delete', 'group' => 'Blog Tag'],
            ['title' => 'blog_tag_access', 'group' => 'Blog Tag'],

            // Blog Post Management
            ['title' => 'blog_post_create', 'group' => 'Blog Post'],
            ['title' => 'blog_post_edit', 'group' => 'Blog Post'],
            ['title' => 'blog_post_show', 'group' => 'Blog Post'],
            ['title' => 'blog_post_delete', 'group' => 'Blog Post'],
            ['title' => 'blog_post_access', 'group' => 'Blog Post'],

            // Menu Item Management
            ['title' => 'menu_item_create', 'group' => 'Menu Item'],
            ['title' => 'menu_item_edit', 'group' => 'Menu Item'],
            ['title' => 'menu_item_show', 'group' => 'Menu Item'],
            ['title' => 'menu_item_delete', 'group' => 'Menu Item'],
            ['title' => 'menu_item_access', 'group' => 'Menu Item'],

            // Slider Slide Management
            ['title' => 'slider_slide_create', 'group' => 'Slider Slide'],
            ['title' => 'slider_slide_edit', 'group' => 'Slider Slide'],
            ['title' => 'slider_slide_show', 'group' => 'Slider Slide'],
            ['title' => 'slider_slide_delete', 'group' => 'Slider Slide'],
            ['title' => 'slider_slide_access', 'group' => 'Slider Slide'],

            // Entity Media Management
            ['title' => 'entity_media_create', 'group' => 'Entity Media'],
            ['title' => 'entity_media_edit', 'group' => 'Entity Media'],
            ['title' => 'entity_media_show', 'group' => 'Entity Media'],
            ['title' => 'entity_media_delete', 'group' => 'Entity Media'],
            ['title' => 'entity_media_access', 'group' => 'Entity Media'],

            // Meta Data Management
            ['title' => 'meta_data_create', 'group' => 'Meta Data'],
            ['title' => 'meta_data_edit', 'group' => 'Meta Data'],
            ['title' => 'meta_data_show', 'group' => 'Meta Data'],
            ['title' => 'meta_data_delete', 'group' => 'Meta Data'],
            ['title' => 'meta_data_access', 'group' => 'Meta Data'],

            // Attribute Value Management
            ['title' => 'attribute_value_create', 'group' => 'Attribute Value'],
            ['title' => 'attribute_value_edit', 'group' => 'Attribute Value'],
            ['title' => 'attribute_value_show', 'group' => 'Attribute Value'],
            ['title' => 'attribute_value_delete', 'group' => 'Attribute Value'],
            ['title' => 'attribute_value_access', 'group' => 'Attribute Value'],

            // Variation Value Management
            ['title' => 'variation_value_create', 'group' => 'Variation Value'],
            ['title' => 'variation_value_edit', 'group' => 'Variation Value'],
            ['title' => 'variation_value_show', 'group' => 'Variation Value'],
            ['title' => 'variation_value_delete', 'group' => 'Variation Value'],
            ['title' => 'variation_value_access', 'group' => 'Variation Value'],

            // Option Value Management
            ['title' => 'option_value_create', 'group' => 'Option Value'],
            ['title' => 'option_value_edit', 'group' => 'Option Value'],
            ['title' => 'option_value_show', 'group' => 'Option Value'],
            ['title' => 'option_value_delete', 'group' => 'Option Value'],
            ['title' => 'option_value_access', 'group' => 'Option Value'],

            // Product Variant Management
            ['title' => 'product_variant_create', 'group' => 'Product Variant'],
            ['title' => 'product_variant_edit', 'group' => 'Product Variant'],
            ['title' => 'product_variant_show', 'group' => 'Product Variant'],
            ['title' => 'product_variant_delete', 'group' => 'Product Variant'],
            ['title' => 'product_variant_access', 'group' => 'Product Variant'],

            // Related Product Management
            ['title' => 'related_product_create', 'group' => 'Related Product'],
            ['title' => 'related_product_edit', 'group' => 'Related Product'],
            ['title' => 'related_product_show', 'group' => 'Related Product'],
            ['title' => 'related_product_delete', 'group' => 'Related Product'],
            ['title' => 'related_product_access', 'group' => 'Related Product'],

            // Up Sell Product Management
            ['title' => 'up_sell_product_create', 'group' => 'Up Sell Product'],
            ['title' => 'up_sell_product_edit', 'group' => 'Up Sell Product'],
            ['title' => 'up_sell_product_show', 'group' => 'Up Sell Product'],
            ['title' => 'up_sell_product_delete', 'group' => 'Up Sell Product'],
            ['title' => 'up_sell_product_access', 'group' => 'Up Sell Product'],

            // Cross Sell Product Management
            ['title' => 'cross_sell_product_create', 'group' => 'Cross Sell Product'],
            ['title' => 'cross_sell_product_edit', 'group' => 'Cross Sell Product'],
            ['title' => 'cross_sell_product_show', 'group' => 'Cross Sell Product'],
            ['title' => 'cross_sell_product_delete', 'group' => 'Cross Sell Product'],
            ['title' => 'cross_sell_product_access', 'group' => 'Cross Sell Product'],

            // Order Product Management
            ['title' => 'order_product_create', 'group' => 'Order Product'],
            ['title' => 'order_product_edit', 'group' => 'Order Product'],
            ['title' => 'order_product_show', 'group' => 'Order Product'],
            ['title' => 'order_product_delete', 'group' => 'Order Product'],
            ['title' => 'order_product_access', 'group' => 'Order Product'],

            // Vendor Order Management
            ['title' => 'vendor_order_create', 'group' => 'Vendor Order'],
            ['title' => 'vendor_order_edit', 'group' => 'Vendor Order'],
            ['title' => 'vendor_order_show', 'group' => 'Vendor Order'],
            ['title' => 'vendor_order_delete', 'group' => 'Vendor Order'],
            ['title' => 'vendor_order_access', 'group' => 'Vendor Order'],

            // Vendor Review Management
            ['title' => 'vendor_review_create', 'group' => 'Vendor Review'],
            ['title' => 'vendor_review_edit', 'group' => 'Vendor Review'],
            ['title' => 'vendor_review_show', 'group' => 'Vendor Review'],
            ['title' => 'vendor_review_delete', 'group' => 'Vendor Review'],
            ['title' => 'vendor_review_access', 'group' => 'Vendor Review'],

            // Flash Sale Product Management
            ['title' => 'flash_sale_product_create', 'group' => 'Flash Sale Product'],
            ['title' => 'flash_sale_product_edit', 'group' => 'Flash Sale Product'],
            ['title' => 'flash_sale_product_show', 'group' => 'Flash Sale Product'],
            ['title' => 'flash_sale_product_delete', 'group' => 'Flash Sale Product'],
            ['title' => 'flash_sale_product_access', 'group' => 'Flash Sale Product'],

            // Vendor Setting Management
            ['title' => 'vendor_setting_create', 'group' => 'Vendor Setting'],
            ['title' => 'vendor_setting_edit', 'group' => 'Vendor Setting'],
            ['title' => 'vendor_setting_show', 'group' => 'Vendor Setting'],
            ['title' => 'vendor_setting_delete', 'group' => 'Vendor Setting'],
            ['title' => 'vendor_setting_access', 'group' => 'Vendor Setting'],

            // Address Management
            ['title' => 'address_create', 'group' => 'Address'],
            ['title' => 'address_edit', 'group' => 'Address'],
            ['title' => 'address_show', 'group' => 'Address'],
            ['title' => 'address_delete', 'group' => 'Address'],
            ['title' => 'address_access', 'group' => 'Address'],

            // Default Address Management
            ['title' => 'default_address_create', 'group' => 'Default Address'],
            ['title' => 'default_address_edit', 'group' => 'Default Address'],
            ['title' => 'default_address_show', 'group' => 'Default Address'],
            ['title' => 'default_address_delete', 'group' => 'Default Address'],
            ['title' => 'default_address_access', 'group' => 'Default Address'],

            // Wish List Management
            ['title' => 'wish_list_create', 'group' => 'Wish List'],
            ['title' => 'wish_list_edit', 'group' => 'Wish List'],
            ['title' => 'wish_list_show', 'group' => 'Wish List'],
            ['title' => 'wish_list_delete', 'group' => 'Wish List'],
            ['title' => 'wish_list_access', 'group' => 'Wish List'],

            // Cart Management
            ['title' => 'cart_create', 'group' => 'Cart'],
            ['title' => 'cart_edit', 'group' => 'Cart'],
            ['title' => 'cart_show', 'group' => 'Cart'],
            ['title' => 'cart_delete', 'group' => 'Cart'],
            ['title' => 'cart_access', 'group' => 'Cart'],

            // Search Term Management
            ['title' => 'search_term_create', 'group' => 'Search Term'],
            ['title' => 'search_term_edit', 'group' => 'Search Term'],
            ['title' => 'search_term_show', 'group' => 'Search Term'],
            ['title' => 'search_term_delete', 'group' => 'Search Term'],
            ['title' => 'search_term_access', 'group' => 'Search Term'],

            // Vendor Notification Management
            ['title' => 'vendor_notification_create', 'group' => 'Vendor Notification'],
            ['title' => 'vendor_notification_edit', 'group' => 'Vendor Notification'],
            ['title' => 'vendor_notification_show', 'group' => 'Vendor Notification'],
            ['title' => 'vendor_notification_delete', 'group' => 'Vendor Notification'],
            ['title' => 'vendor_notification_access', 'group' => 'Vendor Notification'],

            // Vendor Shipping Zone Management
            ['title' => 'vendor_shipping_zone_create', 'group' => 'Vendor Shipping Zone'],
            ['title' => 'vendor_shipping_zone_edit', 'group' => 'Vendor Shipping Zone'],
            ['title' => 'vendor_shipping_zone_show', 'group' => 'Vendor Shipping Zone'],
            ['title' => 'vendor_shipping_zone_delete', 'group' => 'Vendor Shipping Zone'],
            ['title' => 'vendor_shipping_zone_access', 'group' => 'Vendor Shipping Zone'],

            // Authentication System Management
            ['title' => 'activation_create', 'group' => 'Activation'],
            ['title' => 'activation_edit', 'group' => 'Activation'],
            ['title' => 'activation_show', 'group' => 'Activation'],
            ['title' => 'activation_delete', 'group' => 'Activation'],
            ['title' => 'activation_access', 'group' => 'Activation'],

            ['title' => 'persistence_create', 'group' => 'Persistence'],
            ['title' => 'persistence_edit', 'group' => 'Persistence'],
            ['title' => 'persistence_show', 'group' => 'Persistence'],
            ['title' => 'persistence_delete', 'group' => 'Persistence'],
            ['title' => 'persistence_access', 'group' => 'Persistence'],

            ['title' => 'reminder_create', 'group' => 'Reminder'],
            ['title' => 'reminder_edit', 'group' => 'Reminder'],
            ['title' => 'reminder_show', 'group' => 'Reminder'],
            ['title' => 'reminder_delete', 'group' => 'Reminder'],
            ['title' => 'reminder_access', 'group' => 'Reminder'],

            ['title' => 'throttle_create', 'group' => 'Throttle'],
            ['title' => 'throttle_edit', 'group' => 'Throttle'],
            ['title' => 'throttle_show', 'group' => 'Throttle'],
            ['title' => 'throttle_delete', 'group' => 'Throttle'],
            ['title' => 'throttle_access', 'group' => 'Throttle'],

            // Translation Management
            ['title' => 'translation_create', 'group' => 'Translation'],
            ['title' => 'translation_edit', 'group' => 'Translation'],
            ['title' => 'translation_show', 'group' => 'Translation'],
            ['title' => 'translation_delete', 'group' => 'Translation'],
            ['title' => 'translation_access', 'group' => 'Translation'],

            // Language Line Management
            ['title' => 'language_line_create', 'group' => 'Language Line'],
            ['title' => 'language_line_edit', 'group' => 'Language Line'],
            ['title' => 'language_line_show', 'group' => 'Language Line'],
            ['title' => 'language_line_delete', 'group' => 'Language Line'],
            ['title' => 'language_line_access', 'group' => 'Language Line'],

            // Updater Script Management
            ['title' => 'updater_script_create', 'group' => 'Updater Script'],
            ['title' => 'updater_script_edit', 'group' => 'Updater Script'],
            ['title' => 'updater_script_show', 'group' => 'Updater Script'],
            ['title' => 'updater_script_delete', 'group' => 'Updater Script'],
            ['title' => 'updater_script_access', 'group' => 'Updater Script'],

            // OTP Verification Management
            ['title' => 'otp_verification_create', 'group' => 'OTP Verification'],
            ['title' => 'otp_verification_edit', 'group' => 'OTP Verification'],
            ['title' => 'otp_verification_show', 'group' => 'OTP Verification'],
            ['title' => 'otp_verification_delete', 'group' => 'OTP Verification'],
            ['title' => 'otp_verification_access', 'group' => 'OTP Verification'],

            // Group-level "management_access" permissions used by BaseController $additionalPermissions
            ['title' => 'activation_management_access', 'group' => 'Activation'],
            ['title' => 'address_management_access', 'group' => 'Address'],
            ['title' => 'attribute_management_access', 'group' => 'Attribute'],
            ['title' => 'attribute_set_management_access', 'group' => 'Attribute Set'],
            ['title' => 'attribute_value_management_access', 'group' => 'Attribute Value'],
            ['title' => 'brand_management_access', 'group' => 'Brand'],
            ['title' => 'cart_management_access', 'group' => 'Cart'],
            ['title' => 'customer_management_access', 'group' => 'User Management'],
            ['title' => 'flash_sale_product_management_access', 'group' => 'Flash Sale Product'],
            ['title' => 'menu_item_management_access', 'group' => 'Menu Item'],
            ['title' => 'meta_data_management_access', 'group' => 'Meta Data'],
            ['title' => 'option_management_access', 'group' => 'Option'],
            ['title' => 'option_value_management_access', 'group' => 'Option Value'],
            ['title' => 'order_download_management_access', 'group' => 'Order Download'],
            ['title' => 'order_product_management_access', 'group' => 'Order Product'],
            ['title' => 'order_product_option_management_access', 'group' => 'Order Product Option'],
            ['title' => 'order_product_variation_management_access', 'group' => 'Order Product Variation'],
            ['title' => 'otp_verification_management_access', 'group' => 'OTP Verification'],
            ['title' => 'product_attribute_management_access', 'group' => 'Product Attribute'],
            ['title' => 'product_variant_management_access', 'group' => 'Product Variant'],
            ['title' => 'report_access', 'group' => 'Report'],
            ['title' => 'search_term_management_access', 'group' => 'Search Term'],
            ['title' => 'security_management_access', 'group' => 'System Management'],
            ['title' => 'session_management_access', 'group' => 'System Management'],
            ['title' => 'slider_slide_management_access', 'group' => 'Slider Slide'],
            ['title' => 'system_settings_access', 'group' => 'System Management'],
            ['title' => 'tag_management_access', 'group' => 'Tag'],
            ['title' => 'translation_management_access', 'group' => 'Translation'],
            ['title' => 'variation_management_access', 'group' => 'Variation'],
            ['title' => 'variation_value_management_access', 'group' => 'Variation Value'],
            ['title' => 'vendor_notification_management_access', 'group' => 'Vendor Notification'],
            ['title' => 'vendor_order_management_access', 'group' => 'Vendor Order'],
            ['title' => 'vendor_review_management_access', 'group' => 'Vendor Review'],
            ['title' => 'vendor_setting_management_access', 'group' => 'Vendor Setting'],
            ['title' => 'vendor_shipping_zone_management_access', 'group' => 'Vendor Shipping Zone'],

            // Product Attribute CRUD
            ['title' => 'product_attribute_create', 'group' => 'Product Attribute'],
            ['title' => 'product_attribute_edit', 'group' => 'Product Attribute'],
            ['title' => 'product_attribute_show', 'group' => 'Product Attribute'],
            ['title' => 'product_attribute_delete', 'group' => 'Product Attribute'],
            ['title' => 'product_attribute_access', 'group' => 'Product Attribute'],

            // Order Download CRUD
            ['title' => 'order_download_create', 'group' => 'Order Download'],
            ['title' => 'order_download_edit', 'group' => 'Order Download'],
            ['title' => 'order_download_show', 'group' => 'Order Download'],
            ['title' => 'order_download_delete', 'group' => 'Order Download'],
            ['title' => 'order_download_access', 'group' => 'Order Download'],

            // Order Product Option CRUD
            ['title' => 'order_product_option_create', 'group' => 'Order Product Option'],
            ['title' => 'order_product_option_edit', 'group' => 'Order Product Option'],
            ['title' => 'order_product_option_show', 'group' => 'Order Product Option'],
            ['title' => 'order_product_option_delete', 'group' => 'Order Product Option'],
            ['title' => 'order_product_option_access', 'group' => 'Order Product Option'],

            // Order Product Variation CRUD
            ['title' => 'order_product_variation_create', 'group' => 'Order Product Variation'],
            ['title' => 'order_product_variation_edit', 'group' => 'Order Product Variation'],
            ['title' => 'order_product_variation_show', 'group' => 'Order Product Variation'],
            ['title' => 'order_product_variation_delete', 'group' => 'Order Product Variation'],
            ['title' => 'order_product_variation_access', 'group' => 'Order Product Variation'],

            // Special Permissions for Advanced Features
            ['title' => 'can_approve_vendors', 'group' => 'Vendor Management'],
            ['title' => 'can_approve_products', 'group' => 'Product Management'],
            ['title' => 'can_manage_vendor_commissions', 'group' => 'Vendor Management'],
            ['title' => 'can_process_payouts', 'group' => 'Vendor Management'],
            ['title' => 'can_manage_system_settings', 'group' => 'System Management'],
            ['title' => 'can_view_all_orders', 'group' => 'Order Management'],
            ['title' => 'can_refund_transactions', 'group' => 'Transaction Management'],
            ['title' => 'can_manage_translations', 'group' => 'System Management'],
            ['title' => 'can_access_maintenance', 'group' => 'System Management'],
            ['title' => 'can_export_data', 'group' => 'System Management'],
            ['title' => 'can_import_data', 'group' => 'System Management'],
            ['title' => 'can_bulk_operations', 'group' => 'System Management'],
        ];

        // Add timestamps and status to all permissions
        $permissionsWithDefaults = array_map(function ($permission) {
            return array_merge($permission, [
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $permissions);

        // Insert permissions in batches to avoid memory issues
        $chunks = array_chunk($permissionsWithDefaults, 50);
        foreach ($chunks as $chunk) {
            Permission::insert($chunk);
        }
    }
}
