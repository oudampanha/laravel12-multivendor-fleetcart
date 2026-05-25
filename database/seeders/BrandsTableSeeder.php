<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    /**
     * Seed the `brands` table.
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
            [
                'slug' => 'generic',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        if (! empty($rows)) {
            DB::table('brands')->insert($rows);
        }
    }
}
