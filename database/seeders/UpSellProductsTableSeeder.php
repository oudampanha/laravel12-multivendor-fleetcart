<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpSellProductsTableSeeder extends Seeder
{
    /**
     * Seed the `up_sell_products` table.
     *
     * Columns:
     *
     *   - product_id: unsignedBigInteger
     *   - up_sell_product_id: unsignedBigInteger
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_id' => null,
            //     'up_sell_product_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('up_sell_products')->insert($rows);
        }
    }
}
