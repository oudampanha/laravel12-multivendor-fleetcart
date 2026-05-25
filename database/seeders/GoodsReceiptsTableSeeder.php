<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodsReceiptsTableSeeder extends Seeder
{
    /**
     * Seed the `goods_receipts` table.
     *
     * Columns:
     *
     *   - id: id
     *   - code: string
     *   - purchase_order_id: unsignedBigInteger (nullable)
     *   - supplier_id: unsignedBigInteger
     *   - warehouse_id: unsignedBigInteger
     *   - receipt_date: date
     *   - status: enum (default='draft')
     *   - notes: text (nullable)
     *   - created_by: unsignedBigInteger (nullable)
     *   - posted_by: unsignedBigInteger (nullable)
     *   - posted_at: dateTime (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     *   - deleted_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'code' => null,
            //     'purchase_order_id' => null,
            //     'supplier_id' => null,
            //     'warehouse_id' => null,
            //     'receipt_date' => null,
            //     'status' => null,
            //     'notes' => null,
            //     'created_by' => null,
            //     'posted_by' => null,
            //     'posted_at' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('goods_receipts')->insert($rows);
        }
    }
}
