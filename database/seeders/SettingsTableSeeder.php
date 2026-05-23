<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Seed the `settings` table.
     *
     * Columns:
     *
     *   - id: id
     *   - key: string
     *   - is_translatable: boolean (default=false)
     *   - plain_value: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'key' => null,
            //     'is_translatable' => null,
            //     'plain_value' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('settings')->insert($rows);
        }
    }
}
