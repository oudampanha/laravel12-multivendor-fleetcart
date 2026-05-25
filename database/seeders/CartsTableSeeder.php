<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartsTableSeeder extends Seeder
{
    /**
     * Seed the `carts` table.
     *
     * Columns:
     *
     *   - id: string
     *   - data: longText
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'data' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('carts')->insert($rows);
        }
    }
}
