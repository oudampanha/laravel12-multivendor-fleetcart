<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductsTableSeeder extends Seeder
{
    /**
     * Seed the `order_products` table.
     *
     * Columns:
     *
     *   - id: id
     *   - order_id: unsignedBigInteger
     *   - product_id: unsignedBigInteger
     *   - vendor_id: unsignedBigInteger (nullable)
     *   - product_variant_id: unsignedBigInteger (nullable)
     *   - unit_price: decimal
     *   - qty: integer
     *   - line_total: decimal
     *   - vendor_commission: decimal (default=0)
     *   - vendor_status: enum (default='pending')
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'order_id' => null,
            //     'product_id' => null,
            //     'vendor_id' => null,
            //     'product_variant_id' => null,
            //     'unit_price' => null,
            //     'qty' => null,
            //     'line_total' => null,
            //     'vendor_commission' => null,
            //     'vendor_status' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('order_products')->insert($rows);
        }
    }
}
