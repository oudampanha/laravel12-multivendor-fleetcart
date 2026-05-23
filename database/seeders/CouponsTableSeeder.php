<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponsTableSeeder extends Seeder
{
    /**
     * Seed the `coupons` table.
     *
     * Columns:
     *
     *   - id: id
     *   - vendor_id: unsignedBigInteger (nullable)
     *   - code: string
     *   - value: decimal (nullable)
     *   - is_percent: boolean (default=false)
     *   - free_shipping: boolean (default=false)
     *   - minimum_spend: decimal (nullable)
     *   - maximum_spend: decimal (nullable)
     *   - usage_limit_per_coupon: unsignedInteger (nullable)
     *   - usage_limit_per_customer: unsignedInteger (nullable)
     *   - used: integer (default=0)
     *   - is_active: boolean (default=true)
     *   - start_date: date (nullable)
     *   - end_date: date (nullable)
     *   - deleted_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'vendor_id' => null,
            //     'code' => null,
            //     'value' => null,
            //     'is_percent' => null,
            //     'free_shipping' => null,
            //     'minimum_spend' => null,
            //     'maximum_spend' => null,
            //     'usage_limit_per_coupon' => null,
            //     'usage_limit_per_customer' => null,
            //     'used' => null,
            //     'is_active' => null,
            //     'start_date' => null,
            //     'end_date' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('coupons')->insert($rows);
        }
    }
}
