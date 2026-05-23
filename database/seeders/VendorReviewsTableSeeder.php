<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorReviewsTableSeeder extends Seeder
{
    /**
     * Seed the `vendor_reviews` table.
     *
     * Columns:
     *
     *   - id: id
     *   - vendor_id: unsignedBigInteger
     *   - customer_id: unsignedBigInteger (nullable)
     *   - order_id: unsignedBigInteger (nullable)
     *   - rating: integer
     *   - reviewer_name: string
     *   - comment: text
     *   - is_approved: boolean (default=false)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'vendor_id' => null,
            //     'customer_id' => null,
            //     'order_id' => null,
            //     'rating' => null,
            //     'reviewer_name' => null,
            //     'comment' => null,
            //     'is_approved' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('vendor_reviews')->insert($rows);
        }
    }
}
