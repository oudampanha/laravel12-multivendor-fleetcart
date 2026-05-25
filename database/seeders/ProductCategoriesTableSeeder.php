<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Seed the `product_categories` table.
     *
     * Columns:
     *
     *   - product_id: unsignedBigInteger
     *   - category_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_id' => null,
            //     'category_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('product_categories')->insert($rows);
        }
    }
}
