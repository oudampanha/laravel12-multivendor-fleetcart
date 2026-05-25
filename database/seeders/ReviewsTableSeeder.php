<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Seed the `reviews` table.
     *
     * Columns:
     *
     *   - id: id
     *   - reviewer_id: unsignedBigInteger (nullable)
     *   - product_id: unsignedBigInteger
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
            //     'reviewer_id' => null,
            //     'product_id' => null,
            //     'rating' => null,
            //     'reviewer_name' => null,
            //     'comment' => null,
            //     'is_approved' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('reviews')->insert($rows);
        }
    }
}
