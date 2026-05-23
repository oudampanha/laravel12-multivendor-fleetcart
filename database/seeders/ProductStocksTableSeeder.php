<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductStocksTableSeeder extends Seeder
{
    /**
     * Seed the `product_stocks` table.
     *
     * Columns:
     *
     *   - id: id
     *   - product_id: unsignedBigInteger
     *   - product_variant_id: unsignedBigInteger (nullable)
     *   - warehouse_id: unsignedBigInteger
     *   - quantity: integer (default=0)
     *   - reserved_quantity: integer (default=0)
     *   - reorder_level: integer (default=0)
     *   - reorder_quantity: integer (default=0)
     *   - average_cost: decimal (default=0)
     *   - last_movement_at: dateTime (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'product_id' => null,
            //     'product_variant_id' => null,
            //     'warehouse_id' => null,
            //     'quantity' => null,
            //     'reserved_quantity' => null,
            //     'reorder_level' => null,
            //     'reorder_quantity' => null,
            //     'average_cost' => null,
            //     'last_movement_at' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('product_stocks')->insert($rows);
        }
    }
}
