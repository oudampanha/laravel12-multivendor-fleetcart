<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxRatesTableSeeder extends Seeder
{
    /**
     * Seed the `tax_rates` table.
     *
     * Columns:
     *
     *   - id: id
     *   - tax_class_id: unsignedBigInteger
     *   - country: string
     *   - state: string
     *   - city: string
     *   - zip: string
     *   - rate: decimal
     *   - position: unsignedInteger
     *   - deleted_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'tax_class_id' => null,
            //     'country' => null,
            //     'state' => null,
            //     'city' => null,
            //     'zip' => null,
            //     'rate' => null,
            //     'position' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('tax_rates')->insert($rows);
        }
    }
}
