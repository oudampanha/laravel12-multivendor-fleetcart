<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlashSalesTableSeeder extends Seeder
{
    /**
     * Seed the `flash_sales` table.
     *
     * Columns:
     *
     *   - id: id
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            // ],
        ];

        if (! empty($rows)) {
            DB::table('flash_sales')->insert($rows);
        }
    }
}
