<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTagsTableSeeder extends Seeder
{
    /**
     * Seed the `product_tags` table.
     *
     * Columns:
     *
     *   - product_id: unsignedBigInteger
     *   - tag_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_id' => null,
            //     'tag_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('product_tags')->insert($rows);
        }
    }
}
