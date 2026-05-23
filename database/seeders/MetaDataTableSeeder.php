<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetaDataTableSeeder extends Seeder
{
    /**
     * Seed the `meta_data` table.
     *
     * Columns:
     *
     *   - id: id
     *   - entity_type: string
     *   - entity_id: unsignedBigInteger
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'entity_type' => null,
            //     'entity_id' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('meta_data')->insert($rows);
        }
    }
}
