<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuItemsTableSeeder extends Seeder
{
    /**
     * Seed the `menu_items` table.
     *
     * Columns:
     *
     *   - id: id
     *   - menu_id: unsignedBigInteger
     *   - parent_id: unsignedBigInteger (nullable)
     *   - category_id: unsignedBigInteger (nullable)
     *   - page_id: unsignedBigInteger (nullable)
     *   - type: string
     *   - url: string (nullable)
     *   - icon: string (nullable)
     *   - target: string
     *   - position: unsignedInteger (nullable)
     *   - is_root: boolean (default=false)
     *   - is_fluid: boolean (default=false)
     *   - is_active: boolean (default=true)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'menu_id' => null,
            //     'parent_id' => null,
            //     'category_id' => null,
            //     'page_id' => null,
            //     'type' => null,
            //     'url' => null,
            //     'icon' => null,
            //     'target' => null,
            //     'position' => null,
            //     'is_root' => null,
            //     'is_fluid' => null,
            //     'is_active' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('menu_items')->insert($rows);
        }
    }
}
