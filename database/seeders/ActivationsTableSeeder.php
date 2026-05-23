<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivationsTableSeeder extends Seeder
{
    /**
     * Seed the `activations` table.
     *
     * Columns:
     *
     *   - id: id
     *   - user_id: unsignedBigInteger
     *   - code: string
     *   - completed: boolean (default=false)
     *   - completed_at: dateTime (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'user_id' => null,
            //     'code' => null,
            //     'completed' => null,
            //     'completed_at' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('activations')->insert($rows);
        }
    }
}
