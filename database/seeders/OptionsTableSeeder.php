<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionsTableSeeder extends Seeder
{
    /**
     * Seed the `options` table.
     *
     * Columns:
     *
     *   - id: id
     *   - type: string
     *   - is_required: boolean (default=false)
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
            //     'type' => null,
            //     'is_required' => null,
            //     'is_global' => null,
            //     'position' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('options')->insert($rows);
        }
    }
}
