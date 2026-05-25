<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTakeItemsTableSeeder extends Seeder
{
    /**
     * Seed the `stock_take_items` table.
     *
     * Columns:
     *
     *   - id: id
     *   - stock_take_id: unsignedBigInteger
     *   - product_id: unsignedBigInteger
     *   - product_variant_id: unsignedBigInteger (nullable)
     *   - expected_quantity: integer
     *   - counted_quantity: integer (nullable)
     *   - difference: integer (nullable)
     *   - unit_cost: decimal (default=0)
     *   - notes: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'stock_take_id' => null,
            //     'product_id' => null,
            //     'product_variant_id' => null,
            //     'expected_quantity' => null,
            //     'counted_quantity' => null,
            //     'difference' => null,
            //     'unit_cost' => null,
            //     'notes' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('stock_take_items')->insert($rows);
        }
    }
}
