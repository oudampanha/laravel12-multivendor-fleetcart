<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorsTableSeeder extends Seeder
{
    /**
     * Seed the `vendors` table.
     *
     * Columns:
     *
     *   - id: id
     *   - user_id: unsignedBigInteger
     *   - store_slug: string
     *   - store_email: string (nullable)
     *   - store_phone: string (nullable)
     *   - store_address: text (nullable)
     *   - store_city: string (nullable)
     *   - store_state: string (nullable)
     *   - store_country: string (nullable)
     *   - store_zip: string (nullable)
     *   - commission_rate: decimal (default=0)
     *   - is_active: boolean (default=true)
     *   - is_verified: boolean (default=false)
     *   - verified_at: timestamp (nullable)
     *   - balance: decimal (default=0)
     *   - bank_name: string (nullable)
     *   - bank_account_name: string (nullable)
     *   - bank_account_number: string (nullable)
     *   - bank_routing_number: string (nullable)
     *   - paypal_email: string (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'user_id' => null,
            //     'store_slug' => null,
            //     'store_email' => null,
            //     'store_phone' => null,
            //     'store_address' => null,
            //     'store_city' => null,
            //     'store_state' => null,
            //     'store_country' => null,
            //     'store_zip' => null,
            //     'commission_rate' => null,
            //     'is_active' => null,
            //     'is_verified' => null,
            //     'verified_at' => null,
            //     'balance' => null,
            //     'bank_name' => null,
            //     'bank_account_name' => null,
            //     'bank_account_number' => null,
            //     'bank_routing_number' => null,
            //     'paypal_email' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('vendors')->insert($rows);
        }
    }
}
