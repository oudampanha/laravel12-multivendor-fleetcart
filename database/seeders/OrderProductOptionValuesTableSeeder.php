<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductOptionValuesTableSeeder extends Seeder
{
    /**
     * Seed the `order_product_option_values` table.
     *
     * Columns:
     *
     *   - order_product_option_id: unsignedBigInteger
     *   - option_value_id: unsignedBigInteger
     *   - price: decimal (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'order_product_option_id' => null,
            //     'option_value_id' => null,
            //     'price' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('order_product_option_values')->insert($rows);
        }
    }
}
