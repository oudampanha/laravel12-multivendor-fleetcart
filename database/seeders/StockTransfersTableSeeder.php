<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTransfersTableSeeder extends Seeder
{
    /**
     * Seed the `stock_transfers` table.
     *
     * Columns:
     *
     *   - id: id
     *   - code: string
     *   - from_warehouse_id: unsignedBigInteger
     *   - to_warehouse_id: unsignedBigInteger
     *   - transfer_date: date
     *   - shipped_at: dateTime (nullable)
     *   - received_at: dateTime (nullable)
     *   - status: enum (default='draft')
     *   - notes: text (nullable)
     *   - created_by: unsignedBigInteger (nullable)
     *   - shipped_by: unsignedBigInteger (nullable)
     *   - received_by: unsignedBigInteger (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     *   - deleted_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'code' => null,
            //     'from_warehouse_id' => null,
            //     'to_warehouse_id' => null,
            //     'transfer_date' => null,
            //     'shipped_at' => null,
            //     'received_at' => null,
            //     'status' => null,
            //     'notes' => null,
            //     'created_by' => null,
            //     'shipped_by' => null,
            //     'received_by' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('stock_transfers')->insert($rows);
        }
    }
}
