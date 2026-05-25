<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencyRatesTableSeeder extends Seeder
{
    /**
     * Seed the `currency_rates` table.
     *
     * Columns:
     *
     *   - id: id
     *   - currency: string
     *   - rate: decimal
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'currency' => null,
            //     'rate' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('currency_rates')->insert($rows);
        }
    }
}
