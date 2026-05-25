<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeValuesTableSeeder extends Seeder
{
    /**
     * Seed the `attribute_values` table.
     *
     * Columns:
     *
     *   - id: id
     *   - attribute_id: unsignedBigInteger
     *   - position: unsignedInteger
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'attribute_id' => null,
            //     'position' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('attribute_values')->insert($rows);
        }
    }
}
