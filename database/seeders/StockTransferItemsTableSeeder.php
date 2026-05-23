<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTransferItemsTableSeeder extends Seeder
{
    /**
     * Seed the `stock_transfer_items` table.
     *
     * Columns:
     *
     *   - id: id
     *   - stock_transfer_id: unsignedBigInteger
     *   - product_id: unsignedBigInteger
     *   - product_variant_id: unsignedBigInteger (nullable)
     *   - quantity_sent: integer
     *   - quantity_received: integer (default=0)
     *   - unit_cost: decimal (default=0)
     *   - notes: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'stock_transfer_id' => null,
            //     'product_id' => null,
            //     'product_variant_id' => null,
            //     'quantity_sent' => null,
            //     'quantity_received' => null,
            //     'unit_cost' => null,
            //     'notes' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('stock_transfer_items')->insert($rows);
        }
    }
}
