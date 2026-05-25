<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntityMediaTableSeeder extends Seeder
{
    /**
     * Seed the `entity_media` table.
     *
     * Columns:
     *
     *   - id: id
     *   - file_id: unsignedBigInteger
     *   - entity_type: string
     *   - entity_id: unsignedBigInteger
     *   - zone: string
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'file_id' => null,
            //     'entity_type' => null,
            //     'entity_id' => null,
            //     'zone' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('entity_media')->insert($rows);
        }
    }
}
