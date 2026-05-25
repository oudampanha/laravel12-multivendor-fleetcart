<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponCategoriesTableSeeder extends Seeder
{
    /**
     * Seed the `coupon_categories` table.
     *
     * Columns:
     *
     *   - coupon_id: unsignedBigInteger
     *   - category_id: unsignedBigInteger
     *   - exclude: boolean (default=false)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'coupon_id' => null,
            //     'category_id' => null,
            //     'exclude' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('coupon_categories')->insert($rows);
        }
    }
}
