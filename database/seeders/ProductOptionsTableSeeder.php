<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductOptionsTableSeeder extends Seeder
{
    /**
     * Seed the `product_options` table.
     *
     * Columns:
     *
     *   - product_id: unsignedBigInteger
     *   - option_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_id' => null,
            //     'option_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('product_options')->insert($rows);
        }
    }
}
