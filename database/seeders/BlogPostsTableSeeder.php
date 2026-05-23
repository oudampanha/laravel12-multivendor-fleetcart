<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogPostsTableSeeder extends Seeder
{
    /**
     * Seed the `blog_posts` table.
     *
     * Columns:
     *
     *   - id: id
     *   - user_id: unsignedBigInteger
     *   - blog_category_id: unsignedBigInteger (nullable)
     *   - slug: string
     *   - publish_status: string
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'user_id' => null,
            //     'blog_category_id' => null,
            //     'slug' => null,
            //     'publish_status' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('blog_posts')->insert($rows);
        }
    }
}
