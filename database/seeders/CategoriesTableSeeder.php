<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Seed the `categories` table.
     *
     * Columns:
     *
     *   - id: id
     *   - parent_id: unsignedBigInteger (nullable)
     *   - slug: string
     *   - position: unsignedInteger (nullable)
     *   - image: string (nullable)
     *   - is_searchable: boolean (default=true)
     *   - is_active: boolean (default=true)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'parent_id' => null,
            //     'slug' => null,
            //     'position' => null,
            //     'image' => null,
            //     'is_searchable' => null,
            //     'is_active' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('categories')->insert($rows);
        }
    }
}
