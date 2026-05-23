<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorOrdersTableSeeder extends Seeder
{
    /**
     * Seed the `vendor_orders` table.
     *
     * Columns:
     *
     *   - id: id
     *   - vendor_id: unsignedBigInteger
     *   - order_id: unsignedBigInteger
     *   - sub_total: decimal
     *   - commission_amount: decimal
     *   - vendor_amount: decimal
     *   - status: enum (default='pending')
     *   - note: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'vendor_id' => null,
            //     'order_id' => null,
            //     'sub_total' => null,
            //     'commission_amount' => null,
            //     'vendor_amount' => null,
            //     'status' => null,
            //     'note' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('vendor_orders')->insert($rows);
        }
    }
}
