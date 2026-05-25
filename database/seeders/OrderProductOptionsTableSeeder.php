<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductOptionsTableSeeder extends Seeder
{
    /**
     * Seed the `order_product_options` table.
     *
     * Columns:
     *
     *   - id: id
     *   - order_product_id: unsignedBigInteger
     *   - option_id: unsignedBigInteger
     *   - value: text (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'order_product_id' => null,
            //     'option_id' => null,
            //     'value' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('order_product_options')->insert($rows);
        }
    }
}
