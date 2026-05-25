<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelatedProductsTableSeeder extends Seeder
{
    /**
     * Seed the `related_products` table.
     *
     * Columns:
     *
     *   - product_id: unsignedBigInteger
     *   - related_product_id: unsignedBigInteger
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_id' => null,
            //     'related_product_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('related_products')->insert($rows);
        }
    }
}
