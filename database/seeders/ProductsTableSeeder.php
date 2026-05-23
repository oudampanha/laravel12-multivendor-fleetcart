<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Seed the `products` table.
     *
     * Columns:
     *
     *   - id: id
     *   - vendor_id: unsignedBigInteger (nullable)
     *   - brand_id: unsignedBigInteger (nullable)
     *   - tax_class_id: unsignedBigInteger (nullable)
     *   - slug: string
     *   - price: decimal (nullable)
     *   - special_price: decimal (nullable)
     *   - special_price_type: string (nullable)
     *   - special_price_start: date (nullable)
     *   - special_price_end: date (nullable)
     *   - selling_price: decimal (nullable)
     *   - sku: string (nullable)
     *   - manage_stock: boolean (default=false)
     *   - qty: integer (nullable)
     *   - in_stock: boolean (default=true)
     *   - viewed: unsignedInteger (default=0)
     *   - is_active: boolean (default=true)
     *   - is_virtual: boolean (default=false)
     *   - new_from: dateTime (nullable)
     *   - new_to: dateTime (nullable)
     *   - vendor_status: enum (default='pending')
     *   - vendor_rejection_reason: text (nullable)
     *   - deleted_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'vendor_id' => null,
            //     'brand_id' => null,
            //     'tax_class_id' => null,
            //     'slug' => null,
            //     'price' => null,
            //     'special_price' => null,
            //     'special_price_type' => null,
            //     'special_price_start' => null,
            //     'special_price_end' => null,
            //     'selling_price' => null,
            //     'sku' => null,
            //     'manage_stock' => null,
            //     'qty' => null,
            //     'in_stock' => null,
            //     'viewed' => null,
            //     'is_active' => null,
            //     'is_virtual' => null,
            //     'new_from' => null,
            //     'new_to' => null,
            //     'vendor_status' => null,
            //     'vendor_rejection_reason' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('products')->insert($rows);
        }
    }
}
