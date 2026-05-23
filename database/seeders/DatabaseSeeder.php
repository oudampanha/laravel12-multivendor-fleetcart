<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Seeders are grouped by foreign-key dependency layer. Each individual
     * seeder lives in its own file (one per table) and ships with the column
     * structure pre-documented; fill the `$rows` arrays in each file as you
     * onboard real data.
     */
    public function run(): void
    {
        $this->call([
            // -----------------------------------------------------------------
            // Layer 0 - Auth & RBAC core (already present in this repo)
            // -----------------------------------------------------------------
            PermissionTableSeeder::class,
            RoleTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UserTableSeeder::class,
            RoleUserTableSeeder::class,
            PermissionUserTableSeeder::class,
            OtpVerificationsTableSeeder::class,

            // -----------------------------------------------------------------
            // Layer 0 - Independent lookups / config
            // -----------------------------------------------------------------
            AttributeSetsTableSeeder::class,
            AttributeCategoriesTableSeeder::class,
            BrandsTableSeeder::class,
            TaxClassesTableSeeder::class,
            VariationsTableSeeder::class,
            OptionsTableSeeder::class,
            TagsTableSeeder::class,
            CategoriesTableSeeder::class,
            FlashSalesTableSeeder::class,
            PagesTableSeeder::class,
            SlidersTableSeeder::class,
            MenusTableSeeder::class,
            SettingsTableSeeder::class,
            VendorSettingsTableSeeder::class,
            CurrencyRatesTableSeeder::class,
            MediaTableSeeder::class,
            MetaDataTableSeeder::class,
            TranslationsTableSeeder::class,
            LanguageLinesTableSeeder::class,
            UpdaterScriptsTableSeeder::class,
            ActivationsTableSeeder::class,
            PersistencesTableSeeder::class,
            RemindersTableSeeder::class,
            ThrottleTableSeeder::class,
            BlogCategoriesTableSeeder::class,
            BlogTagsTableSeeder::class,
            WarehousesTableSeeder::class,
            SuppliersTableSeeder::class,

            // -----------------------------------------------------------------
            // Layer 1 - Depends on layer 0
            // -----------------------------------------------------------------
            AttributesTableSeeder::class,
            AttributeValuesTableSeeder::class,
            TaxRatesTableSeeder::class,
            VariationValuesTableSeeder::class,
            OptionValuesTableSeeder::class,
            VendorsTableSeeder::class,
            FlashSaleProductsTableSeeder::class,
            SliderSlidesTableSeeder::class,
            MenuItemsTableSeeder::class,
            BlogPostsTableSeeder::class,
            EntityMediaTableSeeder::class,
            AddressesTableSeeder::class,
            DefaultAddressesTableSeeder::class,
            WishListsTableSeeder::class,
            CartsTableSeeder::class,
            SearchTermsTableSeeder::class,
            VendorNotificationsTableSeeder::class,
            VendorShippingZonesTableSeeder::class,

            // -----------------------------------------------------------------
            // Layer 2 - Vendor / product / stock parents
            // -----------------------------------------------------------------
            ProductsTableSeeder::class,
            CouponsTableSeeder::class,
            PurchaseOrdersTableSeeder::class,
            GoodsReceiptsTableSeeder::class,
            StockAdjustmentsTableSeeder::class,
            StockTransfersTableSeeder::class,
            StockTakesTableSeeder::class,
            BlogPostBlogTagTableSeeder::class,

            // -----------------------------------------------------------------
            // Layer 3 - Depends on products
            // -----------------------------------------------------------------
            ProductCategoriesTableSeeder::class,
            ProductAttributesTableSeeder::class,
            ProductAttributeValuesTableSeeder::class,
            ProductVariationsTableSeeder::class,
            ProductVariantsTableSeeder::class,
            ProductOptionsTableSeeder::class,
            ProductTagsTableSeeder::class,
            RelatedProductsTableSeeder::class,
            UpSellProductsTableSeeder::class,
            CrossSellProductsTableSeeder::class,
            CouponCategoriesTableSeeder::class,
            CouponProductsTableSeeder::class,
            ProductStocksTableSeeder::class,
            PurchaseOrderItemsTableSeeder::class,
            GoodsReceiptItemsTableSeeder::class,
            StockAdjustmentItemsTableSeeder::class,
            StockTransferItemsTableSeeder::class,
            StockTakeItemsTableSeeder::class,
            StockMovementsTableSeeder::class,

            // -----------------------------------------------------------------
            // Layer 4 - Orders, transactions, reviews
            // -----------------------------------------------------------------
            OrdersTableSeeder::class,
            OrderProductsTableSeeder::class,
            OrderProductOptionsTableSeeder::class,
            OrderProductOptionValuesTableSeeder::class,
            OrderProductVariationsTableSeeder::class,
            OrderProductVariationValuesTableSeeder::class,
            OrderTaxesTableSeeder::class,
            OrderDownloadsTableSeeder::class,
            TransactionsTableSeeder::class,
            VendorOrdersTableSeeder::class,
            VendorPayoutsTableSeeder::class,
            VendorWithdrawalsTableSeeder::class,
            ReviewsTableSeeder::class,
            VendorReviewsTableSeeder::class,
            FlashSaleProductOrdersTableSeeder::class,
        ]);
    }
}
