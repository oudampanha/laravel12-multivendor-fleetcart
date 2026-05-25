<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductVariationsTableSeeder extends Seeder
{
    /**
     * Seed the `order_product_variations` table.
     *
     * Columns:
     *
     *   - id: id
     *   - order_product_id: unsignedBigInteger
     *   - variation_id: unsignedBigInteger
     *   - type: string
     *   - value: string
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'order_product_id' => null,
            //     'variation_id' => null,
            //     'type' => null,
            //     'value' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('order_product_variations')->insert($rows);
        }
    }
}
