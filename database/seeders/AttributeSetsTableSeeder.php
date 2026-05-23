<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSetsTableSeeder extends Seeder
{
    /**
     * Seed the `attribute_sets` table.
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
            [
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        if (!empty($rows)) {
            DB::table('attribute_sets')->insert($rows);
        }
    }
}
