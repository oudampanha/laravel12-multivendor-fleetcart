<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishListsTableSeeder extends Seeder
{
    /**
     * Seed the `wish_lists` table.
     *
     * Columns:
     *
     *   - user_id: unsignedBigInteger
     *   - product_id: unsignedBigInteger
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'user_id' => null,
            //     'product_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('wish_lists')->insert($rows);
        }
    }
}
