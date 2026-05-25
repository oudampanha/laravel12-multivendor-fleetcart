<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder
{
    /**
     * Seed the `attributes` table.
     *
     * Columns:
     *
     *   - id: id
     *   - attribute_set_id: unsignedBigInteger
     *   - slug: string (nullable)
     *   - is_filterable: boolean (default=false)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'attribute_set_id' => null,
            //     'slug' => null,
            //     'is_filterable' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('attributes')->insert($rows);
        }
    }
}
