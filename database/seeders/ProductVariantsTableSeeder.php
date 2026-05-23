<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantsTableSeeder extends Seeder
{
    /**
     * Seed the `product_variants` table.
     *
     * Columns:
     *
     *   - id: id
     *   - uid: string
     *   - uids: text
     *   - product_id: unsignedBigInteger
     *   - name: string
     *   - price: decimal (nullable)
     *   - special_price: decimal (nullable)
     *   - special_price_type: string (nullable)
     *   - special_price_start: date (nullable)
     *   - special_price_end: date (nullable)
     *   - selling_price: decimal (nullable)
     *   - sku: string (nullable)
     *   - manage_stock: boolean (nullable)
     *   - qty: integer (nullable)
     *   - in_stock: boolean (nullable)
     *   - is_default: boolean (nullable)
     *   - is_active: boolean (nullable)
     *   - position: unsignedInteger (nullable)
     *   - deleted_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'uid' => null,
            //     'uids' => null,
            //     'product_id' => null,
            //     'name' => null,
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
            //     'is_default' => null,
            //     'is_active' => null,
            //     'position' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('product_variants')->insert($rows);
        }
    }
}
