<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductAttributesTableSeeder extends Seeder
{
    /**
     * Seed the `product_attributes` table.
     *
     * Columns:
     *
     *   - id: id
     *   - product_id: unsignedBigInteger
     *   - attribute_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_id' => null,
            //     'attribute_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('product_attributes')->insert($rows);
        }
    }
}
