<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder
{
    /**
     * Seed the `tags` table.
     *
     * Columns:
     *
     *   - id: id
     *   - slug: string
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            [
                'slug' => 'featured',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        if (!empty($rows)) {
            DB::table('tags')->insert($rows);
        }
    }
}
