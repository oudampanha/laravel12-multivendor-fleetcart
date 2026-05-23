<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponProductsTableSeeder extends Seeder
{
    /**
     * Seed the `coupon_products` table.
     *
     * Columns:
     *
     *   - coupon_id: unsignedBigInteger
     *   - product_id: unsignedBigInteger
     *   - exclude: boolean (default=false)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'coupon_id' => null,
            //     'product_id' => null,
            //     'exclude' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('coupon_products')->insert($rows);
        }
    }
}
