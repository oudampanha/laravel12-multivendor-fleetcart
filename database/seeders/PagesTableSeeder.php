<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesTableSeeder extends Seeder
{
    /**
     * Seed the `pages` table.
     *
     * Columns:
     *
     *   - id: id
     *   - slug: string
     *   - is_active: boolean (default=true)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'slug' => null,
            //     'is_active' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('pages')->insert($rows);
        }
    }
}
