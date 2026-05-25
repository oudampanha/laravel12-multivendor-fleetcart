<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageLinesTableSeeder extends Seeder
{
    /**
     * Seed the `language_lines` table.
     *
     * Columns:
     *
     *   - id: id
     *   - group: string
     *   - key: string
     *   - text: json
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'group' => null,
            //     'key' => null,
            //     'text' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('language_lines')->insert($rows);
        }
    }
}
