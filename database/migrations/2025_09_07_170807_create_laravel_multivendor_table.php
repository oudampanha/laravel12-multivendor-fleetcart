<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {

    // Schema::create('users', function (Blueprint $table) {
    //   $table->id();
    //   $table->string('first_name');
    //   $table->string('last_name');
    //   $table->string('username')->unique()->nullable();
    //   $table->string('email')->unique();
    //   $table->string('phone_no')->nullable();
    //   $table->string('password');
    //   $table->timestamp('email_verified_at')->nullable();
    //   $table->boolean('is_verified')->default(false);
    //   $table->dateTime('last_login')->nullable();
    //   $table->string('avartar')->nullable();
    //   $table->rememberToken();
    //   $table->timestamps();
    // });

    // OTP Verifications table
    Schema::create('otp_verifications', function (Blueprint $table) {
      $table->id();
      $table->string('email')->index();
      $table->string('otp', 6);
      $table->timestamp('expires_at');
      $table->boolean('is_used')->default(false);
      $table->timestamps();
    });

    // Roles table
    Schema::create('roles', function (Blueprint $table) {
      $table->id();
      $table->string('title')->nullable();
      $table->boolean('status')->default(true);
      $table->timestamps();
    });

    // Permissions table
    Schema::create('permissions', function (Blueprint $table) {
      $table->id();
      $table->string('group')->nullable();
      $table->string('title')->nullable();
      $table->boolean('status')->default(true);
      $table->timestamps();
    });

    // User permissions pivot table
    Schema::create('permission_role', function (Blueprint $table) {
      $table->unsignedBigInteger('role_id');
      $table->unsignedBigInteger('permission_id');
      $table->timestamps();
      $table->primary(['role_id', 'permission_id']);

      $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
      $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
    });

    // User roles pivot table
    Schema::create('role_user', function (Blueprint $table) {
      $table->unsignedBigInteger('user_id');
      $table->unsignedBigInteger('role_id');
      $table->timestamps();

      $table->primary(['user_id', 'role_id']);
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
    });

    // Create user_permission pivot table for direct user-permission assignments
    Schema::create('permission_user', function (Blueprint $table) {
      $table->unsignedBigInteger('user_id');
      $table->unsignedBigInteger('permission_id');
      $table->timestamps();

      $table->primary(['user_id', 'permission_id']);

      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
    });

    // Vendors table
    Schema::create('vendors', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->string('store_slug')->unique();
      $table->string('store_email')->nullable();
      $table->string('store_phone')->nullable();
      $table->text('store_address')->nullable();
      $table->string('store_city')->nullable();
      $table->string('store_state')->nullable();
      $table->string('store_country')->nullable();
      $table->string('store_zip')->nullable();
      $table->decimal('commission_rate', 5, 2)->default(0);
      $table->boolean('is_active')->default(true);
      $table->boolean('is_verified')->default(false);
      $table->timestamp('verified_at')->nullable();
      $table->decimal('balance', 18, 4)->default(0);
      $table->string('bank_name')->nullable();
      $table->string('bank_account_name')->nullable();
      $table->string('bank_account_number')->nullable();
      $table->string('bank_routing_number')->nullable();
      $table->string('paypal_email')->nullable();
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->index('store_slug');
      $table->index('is_active');
      $table->index('is_verified');
    });

    // Categories table
    Schema::create('categories', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('parent_id')->nullable();
      $table->string('slug')->unique();
      $table->unsignedInteger('position')->nullable();
      $table->string('image')->nullable();
      $table->boolean('is_searchable')->default(true);
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
    });

    // Brands table
    Schema::create('brands', function (Blueprint $table) {
      $table->id();
      $table->string('slug')->unique();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });

    // Tax classes table
    Schema::create('tax_classes', function (Blueprint $table) {
      $table->id();
      $table->string('based_on');
      $table->softDeletes();
      $table->timestamps();
    });

    // Tax rates table
    Schema::create('tax_rates', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('tax_class_id');
      $table->string('country');
      $table->string('state');
      $table->string('city');
      $table->string('zip');
      $table->decimal('rate', 8, 4);
      $table->unsignedInteger('position');
      $table->softDeletes();
      $table->timestamps();

      $table->index('tax_class_id');
      $table->foreign('tax_class_id')->references('id')->on('tax_classes')->onDelete('cascade');
    });

    // Products table
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id')->nullable();
      $table->unsignedBigInteger('brand_id')->nullable();
      $table->unsignedBigInteger('tax_class_id')->nullable();
      $table->string('slug')->unique();
      $table->decimal('price', 18, 4)->nullable();
      $table->decimal('special_price', 18, 4)->nullable();
      $table->string('special_price_type')->nullable();
      $table->date('special_price_start')->nullable();
      $table->date('special_price_end')->nullable();
      $table->decimal('selling_price', 18, 4)->nullable();
      $table->string('sku')->nullable();
      $table->boolean('manage_stock')->default(false);
      $table->integer('qty')->nullable();
      $table->boolean('in_stock')->default(true);
      $table->unsignedInteger('viewed')->default(0);
      $table->boolean('is_active')->default(true);
      $table->boolean('is_virtual')->default(false);
      $table->dateTime('new_from')->nullable();
      $table->dateTime('new_to')->nullable();
      $table->enum('vendor_status', ['pending', 'approved', 'rejected'])->default('pending');
      $table->text('vendor_rejection_reason')->nullable();
      $table->softDeletes();
      $table->timestamps();

      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
      $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
      $table->foreign('tax_class_id')->references('id')->on('tax_classes')->onDelete('set null');
      $table->index('vendor_id');
      $table->index('vendor_status');
    });

    // Product categories pivot table
    Schema::create('product_categories', function (Blueprint $table) {
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('category_id');

      $table->primary(['product_id', 'category_id']);
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
    });

    // Attributes table
    Schema::create('attributes', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('attribute_set_id');
      $table->string('slug')->unique()->nullable();
      $table->boolean('is_filterable')->default(false);
      $table->timestamps();

      $table->index('attribute_set_id');
    });

    // Attribute sets table
    Schema::create('attribute_sets', function (Blueprint $table) {
      $table->id();
      $table->timestamps();
    });

    // Attribute values table
    Schema::create('attribute_values', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('attribute_id');
      $table->unsignedInteger('position');
      $table->timestamps();

      $table->index('attribute_id');
      $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
    });

    // Product attributes table
    Schema::create('product_attributes', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('attribute_id');

      $table->index('product_id');
      $table->index('attribute_id');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
    });

    // Product attribute values pivot table
    Schema::create('product_attribute_values', function (Blueprint $table) {
      $table->unsignedBigInteger('product_attribute_id');
      $table->unsignedBigInteger('attribute_value_id');

      $table->primary(['product_attribute_id', 'attribute_value_id'], 'product_attr_values_primary');
      $table->foreign('product_attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
      $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->onDelete('cascade');
    });

    // Variations table
    Schema::create('variations', function (Blueprint $table) {
      $table->id();
      $table->string('uid')->unique();
      $table->string('type');
      $table->boolean('is_global')->default(true);
      $table->unsignedInteger('position')->nullable();
      $table->softDeletes();
      $table->timestamps();
    });

    // Variation values table
    Schema::create('variation_values', function (Blueprint $table) {
      $table->id();
      $table->string('uid')->unique();
      $table->unsignedBigInteger('variation_id');
      $table->string('value')->nullable();
      $table->unsignedInteger('position')->nullable();
      $table->timestamps();

      $table->index('variation_id');
      $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
    });

    // Product variations table
    Schema::create('product_variations', function (Blueprint $table) {
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('variation_id');

      $table->primary(['product_id', 'variation_id']);
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
    });

    // Product variants table
    Schema::create('product_variants', function (Blueprint $table) {
      $table->id();
      $table->string('uid');
      $table->text('uids');
      $table->unsignedBigInteger('product_id');
      $table->string('name');
      $table->decimal('price', 18, 4)->nullable();
      $table->decimal('special_price', 18, 4)->nullable();
      $table->string('special_price_type')->nullable();
      $table->date('special_price_start')->nullable();
      $table->date('special_price_end')->nullable();
      $table->decimal('selling_price', 18, 4)->nullable();
      $table->string('sku')->nullable();
      $table->boolean('manage_stock')->nullable();
      $table->integer('qty')->nullable();
      $table->boolean('in_stock')->nullable();
      $table->boolean('is_default')->nullable();
      $table->boolean('is_active')->nullable();
      $table->unsignedInteger('position')->nullable();
      $table->softDeletes();
      $table->timestamps();

      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });

    // Options table
    Schema::create('options', function (Blueprint $table) {
      $table->id();
      $table->string('type');
      $table->boolean('is_required')->default(false);
      $table->boolean('is_global')->default(true);
      $table->unsignedInteger('position')->nullable();
      $table->softDeletes();
      $table->timestamps();
    });

    // Option values table
    Schema::create('option_values', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('option_id');
      $table->decimal('price', 18, 4)->nullable();
      $table->string('price_type', 10);
      $table->unsignedInteger('position');
      $table->timestamps();

      $table->index('option_id');
      $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
    });

    // Product options pivot table
    Schema::create('product_options', function (Blueprint $table) {
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('option_id');

      $table->primary(['product_id', 'option_id']);
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
    });

    // Tags table
    Schema::create('tags', function (Blueprint $table) {
      $table->id();
      $table->string('slug')->unique();
      $table->timestamps();
    });

    // Product tags pivot table
    Schema::create('product_tags', function (Blueprint $table) {
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('tag_id');

      $table->primary(['product_id', 'tag_id']);
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
    });

    // Related products table
    Schema::create('related_products', function (Blueprint $table) {
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('related_product_id');
      $table->timestamps();

      $table->primary(['product_id', 'related_product_id']);
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('related_product_id')->references('id')->on('products')->onDelete('cascade');
    });

    // Up sell products table
    Schema::create('up_sell_products', function (Blueprint $table) {
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('up_sell_product_id');
      $table->timestamps();

      $table->primary(['product_id', 'up_sell_product_id']);
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('up_sell_product_id')->references('id')->on('products')->onDelete('cascade');
    });

    // Cross sell products table
    Schema::create('cross_sell_products', function (Blueprint $table) {
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('cross_sell_product_id');
      $table->timestamps();

      $table->primary(['product_id', 'cross_sell_product_id']);
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('cross_sell_product_id')->references('id')->on('products')->onDelete('cascade');
    });

    // Coupons table
    Schema::create('coupons', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id')->nullable();
      $table->string('code')->index();
      $table->decimal('value', 18, 4)->nullable();
      $table->boolean('is_percent')->default(false);
      $table->boolean('free_shipping')->default(false);
      $table->decimal('minimum_spend', 18, 4)->nullable();
      $table->decimal('maximum_spend', 18, 4)->nullable();
      $table->unsignedInteger('usage_limit_per_coupon')->nullable();
      $table->unsignedInteger('usage_limit_per_customer')->nullable();
      $table->integer('used')->default(0);
      $table->boolean('is_active')->default(true);
      $table->date('start_date')->nullable();
      $table->date('end_date')->nullable();
      $table->softDeletes();
      $table->timestamps();

      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
      $table->index('vendor_id');
    });

    // Coupon categories pivot table
    Schema::create('coupon_categories', function (Blueprint $table) {
      $table->unsignedBigInteger('coupon_id');
      $table->unsignedBigInteger('category_id');
      $table->boolean('exclude')->default(false);

      $table->primary(['coupon_id', 'category_id', 'exclude']);
      $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
      $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
    });

    // Coupon products pivot table
    Schema::create('coupon_products', function (Blueprint $table) {
      $table->unsignedBigInteger('coupon_id');
      $table->unsignedBigInteger('product_id');
      $table->boolean('exclude')->default(false);

      $table->primary(['coupon_id', 'product_id']);
      $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });

    // Orders table
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('customer_id')->nullable();
      $table->string('customer_email');
      $table->string('customer_phone')->nullable();
      $table->string('customer_first_name');
      $table->string('customer_last_name');
      $table->string('billing_first_name');
      $table->string('billing_last_name');
      $table->string('billing_address_1');
      $table->string('billing_address_2')->nullable();
      $table->string('billing_city');
      $table->string('billing_state');
      $table->string('billing_zip');
      $table->string('billing_country');
      $table->string('shipping_first_name');
      $table->string('shipping_last_name');
      $table->string('shipping_address_1');
      $table->string('shipping_address_2')->nullable();
      $table->string('shipping_city');
      $table->string('shipping_state');
      $table->string('shipping_zip');
      $table->string('shipping_country');
      $table->decimal('sub_total', 18, 4);
      $table->string('shipping_method')->nullable();
      $table->decimal('shipping_cost', 18, 4);
      $table->unsignedBigInteger('coupon_id')->nullable();
      $table->decimal('discount', 18, 4);
      $table->decimal('total', 18, 4);
      $table->string('payment_method');
      $table->string('currency');
      $table->decimal('currency_rate', 18, 4);
      $table->string('locale');
      $table->string('status');
      $table->text('note')->nullable();
      $table->text('tracking_reference')->nullable();
      $table->softDeletes();
      $table->timestamps();

      $table->index('customer_id');
      $table->index('coupon_id');
    });

    // Order products table
    Schema::create('order_products', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id');
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('vendor_id')->nullable();
      $table->unsignedBigInteger('product_variant_id')->nullable();
      $table->decimal('unit_price', 18, 4);
      $table->integer('qty');
      $table->decimal('line_total', 18, 4);
      $table->decimal('vendor_commission', 18, 4)->default(0);
      $table->enum('vendor_status', ['pending', 'processing', 'shipped', 'delivered', 'canceled', 'refunded'])->default('pending');

      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
      $table->index('vendor_id');
      $table->index('vendor_status');
    });

    // Order product options table
    Schema::create('order_product_options', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_product_id');
      $table->unsignedBigInteger('option_id');
      $table->text('value')->nullable();

      $table->unique(['order_product_id', 'option_id']);
      $table->foreign('order_product_id')->references('id')->on('order_products')->onDelete('cascade');
      $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
    });

    // Order product option values table
    Schema::create('order_product_option_values', function (Blueprint $table) {
      $table->unsignedBigInteger('order_product_option_id');
      $table->unsignedBigInteger('option_value_id');
      $table->decimal('price', 18, 4)->nullable();

      $table->primary(['order_product_option_id', 'option_value_id'], 'order_product_option_values_primary');
      $table->foreign('order_product_option_id')->references('id')->on('order_product_options')->onDelete('cascade');
      $table->foreign('option_value_id')->references('id')->on('option_values')->onDelete('cascade');
    });

    // Order product variations table
    Schema::create('order_product_variations', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_product_id');
      $table->unsignedBigInteger('variation_id');
      $table->string('type');
      $table->string('value');

      $table->unique(['order_product_id', 'variation_id']);
      $table->foreign('order_product_id')->references('id')->on('order_products')->onDelete('cascade');
      $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
    });

    // Order product variation values table
    Schema::create('order_product_variation_values', function (Blueprint $table) {
      $table->unsignedBigInteger('order_product_variation_id');
      $table->unsignedBigInteger('variation_value_id');

      $table->primary(['order_product_variation_id', 'variation_value_id'], 'order_product_variation_values_primary');
      $table->foreign('order_product_variation_id', 'order_prod_var_id_foreign')->references('id')->on('order_product_variations')->onDelete('cascade');
      $table->foreign('variation_value_id')->references('id')->on('variation_values')->onDelete('cascade');
    });

    // Order taxes table
    Schema::create('order_taxes', function (Blueprint $table) {
      $table->unsignedBigInteger('order_id');
      $table->unsignedBigInteger('tax_rate_id');
      $table->decimal('amount', 15, 4);

      $table->primary(['order_id', 'tax_rate_id']);
      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
      $table->foreign('tax_rate_id')->references('id')->on('tax_rates')->onDelete('cascade');
    });

    // Order downloads table
    Schema::create('order_downloads', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id');
      $table->unsignedBigInteger('file_id');

      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
    });

    // Transactions table
    Schema::create('transactions', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id');
      $table->string('transaction_id');
      $table->string('payment_method');
      $table->softDeletes();
      $table->timestamps();

      $table->unique('order_id');
      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
    });

    // Vendor orders table
    Schema::create('vendor_orders', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id');
      $table->unsignedBigInteger('order_id');
      $table->decimal('sub_total', 18, 4);
      $table->decimal('commission_amount', 18, 4);
      $table->decimal('vendor_amount', 18, 4);
      $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'canceled', 'refunded'])->default('pending');
      $table->text('note')->nullable();
      $table->timestamps();

      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
      $table->index(['vendor_id', 'order_id']);
      $table->index('status');
    });

    // Vendor payouts table
    Schema::create('vendor_payouts', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id');
      $table->decimal('amount', 18, 4);
      $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'canceled'])->default('pending');
      $table->enum('method', ['bank_transfer', 'paypal', 'stripe', 'manual']);
      $table->string('reference_number')->nullable();
      $table->text('note')->nullable();
      $table->timestamp('paid_at')->nullable();
      $table->timestamps();

      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
      $table->index('vendor_id');
      $table->index('status');
    });

    // Vendor withdrawals table
    Schema::create('vendor_withdrawals', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id');
      $table->decimal('amount', 18, 4);
      $table->enum('method', ['bank_transfer', 'paypal', 'stripe', 'manual']);
      $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
      $table->text('note')->nullable();
      $table->text('admin_note')->nullable();
      $table->timestamp('processed_at')->nullable();
      $table->timestamps();

      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
      $table->index(['vendor_id', 'status']);
    });

    // Reviews table
    Schema::create('reviews', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('reviewer_id')->nullable();
      $table->unsignedBigInteger('product_id');
      $table->integer('rating');
      $table->string('reviewer_name');
      $table->text('comment');
      $table->boolean('is_approved')->default(false);
      $table->timestamps();

      $table->index('reviewer_id');
      $table->index('product_id');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });

    // Vendor reviews table
    Schema::create('vendor_reviews', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id');
      $table->unsignedBigInteger('customer_id')->nullable();
      $table->unsignedBigInteger('order_id')->nullable();
      $table->integer('rating');
      $table->string('reviewer_name');
      $table->text('comment');
      $table->boolean('is_approved')->default(false);
      $table->timestamps();

      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
      $table->foreign('customer_id')->references('id')->on('users')->onDelete('set null');
      $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
      $table->index('vendor_id');
      $table->index('is_approved');
    });

    // Flash sales table
    Schema::create('flash_sales', function (Blueprint $table) {
      $table->id();
      $table->timestamps();
    });

    // Flash sale products table
    Schema::create('flash_sale_products', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('flash_sale_id');
      $table->unsignedBigInteger('product_id');
      $table->date('end_date');
      $table->decimal('price', 18, 4);
      $table->integer('qty');
      $table->integer('position');

      $table->foreign('flash_sale_id')->references('id')->on('flash_sales')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });

    // Flash sale product orders table
    Schema::create('flash_sale_product_orders', function (Blueprint $table) {
      $table->unsignedBigInteger('flash_sale_product_id');
      $table->unsignedBigInteger('order_id');
      $table->integer('qty');

      $table->primary(['flash_sale_product_id', 'order_id']);
      $table->foreign('flash_sale_product_id')->references('id')->on('flash_sale_products')->onDelete('cascade');
      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
    });

    // Blog categories table
    Schema::create('blog_categories', function (Blueprint $table) {
      $table->id();
      $table->string('slug')->unique();
      $table->timestamps();
    });

    // Blog tags table
    Schema::create('blog_tags', function (Blueprint $table) {
      $table->id();
      $table->string('slug')->unique();
      $table->timestamps();
    });

    // Blog posts table
    Schema::create('blog_posts', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->unsignedBigInteger('blog_category_id')->nullable();
      $table->string('slug')->unique();
      $table->string('publish_status');
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('blog_category_id')->references('id')->on('blog_categories')->onDelete('cascade');
    });

    // Blog post blog tag pivot table
    Schema::create('blog_post_blog_tag', function (Blueprint $table) {
      $table->unsignedBigInteger('blog_post_id');
      $table->unsignedBigInteger('blog_tag_id');

      $table->primary(['blog_post_id', 'blog_tag_id']);
      $table->foreign('blog_post_id')->references('id')->on('blog_posts')->onDelete('cascade');
      $table->foreign('blog_tag_id')->references('id')->on('blog_tags')->onDelete('cascade');
    });

    // Pages table
    Schema::create('pages', function (Blueprint $table) {
      $table->id();
      $table->string('slug')->unique();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });

    // Menus table
    Schema::create('menus', function (Blueprint $table) {
      $table->id();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });

    // Menu items table
    Schema::create('menu_items', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('menu_id');
      $table->unsignedBigInteger('parent_id')->nullable();
      $table->unsignedBigInteger('category_id')->nullable();
      $table->unsignedBigInteger('page_id')->nullable();
      $table->string('type');
      $table->string('url')->nullable();
      $table->string('icon')->nullable();
      $table->string('target');
      $table->unsignedInteger('position')->nullable();
      $table->boolean('is_root')->default(false);
      $table->boolean('is_fluid')->default(false);
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index('menu_id');
      $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
      $table->foreign('parent_id')->references('id')->on('menu_items')->onDelete('cascade');
      $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
      $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
    });

    // Sliders table
    Schema::create('sliders', function (Blueprint $table) {
      $table->id();
      $table->integer('speed')->nullable();
      $table->boolean('autoplay')->nullable();
      $table->integer('autoplay_speed')->nullable();
      $table->boolean('fade')->default(false);
      $table->boolean('dots')->nullable();
      $table->boolean('arrows')->nullable();
      $table->timestamps();
    });

    // Slider slides table
    Schema::create('slider_slides', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('slider_id');
      $table->text('options')->nullable();
      $table->string('call_to_action_url')->nullable();
      $table->boolean('open_in_new_window')->nullable();
      $table->integer('position')->nullable();
      $table->timestamps();

      $table->foreign('slider_id')->references('id')->on('sliders')->onDelete('cascade');
    });

    // Settings table
    Schema::create('settings', function (Blueprint $table) {
      $table->id();
      $table->string('key')->unique();
      $table->boolean('is_translatable')->default(false);
      $table->text('plain_value')->nullable();
      $table->timestamps();
    });

    // Vendor settings table
    Schema::create('vendor_settings', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id');
      $table->string('key');
      $table->text('value')->nullable();
      $table->timestamps();

      $table->unique(['vendor_id', 'key']);
      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
    });

    // Currency rates table
    Schema::create('currency_rates', function (Blueprint $table) {
      $table->id();
      $table->string('currency')->unique();
      $table->decimal('rate', 18, 8);
      $table->timestamps();
    });

    // Meta data table
    Schema::create('meta_data', function (Blueprint $table) {
      $table->id();
      $table->string('entity_type');
      $table->unsignedBigInteger('entity_id');
      $table->timestamps();

      $table->index(['entity_type', 'entity_id']);
    });

    // media table
    Schema::create('media', function (Blueprint $table) {
      $table->id();
      $table->string('file_name');
      $table->string('original_name');
      $table->string('file_path');
      $table->string('file_url');
      $table->string('folder_path')->nullable();
      $table->string('mime_type');
      $table->string('file_extension', 10);
      $table->bigInteger('file_size');
      $table->string('disk', 255);
      $table->string('file_type', 255);
      $table->json('metadata')->nullable();
      $table->unsignedBigInteger('user_id')->nullable();
      $table->timestamps();

      $table->index('folder_path');
      $table->index('file_type');
      $table->index('user_id');
      $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
    });

    // Entity media table
    Schema::create('entity_media', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('file_id');
      $table->string('entity_type');
      $table->unsignedBigInteger('entity_id');
      $table->string('zone')->index();
      $table->timestamps();

      $table->index(['entity_type', 'entity_id']);
      $table->foreign('file_id')->references('id')->on('media')->onDelete('cascade');
    });

    // Addresses table
    Schema::create('addresses', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('customer_id');
      $table->string('first_name');
      $table->string('last_name');
      $table->string('address_1');
      $table->string('address_2')->nullable();
      $table->string('city');
      $table->string('state');
      $table->string('zip');
      $table->string('country');
      $table->timestamps();

      $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
    });

    // Default addresses table
    Schema::create('default_addresses', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('customer_id');
      $table->unsignedBigInteger('address_id');

      $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
    });

    // Wish lists table
    Schema::create('wish_lists', function (Blueprint $table) {
      $table->unsignedBigInteger('user_id');
      $table->unsignedBigInteger('product_id');
      $table->timestamps();

      $table->primary(['user_id', 'product_id']);
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });

    // Carts table
    Schema::create('carts', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->longText('data');
      $table->timestamps();

      $table->index('id');
    });

    // Search terms table
    Schema::create('search_terms', function (Blueprint $table) {
      $table->id();
      $table->string('term')->unique();
      $table->unsignedInteger('results');
      $table->unsignedInteger('hits')->default(0);
      $table->timestamps();
    });

    // Vendor notifications table
    Schema::create('vendor_notifications', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id');
      $table->string('type');
      $table->string('title');
      $table->text('message');
      $table->json('data')->nullable();
      $table->boolean('is_read')->default(false);
      $table->timestamp('read_at')->nullable();
      $table->timestamps();

      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
      $table->index(['vendor_id', 'is_read']);
    });

    // Vendor shipping zones table
    Schema::create('vendor_shipping_zones', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id');
      $table->string('name');
      $table->json('countries');
      $table->json('states')->nullable();
      $table->json('zip_codes')->nullable();
      $table->enum('shipping_method', ['flat_rate', 'free_shipping', 'local_pickup', 'by_weight', 'by_price']);
      $table->decimal('rate', 18, 4)->nullable();
      $table->decimal('minimum_order', 18, 4)->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
      $table->index('vendor_id');
    });

    // Authentication tables
    Schema::create('activations', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->string('code');
      $table->boolean('completed')->default(false);
      $table->dateTime('completed_at')->nullable();
      $table->timestamps();

      $table->index('user_id');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });

    Schema::create('persistences', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->string('code')->unique();
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });

    Schema::create('reminders', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->string('code');
      $table->boolean('completed')->default(false);
      $table->dateTime('completed_at')->nullable();
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });

    Schema::create('throttle', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id')->nullable();
      $table->string('type');
      $table->string('ip')->nullable();
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });

    // UNIFIED TRANSLATIONS TABLE
    Schema::create('translations', function (Blueprint $table) {
      $table->id();
      $table->string('translatable_type');
      $table->unsignedBigInteger('translatable_id');
      $table->string('locale');
      $table->string('field');
      $table->longText('value')->nullable();
      $table->timestamps();

      $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'unique_translation');
      $table->index(['translatable_type', 'translatable_id'], 'translatable_index');
      $table->index('locale');
    });

    // Language translations for general UI
    Schema::create('language_lines', function (Blueprint $table) {
      $table->id();
      $table->string('group');
      $table->string('key')->index();
      $table->json('text');
      $table->timestamps();

      $table->unique(['group', 'key']);
    });

    // Updater scripts table
    Schema::create('updater_scripts', function (Blueprint $table) {
      $table->id();
      $table->string('script');
    });

    // Attribute categories table
    Schema::create('attribute_categories', function (Blueprint $table) {
      $table->unsignedBigInteger('attribute_id');
      $table->unsignedBigInteger('category_id');

      $table->primary(['attribute_id', 'category_id']);
      $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
      $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // Drop tables in reverse order to avoid foreign key constraint issues
    Schema::dropIfExists('attribute_categories');
    Schema::dropIfExists('updater_scripts');
    Schema::dropIfExists('language_lines');
    Schema::dropIfExists('translations');
    Schema::dropIfExists('throttle');
    Schema::dropIfExists('reminders');
    Schema::dropIfExists('persistences');
    Schema::dropIfExists('activations');
    Schema::dropIfExists('vendor_shipping_zones');
    Schema::dropIfExists('vendor_notifications');
    Schema::dropIfExists('search_terms');
    Schema::dropIfExists('carts');
    Schema::dropIfExists('wish_lists');
    Schema::dropIfExists('default_addresses');
    Schema::dropIfExists('addresses');
    Schema::dropIfExists('entity_media');
    Schema::dropIfExists('media');
    Schema::dropIfExists('meta_data');
    Schema::dropIfExists('currency_rates');
    Schema::dropIfExists('vendor_settings');
    Schema::dropIfExists('settings');
    Schema::dropIfExists('slider_slides');
    Schema::dropIfExists('sliders');
    Schema::dropIfExists('menu_items');
    Schema::dropIfExists('menus');
    Schema::dropIfExists('pages');
    Schema::dropIfExists('blog_post_blog_tag');
    Schema::dropIfExists('blog_posts');
    Schema::dropIfExists('blog_tags');
    Schema::dropIfExists('blog_categories');
    Schema::dropIfExists('flash_sale_product_orders');
    Schema::dropIfExists('flash_sale_products');
    Schema::dropIfExists('flash_sales');
    Schema::dropIfExists('vendor_reviews');
    Schema::dropIfExists('reviews');
    Schema::dropIfExists('vendor_withdrawals');
    Schema::dropIfExists('vendor_payouts');
    Schema::dropIfExists('vendor_orders');
    Schema::dropIfExists('transactions');
    Schema::dropIfExists('order_downloads');
    Schema::dropIfExists('order_taxes');
    Schema::dropIfExists('order_product_variation_values');
    Schema::dropIfExists('order_product_variations');
    Schema::dropIfExists('order_product_option_values');
    Schema::dropIfExists('order_product_options');
    Schema::dropIfExists('order_products');
    Schema::dropIfExists('orders');
    Schema::dropIfExists('coupon_products');
    Schema::dropIfExists('coupon_categories');
    Schema::dropIfExists('coupons');
    Schema::dropIfExists('cross_sell_products');
    Schema::dropIfExists('up_sell_products');
    Schema::dropIfExists('related_products');
    Schema::dropIfExists('product_tags');
    Schema::dropIfExists('tags');
    Schema::dropIfExists('product_options');
    Schema::dropIfExists('option_values');
    Schema::dropIfExists('options');
    Schema::dropIfExists('product_variants');
    Schema::dropIfExists('product_variations');
    Schema::dropIfExists('variation_values');
    Schema::dropIfExists('variations');
    Schema::dropIfExists('product_attribute_values');
    Schema::dropIfExists('product_attributes');
    Schema::dropIfExists('attribute_values');
    Schema::dropIfExists('attribute_sets');
    Schema::dropIfExists('attributes');
    Schema::dropIfExists('product_categories');
    Schema::dropIfExists('products');
    Schema::dropIfExists('tax_rates');
    Schema::dropIfExists('tax_classes');
    Schema::dropIfExists('brands');
    Schema::dropIfExists('categories');
    Schema::dropIfExists('vendors');
    Schema::dropIfExists('permission_role');
    Schema::dropIfExists('permissions');
    Schema::dropIfExists('permission_user');
    Schema::dropIfExists('role_user');
    Schema::dropIfExists('roles');
    Schema::dropIfExists('otp_verifications');
    Schema::dropIfExists('users');
  }
};
