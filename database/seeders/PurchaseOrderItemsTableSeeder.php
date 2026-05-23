<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderItemsTableSeeder extends Seeder
{
    /**
     * Seed the `purchase_order_items` table.
     *
     * Columns:
     *
     *   - id: id
     *   - purchase_order_id: unsignedBigInteger
     *   - product_id: unsignedBigInteger
     *   - product_variant_id: unsignedBigInteger (nullable)
     *   - quantity_ordered: integer
     *   - quantity_received: integer (default=0)
     *   - unit_cost: decimal
     *   - tax_rate: decimal (default=0)
     *   - discount: decimal (default=0)
     *   - line_total: decimal (default=0)
     *   - notes: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'purchase_order_id' => null,
            //     'product_id' => null,
            //     'product_variant_id' => null,
            //     'quantity_ordered' => null,
            //     'quantity_received' => null,
            //     'unit_cost' => null,
            //     'tax_rate' => null,
            //     'discount' => null,
            //     'line_total' => null,
            //     'notes' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('purchase_order_items')->insert($rows);
        }
    }
}
