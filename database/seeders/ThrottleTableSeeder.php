<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThrottleTableSeeder extends Seeder
{
    /**
     * Seed the `throttle` table.
     *
     * Columns:
     *
     *   - id: id
     *   - user_id: unsignedBigInteger (nullable)
     *   - type: string
     *   - ip: string (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'user_id' => null,
            //     'type' => null,
            //     'ip' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('throttle')->insert($rows);
        }
    }
}
