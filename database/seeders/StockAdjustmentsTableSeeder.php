<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockAdjustmentsTableSeeder extends Seeder
{
    /**
     * Seed the `stock_adjustments` table.
     *
     * Columns:
     *
     *   - id: id
     *   - code: string
     *   - warehouse_id: unsignedBigInteger
     *   - adjustment_date: date
     *   - reason: enum
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
            //     'warehouse_id' => null,
            //     'adjustment_date' => null,
            //     'reason' => null,
            //     'status' => null,
            //     'notes' => null,
            //     'created_by' => null,
            //     'posted_by' => null,
            //     'posted_at' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('stock_adjustments')->insert($rows);
        }
    }
}
