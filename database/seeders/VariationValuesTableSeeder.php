<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariationValuesTableSeeder extends Seeder
{
    /**
     * Seed the `variation_values` table.
     *
     * Columns:
     *
     *   - id: id
     *   - uid: string
     *   - variation_id: unsignedBigInteger
     *   - value: string (nullable)
     *   - position: unsignedInteger (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'uid' => null,
            //     'variation_id' => null,
            //     'value' => null,
            //     'position' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('variation_values')->insert($rows);
        }
    }
}
