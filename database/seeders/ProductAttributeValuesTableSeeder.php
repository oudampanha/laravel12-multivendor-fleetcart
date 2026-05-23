<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductAttributeValuesTableSeeder extends Seeder
{
    /**
     * Seed the `product_attribute_values` table.
     *
     * Columns:
     *
     *   - product_attribute_id: unsignedBigInteger
     *   - attribute_value_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_attribute_id' => null,
            //     'attribute_value_id' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('product_attribute_values')->insert($rows);
        }
    }
}
