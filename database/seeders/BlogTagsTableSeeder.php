<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogTagsTableSeeder extends Seeder
{
    /**
     * Seed the `blog_tags` table.
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
                'slug' => 'news',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        if (!empty($rows)) {
            DB::table('blog_tags')->insert($rows);
        }
    }
}
