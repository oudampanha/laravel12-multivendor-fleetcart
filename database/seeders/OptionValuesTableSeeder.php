<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionValuesTableSeeder extends Seeder
{
    /**
     * Seed the `option_values` table.
     *
     * Columns:
     *
     *   - id: id
     *   - option_id: unsignedBigInteger
     *   - price: decimal (nullable)
     *   - price_type: string
     *   - position: unsignedInteger
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'option_id' => null,
            //     'price' => null,
            //     'price_type' => null,
            //     'position' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('option_values')->insert($rows);
        }
    }
}
