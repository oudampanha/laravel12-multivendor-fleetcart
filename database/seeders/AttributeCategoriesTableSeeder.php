<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeCategoriesTableSeeder extends Seeder
{
    /**
     * Seed the `attribute_categories` table.
     *
     * Columns:
     *
     *   - attribute_id: unsignedBigInteger
     *   - category_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'attribute_id' => null,
            //     'category_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('attribute_categories')->insert($rows);
        }
    }
}
