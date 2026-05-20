<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <h3>Multi-Vendor</h3>
  </div>
  <nav class="sidebar-menu">
    <ul class="metismenu" id="metismenu">

      <!-- Dashboard -->
      <li class="{{ request()->is('admin') || request()->is('admin/dashboard') ? 'mm-active' : '' }}">
        <a href="{{ route('admin.dashboard') }}">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <!-- User Management -->
      @permission('user_management_access')
        <li
          class="{{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'true' : 'false' }}">
            <i class="fas fa-users"></i>
            <span>User Management</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul
            class="{{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'mm-show' : '' }}">
            @permission('user_access')
              <li class="{{ request()->is('admin/users') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.users.index') }}">All Users</a></li>
            @endpermission
            @permission('user_create')
              <li class="{{ request()->is('admin/users/create') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.users.create') }}">Add User</a></li>
            @endpermission
            @permission('role_access')
              <li class="{{ request()->is('admin/roles*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.roles.index') }}">User Roles</a></li>
            @endpermission
            @permission('permission_access')
              <li class="{{ request()->is('admin/permissions*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.permissions.index') }}">Permissions</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission

      <!-- Vendor Management -->
      @permission('vendor_management_access')
        <li class="{{ request()->is('admin/vendors*') || request()->is('admin/vendor-*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/vendors*') || request()->is('admin/vendor-*') ? 'true' : 'false' }}">
            <i class="fas fa-store"></i>
            <span>Vendor Management</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul class="{{ request()->is('admin/vendors*') || request()->is('admin/vendor-*') ? 'mm-show' : '' }}">
            @permission('vendor_access')
              <li class="{{ request()->is('admin/vendors') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.vendors.index') }}">All Vendors</a></li>
            @endpermission
            @permission('vendor_create')
              <li class="{{ request()->is('admin/vendors/create') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.vendors.create') }}">Add Vendor</a></li>
            @endpermission
            @permission('vendor_payout_access')
              <li
                class="{{ request()->is('admin/vendor-payouts') && !request()->is('admin/vendor-payouts/pending') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.vendor-payouts.index') }}">Vendor Payouts</a></li>
              <li class="{{ request()->is('admin/vendor-payouts/pending') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.vendor-payouts.pending') }}">Pending Payouts</a></li>
            @endpermission
            @permission('vendor_withdrawal_access')
              <li
                class="{{ request()->is('admin/vendor-withdrawals') && !request()->is('admin/vendor-withdrawals/pending') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.vendor-withdrawals.index') }}">Withdrawal Requests</a></li>
              <li class="{{ request()->is('admin/vendor-withdrawals/pending') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.vendor-withdrawals.pending') }}">Pending Withdrawals</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission

      <!-- Product Management -->
      @permission('product_management_access')
        <li
          class="{{ request()->is('admin/products*') || request()->is('admin/categories*') || request()->is('admin/brands*') || request()->is('admin/attributes*') || request()->is('admin/variations*') || request()->is('admin/options*') || request()->is('admin/tags*') || request()->is('admin/attribute-sets*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/products*') || request()->is('admin/categories*') || request()->is('admin/brands*') || request()->is('admin/attributes*') || request()->is('admin/variations*') || request()->is('admin/options*') || request()->is('admin/tags*') || request()->is('admin/attribute-sets*') ? 'true' : 'false' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Product Management</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul
            class="{{ request()->is('admin/products*') || request()->is('admin/categories*') || request()->is('admin/brands*') || request()->is('admin/attributes*') || request()->is('admin/variations*') || request()->is('admin/options*') || request()->is('admin/tags*') || request()->is('admin/attribute-sets*') ? 'mm-show' : '' }}">
            @permission('product_access')
              <li
                class="{{ request()->is('admin/products') && !request()->is('admin/products/pending-approval') && !request()->is('admin/products/approved') && !request()->is('admin/products/rejected') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.products.index') }}">All Products</a></li>
              <li class="{{ request()->is('admin/products/pending-approval') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.products.pending-approval') }}">Pending Approval</a></li>
              <li class="{{ request()->is('admin/products/approved') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.products.approved') }}">Approved Products</a></li>
              <li class="{{ request()->is('admin/products/rejected') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.products.rejected') }}">Rejected Products</a></li>
            @endpermission
            @permission('product_create')
              <li class="{{ request()->is('admin/products/create') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.products.create') }}">Add Product</a></li>
            @endpermission
            @permission('category_access')
              <li
                class="{{ request()->is('admin/categories') && !request()->is('admin/categories/tree') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.categories.index') }}">Categories</a></li>
            @endpermission
            @permission('brand_access')
              <li class="{{ request()->is('admin/brands*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.brands.index') }}">Brands</a></li>
            @endpermission
            @permission('attribute_access')
              <li class="{{ request()->is('admin/attribute-sets*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.attribute-sets.index') }}">Attribute Sets</a></li>
              <li class="{{ request()->is('admin/attributes*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.attributes.index') }}">Attributes</a></li>
            @endpermission
            @permission('variation_access')
              <li class="{{ request()->is('admin/variations*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.variations.index') }}">Variations</a></li>
            @endpermission
            @permission('option_access')
              <li class="{{ request()->is('admin/options*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.options.index') }}">Options</a></li>
            @endpermission
            @permission('tag_access')
              <li class="{{ request()->is('admin/tags*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.tags.index') }}">Product Tags</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission

      <!-- Inventory Management -->
      @permission('inventory_management_access')
        <li
          class="{{ request()->is('admin/inventory*') || request()->is('admin/warehouses*') || request()->is('admin/suppliers*') || request()->is('admin/product-stocks*') || request()->is('admin/stock-*') || request()->is('admin/purchase-orders*') || request()->is('admin/goods-receipts*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/inventory*') || request()->is('admin/warehouses*') || request()->is('admin/suppliers*') || request()->is('admin/product-stocks*') || request()->is('admin/stock-*') || request()->is('admin/purchase-orders*') || request()->is('admin/goods-receipts*') ? 'true' : 'false' }}">
            <i class="fas fa-warehouse"></i>
            <span>Inventory</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul
            class="{{ request()->is('admin/inventory*') || request()->is('admin/warehouses*') || request()->is('admin/suppliers*') || request()->is('admin/product-stocks*') || request()->is('admin/stock-*') || request()->is('admin/purchase-orders*') || request()->is('admin/goods-receipts*') ? 'mm-show' : '' }}">
            <li class="{{ request()->is('admin/inventory') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.inventory.dashboard') }}">Dashboard</a></li>
            @permission('warehouse_access')
              <li class="{{ request()->is('admin/warehouses*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.warehouses.index') }}">Warehouses</a></li>
            @endpermission
            @permission('supplier_access')
              <li class="{{ request()->is('admin/suppliers*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
            @endpermission
            @permission('product_stock_access')
              <li class="{{ request()->is('admin/product-stocks') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.product-stocks.index') }}">Stock On Hand</a></li>
              <li class="{{ request()->is('admin/product-stocks/low-stock') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.product-stocks.low-stock') }}">Low Stock</a></li>
              <li class="{{ request()->is('admin/product-stocks/out-of-stock') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.product-stocks.out-of-stock') }}">Out of Stock</a></li>
            @endpermission
            @permission('stock_movement_access')
              <li class="{{ request()->is('admin/stock-movements*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.stock-movements.index') }}">Stock Movements</a></li>
            @endpermission
            @permission('purchase_order_access')
              <li class="{{ request()->is('admin/purchase-orders*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.purchase-orders.index') }}">Purchase Orders</a></li>
            @endpermission
            @permission('goods_receipt_access')
              <li class="{{ request()->is('admin/goods-receipts*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.goods-receipts.index') }}">Goods Receipts</a></li>
            @endpermission
            @permission('stock_adjustment_access')
              <li class="{{ request()->is('admin/stock-adjustments*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.stock-adjustments.index') }}">Stock Adjustments</a></li>
            @endpermission
            @permission('stock_transfer_access')
              <li class="{{ request()->is('admin/stock-transfers*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.stock-transfers.index') }}">Stock Transfers</a></li>
            @endpermission
            @permission('stock_take_access')
              <li class="{{ request()->is('admin/stock-takes*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.stock-takes.index') }}">Stock Takes</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission

      <!-- Order Management -->
      @permission('order_management_access')
        <li class="{{ request()->is('admin/orders*') || request()->is('admin/transactions*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/orders*') || request()->is('admin/transactions*') ? 'true' : 'false' }}">
            <i class="fas fa-receipt"></i>
            <span>Order Management</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul class="{{ request()->is('admin/orders*') || request()->is('admin/transactions*') ? 'mm-show' : '' }}">
            @permission('order_access')
              <li
                class="{{ request()->is('admin/orders') && !request()->is('admin/orders/by-status/*') && !request()->is('admin/orders/export') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.orders.index') }}">All Orders</a></li>
              <li class="{{ request()->is('admin/orders/by-status/pending') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.orders.by-status', 'pending') }}">Pending Orders</a></li>
              <li class="{{ request()->is('admin/orders/by-status/processing') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.orders.by-status', 'processing') }}">Processing Orders</a></li>
              <li class="{{ request()->is('admin/orders/by-status/delivered') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.orders.by-status', 'delivered') }}">Completed Orders</a></li>
              <li class="{{ request()->is('admin/orders/export') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.orders.export') }}">Export Orders</a></li>
            @endpermission
            @permission('transaction_access')
              <li
                class="{{ request()->is('admin/transactions') && !request()->is('admin/transactions/failed') && !request()->is('admin/transactions/refunded') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.transactions.index') }}">Transactions</a></li>
              <li class="{{ request()->is('admin/transactions/failed') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.transactions.failed') }}">Failed Transactions</a></li>
              <li class="{{ request()->is('admin/transactions/refunded') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.transactions.refunded') }}">Refunded Transactions</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission

      <!-- Marketing & Promotions -->
      @permission('promotion_management_access')
        <li class="{{ request()->is('admin/coupons*') || request()->is('admin/flash-sales*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/coupons*') || request()->is('admin/flash-sales*') ? 'true' : 'false' }}">
            <i class="fas fa-tags"></i>
            <span>Marketing & Promotions</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul class="{{ request()->is('admin/coupons*') || request()->is('admin/flash-sales*') ? 'mm-show' : '' }}">
            @permission('coupon_access')
              <li
                class="{{ request()->is('admin/coupons') && !request()->is('admin/coupons/active') && !request()->is('admin/coupons/expired') && !request()->is('admin/coupons/create') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.coupons.index') }}">All Coupons</a></li>
              <li class="{{ request()->is('admin/coupons/active') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.coupons.active') }}">Active Coupons</a></li>
              <li class="{{ request()->is('admin/coupons/expired') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.coupons.expired') }}">Expired Coupons</a></li>
            @endpermission
            @permission('coupon_create')
              <li class="{{ request()->is('admin/coupons/create') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.coupons.create') }}">Create Coupon</a></li>
            @endpermission
            @permission('flash_sale_access')
              <li
                class="{{ request()->is('admin/flash-sales') && !request()->is('admin/flash-sales/create') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.flash-sales.index') }}">Flash Sales</a></li>
            @endpermission
            @permission('flash_sale_create')
              <li class="{{ request()->is('admin/flash-sales/create') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.flash-sales.create') }}">Create Flash Sale</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission

      <!-- Reviews & Ratings -->
      @permission('review_management_access')
        <li
          class="{{ request()->is('admin/reviews*') || request()->is('admin/vendor-reviews*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/reviews*') || request()->is('admin/vendor-reviews*') ? 'true' : 'false' }}">
            <i class="fas fa-star"></i>
            <span>Reviews & Ratings</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul class="{{ request()->is('admin/reviews*') || request()->is('admin/vendor-reviews*') ? 'mm-show' : '' }}">
            @permission('review_access')
              <li
                class="{{ request()->is('admin/reviews') && !request()->is('admin/reviews/pending') && !request()->is('admin/reviews/approved') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.reviews.index') }}">Product Reviews</a></li>
              <li class="{{ request()->is('admin/reviews/pending') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.reviews.pending') }}">Pending Reviews</a></li>
              <li class="{{ request()->is('admin/reviews/approved') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.reviews.approved') }}">Approved Reviews</a></li>
            @endpermission
            @permission('vendor_review_access')
              <li class="{{ request()->is('admin/vendor-reviews*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.vendor-reviews.index') }}">Vendor Reviews</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission

      <!-- Localization -->
      @permission('translation_management_access')
        <li
          class="{{ request()->is('admin/translations*') || request()->is('admin/language-lines*') || request()->is('admin/translation-management*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/translations*') || request()->is('admin/language-lines*') || request()->is('admin/translation-management*') ? 'true' : 'false' }}">
            <i class="fas fa-language"></i>
            <span>Localization</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul
            class="{{ request()->is('admin/translations*') || request()->is('admin/language-lines*') || request()->is('admin/translation-management*') ? 'mm-show' : '' }}">
            @permission('translation_access')
              <li class="{{ request()->is('admin/translations*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.translations.index') }}">Translations</a></li>
            @endpermission
            @permission('language_line_access')
              <li class="{{ request()->is('admin/language-lines*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.language-lines.index') }}">Language Lines</a></li>
            @endpermission
            @permission('can_manage_translations')
              <li class="{{ request()->is('admin/translation-management*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.translation-management.index') }}">Translation Management</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission

      <!-- Content Management -->
      @permission('content_management_access')
        <li
          class="{{ request()->is('admin/blog-*') || request()->is('admin/pages*') || request()->is('admin/menus*') || request()->is('admin/sliders*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/blog-*') || request()->is('admin/pages*') || request()->is('admin/menus*') || request()->is('admin/sliders*') ? 'true' : 'false' }}">
            <i class="fas fa-edit"></i>
            <span>Content Management</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul
            class="{{ request()->is('admin/blog-*') || request()->is('admin/pages*') || request()->is('admin/menus*') || request()->is('admin/sliders*') ? 'mm-show' : '' }}">
            @permission('blog_access')
              <li
                class="{{ request()->is('admin/blog-posts') && !request()->is('admin/blog-posts/published') && !request()->is('admin/blog-posts/draft') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.blog-posts.index') }}">Blog Posts</a></li>
              <li class="{{ request()->is('admin/blog-posts/published') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.blog-posts.published') }}">Published Posts</a></li>
              <li class="{{ request()->is('admin/blog-posts/draft') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.blog-posts.draft') }}">Draft Posts</a></li>
              <li class="{{ request()->is('admin/blog-categories*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.blog-categories.index') }}">Blog Categories</a></li>
              <li class="{{ request()->is('admin/blog-tags*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.blog-tags.index') }}">Blog Tags</a></li>
            @endpermission
            @permission('page_access')
              <li class="{{ request()->is('admin/pages') && !request()->is('admin/pages/active') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.pages.index') }}">Pages</a></li>
              <li class="{{ request()->is('admin/pages/active') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.pages.active') }}">Active Pages</a></li>
            @endpermission
            @permission('menu_access')
              <li class="{{ request()->is('admin/menus*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.menus.index') }}">Navigation Menus</a></li>
            @endpermission
            @permission('slider_access')
              <li class="{{ request()->is('admin/sliders*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.sliders.index') }}">Sliders</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission

      <!-- Media Management -->
      @permission('media_management_access')
        <li class="{{ request()->is('admin/media*') ? 'mm-active' : '' }}">
          <a href="{{ route('admin.media.index') }}">
            <i class="fas fa-images"></i>
            <span>Media Library</span>
          </a>
        </li>
      @endpermission


      <!-- Tax Management -->
      @permission('tax_management_access')
        <li class="{{ request()->is('admin/tax-*') ? 'mm-active' : '' }}">
          <a href="#" aria-expanded="{{ request()->is('admin/tax-*') ? 'true' : 'false' }}">
            <i class="fas fa-calculator"></i>
            <span>Tax Management</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul class="{{ request()->is('admin/tax-*') ? 'mm-show' : '' }}">
            @permission('tax_class_access')
              <li class="{{ request()->is('admin/tax-classes*') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.tax-classes.index') }}">Tax Classes</a></li>
            @endpermission
            @permission('tax_rate_access')
              <li
                class="{{ request()->is('admin/tax-rates') && !request()->is('admin/tax-rates/calculator') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.tax-rates.index') }}">Tax Rates</a></li>
              <li class="{{ request()->is('admin/tax-rates/calculator') ? 'mm-active' : '' }}"><a
                  href="{{ route('admin.tax-rates.calculator') }}">Tax Calculator</a></li>
            @endpermission
          </ul>
        </li>
      @endpermission


      <!-- System Settings -->
      @permission('system_management_access')
        <li
          class="{{ request()->is('admin/settings*') || request()->is('admin/currency-rates*') ? 'mm-active' : '' }}">
          <a href="#"
            aria-expanded="{{ request()->is('admin/settings*') || request()->is('admin/currency-rates*') ? 'true' : 'false' }}">
            <i class="fas fa-cog"></i>
            <span>System Settings</span>
            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
          </a>
          <ul
            class="{{ request()->is('admin/settings*') || request()->is('admin/currency-rates*') ? 'mm-show' : '' }}">
            <li class="{{ request()->is('admin/settings') && !request()->is('admin/settings/*') ? 'mm-active' : '' }}">
              <a href="{{ route('admin.settings.index') }}">General Settings</a></li>
            <li class="{{ request()->is('admin/settings/general') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.settings.general') }}">General</a></li>
            <li class="{{ request()->is('admin/settings/mail') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.settings.mail') }}">Mail Settings</a></li>
            <li class="{{ request()->is('admin/settings/payment') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.settings.payment') }}">Payment Settings</a></li>
            <li class="{{ request()->is('admin/settings/shipping') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.settings.shipping') }}">Shipping Settings</a></li>
            <li class="{{ request()->is('admin/settings/tax') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.settings.tax') }}">Tax Settings</a></li>
            <li class="{{ request()->is('admin/settings/seo') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.settings.seo') }}">SEO Settings</a></li>
            <li class="{{ request()->is('admin/settings/social') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.settings.social') }}">Social Settings</a></li>
            <li class="{{ request()->is('admin/settings/analytics') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.settings.analytics') }}">Analytics Settings</a></li>
            <li
              class="{{ request()->is('admin/currency-rates') && !request()->is('admin/currency-rates/history') ? 'mm-active' : '' }}">
              <a href="{{ route('admin.currency-rates.index') }}">Currency Rates</a></li>
            <li class="{{ request()->is('admin/currency-rates/history') ? 'mm-active' : '' }}"><a
                href="{{ route('admin.currency-rates.history') }}">Rate History</a></li>
          </ul>
        </li>
      @endpermission


      <!-- Profile -->
      <li class="{{ request()->is('admin/profile*') ? 'mm-active' : '' }}">
        <a href="{{ route('profile.edit') }}">
          <i class="fas fa-user"></i>
          <span>Profile</span>
        </a>
      </li>

      <!-- Logout -->
      <li>
        <form method="POST" action="{{ route('logout') }}" style="display: none;" id="logout-form">
          @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </nav>
</div>
