<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrossSellProductsTableSeeder extends Seeder
{
    /**
     * Seed the `cross_sell_products` table.
     *
     * Columns:
     *
     *   - product_id: unsignedBigInteger
     *   - cross_sell_product_id: unsignedBigInteger
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_id' => null,
            //     'cross_sell_product_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('cross_sell_products')->insert($rows);
        }
    }
}
