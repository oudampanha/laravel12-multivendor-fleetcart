<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTakesTableSeeder extends Seeder
{
    /**
     * Seed the `stock_takes` table.
     *
     * Columns:
     *
     *   - id: id
     *   - code: string
     *   - warehouse_id: unsignedBigInteger
     *   - count_date: date
     *   - status: enum (default='draft')
     *   - notes: text (nullable)
     *   - created_by: unsignedBigInteger (nullable)
     *   - completed_by: unsignedBigInteger (nullable)
     *   - completed_at: dateTime (nullable)
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
            //     'count_date' => null,
            //     'status' => null,
            //     'notes' => null,
            //     'created_by' => null,
            //     'completed_by' => null,
            //     'completed_at' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('stock_takes')->insert($rows);
        }
    }
}
