<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductVariationValuesTableSeeder extends Seeder
{
    /**
     * Seed the `order_product_variation_values` table.
     *
     * Columns:
     *
     *   - order_product_variation_id: unsignedBigInteger
     *   - variation_value_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'order_product_variation_id' => null,
            //     'variation_value_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('order_product_variation_values')->insert($rows);
        }
    }
}
