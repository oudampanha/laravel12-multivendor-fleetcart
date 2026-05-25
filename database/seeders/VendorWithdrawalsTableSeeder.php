<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorWithdrawalsTableSeeder extends Seeder
{
    /**
     * Seed the `vendor_withdrawals` table.
     *
     * Columns:
     *
     *   - id: id
     *   - vendor_id: unsignedBigInteger
     *   - amount: decimal
     *   - method: enum
     *   - status: enum (default='pending')
     *   - note: text (nullable)
     *   - admin_note: text (nullable)
     *   - processed_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'vendor_id' => null,
            //     'amount' => null,
            //     'method' => null,
            //     'status' => null,
            //     'note' => null,
            //     'admin_note' => null,
            //     'processed_at' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('vendor_withdrawals')->insert($rows);
        }
    }
}
