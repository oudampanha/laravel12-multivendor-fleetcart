<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockAdjustmentItemsTableSeeder extends Seeder
{
    /**
     * Seed the `stock_adjustment_items` table.
     *
     * Columns:
     *
     *   - id: id
     *   - stock_adjustment_id: unsignedBigInteger
     *   - product_id: unsignedBigInteger
     *   - product_variant_id: unsignedBigInteger (nullable)
     *   - system_quantity: integer
     *   - actual_quantity: integer
     *   - difference: integer
     *   - unit_cost: decimal (default=0)
     *   - notes: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'stock_adjustment_id' => null,
            //     'product_id' => null,
            //     'product_variant_id' => null,
            //     'system_quantity' => null,
            //     'actual_quantity' => null,
            //     'difference' => null,
            //     'unit_cost' => null,
            //     'notes' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('stock_adjustment_items')->insert($rows);
        }
    }
}
