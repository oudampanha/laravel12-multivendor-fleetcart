<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogPostBlogTagTableSeeder extends Seeder
{
    /**
     * Seed the `blog_post_blog_tag` table.
     *
     * Columns:
     *
     *   - blog_post_id: unsignedBigInteger
     *   - blog_tag_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'blog_post_id' => null,
            //     'blog_tag_id' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('blog_post_blog_tag')->insert($rows);
        }
    }
}
