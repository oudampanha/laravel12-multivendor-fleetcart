<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariationsTableSeeder extends Seeder
{
    /**
     * Seed the `variations` table.
     *
     * Columns:
     *
     *   - id: id
     *   - uid: string
     *   - type: string
     *   - is_global: boolean (default=true)
     *   - position: unsignedInteger (nullable)
     *   - deleted_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'uid' => null,
            //     'type' => null,
            //     'is_global' => null,
            //     'position' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('variations')->insert($rows);
        }
    }
}
