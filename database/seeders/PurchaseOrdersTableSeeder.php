<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrdersTableSeeder extends Seeder
{
    /**
     * Seed the `purchase_orders` table.
     *
     * Columns:
     *
     *   - id: id
     *   - code: string
     *   - supplier_id: unsignedBigInteger
     *   - warehouse_id: unsignedBigInteger
     *   - vendor_id: unsignedBigInteger (nullable)
     *   - order_date: date
     *   - expected_date: date (nullable)
     *   - status: enum (default='draft')
     *   - subtotal: decimal (default=0)
     *   - tax_amount: decimal (default=0)
     *   - shipping_amount: decimal (default=0)
     *   - discount_amount: decimal (default=0)
     *   - total_amount: decimal (default=0)
     *   - currency_code: string (default='USD')
     *   - exchange_rate: decimal (default=1)
     *   - notes: text (nullable)
     *   - terms: text (nullable)
     *   - created_by: unsignedBigInteger (nullable)
     *   - approved_by: unsignedBigInteger (nullable)
     *   - approved_at: dateTime (nullable)
     *   - received_at: dateTime (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     *   - deleted_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'code' => null,
            //     'supplier_id' => null,
            //     'warehouse_id' => null,
            //     'vendor_id' => null,
            //     'order_date' => null,
            //     'expected_date' => null,
            //     'status' => null,
            //     'subtotal' => null,
            //     'tax_amount' => null,
            //     'shipping_amount' => null,
            //     'discount_amount' => null,
            //     'total_amount' => null,
            //     'currency_code' => null,
            //     'exchange_rate' => null,
            //     'notes' => null,
            //     'terms' => null,
            //     'created_by' => null,
            //     'approved_by' => null,
            //     'approved_at' => null,
            //     'received_at' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('purchase_orders')->insert($rows);
        }
    }
}
