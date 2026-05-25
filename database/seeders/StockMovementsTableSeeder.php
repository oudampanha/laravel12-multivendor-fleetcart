<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockMovementsTableSeeder extends Seeder
{
    /**
     * Seed the `stock_movements` table.
     *
     * Columns:
     *
     *   - id: id
     *   - warehouse_id: unsignedBigInteger
     *   - product_id: unsignedBigInteger
     *   - product_variant_id: unsignedBigInteger (nullable)
     *   - type: enum
     *   - reference_type: string (nullable)
     *   - reference_id: unsignedBigInteger (nullable)
     *   - quantity: integer
     *   - balance_after: integer
     *   - unit_cost: decimal (default=0)
     *   - total_cost: decimal (default=0)
     *   - batch_number: string (nullable)
     *   - expiry_date: date (nullable)
     *   - notes: text (nullable)
     *   - user_id: unsignedBigInteger (nullable)
     *   - created_at: timestamp
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'warehouse_id' => null,
            //     'product_id' => null,
            //     'product_variant_id' => null,
            //     'type' => null,
            //     'reference_type' => null,
            //     'reference_id' => null,
            //     'quantity' => null,
            //     'balance_after' => null,
            //     'unit_cost' => null,
            //     'total_cost' => null,
            //     'batch_number' => null,
            //     'expiry_date' => null,
            //     'notes' => null,
            //     'user_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('stock_movements')->insert($rows);
        }
    }
}
