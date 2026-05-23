<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxClassesTableSeeder extends Seeder
{
    /**
     * Seed the `tax_classes` table.
     *
     * Columns:
     *
     *   - id: id
     *   - based_on: string
     *   - deleted_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            [
                'based_on' => 'shipping_address',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        if (!empty($rows)) {
            DB::table('tax_classes')->insert($rows);
        }
    }
}
