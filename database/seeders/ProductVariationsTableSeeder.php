<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariationsTableSeeder extends Seeder
{
    /**
     * Seed the `product_variations` table.
     *
     * Columns:
     *
     *   - product_id: unsignedBigInteger
     *   - variation_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_id' => null,
            //     'variation_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('product_variations')->insert($rows);
        }
    }
}
