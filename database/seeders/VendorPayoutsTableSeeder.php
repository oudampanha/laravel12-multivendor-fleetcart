<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorPayoutsTableSeeder extends Seeder
{
    /**
     * Seed the `vendor_payouts` table.
     *
     * Columns:
     *
     *   - id: id
     *   - vendor_id: unsignedBigInteger
     *   - amount: decimal
     *   - status: enum (default='pending')
     *   - method: enum
     *   - reference_number: string (nullable)
     *   - note: text (nullable)
     *   - paid_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'vendor_id' => null,
            //     'amount' => null,
            //     'status' => null,
            //     'method' => null,
            //     'reference_number' => null,
            //     'note' => null,
            //     'paid_at' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('vendor_payouts')->insert($rows);
        }
    }
}
