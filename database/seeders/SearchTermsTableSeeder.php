<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchTermsTableSeeder extends Seeder
{
    /**
     * Seed the `search_terms` table.
     *
     * Columns:
     *
     *   - id: id
     *   - term: string
     *   - results: unsignedInteger
     *   - hits: unsignedInteger (default=0)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'term' => null,
            //     'results' => null,
            //     'hits' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('search_terms')->insert($rows);
        }
    }
}
