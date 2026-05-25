<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusTableSeeder extends Seeder
{
    /**
     * Seed the `menus` table.
     *
     * Columns:
     *
     *   - id: id
     *   - is_active: boolean (default=true)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'is_active' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('menus')->insert($rows);
        }
    }
}
