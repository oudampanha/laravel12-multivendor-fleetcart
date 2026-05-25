<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodsReceiptItemsTableSeeder extends Seeder
{
    /**
     * Seed the `goods_receipt_items` table.
     *
     * Columns:
     *
     *   - id: id
     *   - goods_receipt_id: unsignedBigInteger
     *   - purchase_order_item_id: unsignedBigInteger (nullable)
     *   - product_id: unsignedBigInteger
     *   - product_variant_id: unsignedBigInteger (nullable)
     *   - quantity_received: integer
     *   - unit_cost: decimal (default=0)
     *   - batch_number: string (nullable)
     *   - expiry_date: date (nullable)
     *   - notes: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'goods_receipt_id' => null,
            //     'purchase_order_item_id' => null,
            //     'product_id' => null,
            //     'product_variant_id' => null,
            //     'quantity_received' => null,
            //     'unit_cost' => null,
            //     'batch_number' => null,
            //     'expiry_date' => null,
            //     'notes' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('goods_receipt_items')->insert($rows);
        }
    }
}
