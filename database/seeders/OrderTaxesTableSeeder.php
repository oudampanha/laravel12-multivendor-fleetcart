<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderTaxesTableSeeder extends Seeder
{
    /**
     * Seed the `order_taxes` table.
     *
     * Columns:
     *
     *   - order_id: unsignedBigInteger
     *   - tax_rate_id: unsignedBigInteger
     *   - amount: decimal
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'order_id' => null,
            //     'tax_rate_id' => null,
            //     'amount' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('order_taxes')->insert($rows);
        }
    }
}
