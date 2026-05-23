<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersistencesTableSeeder extends Seeder
{
    /**
     * Seed the `persistences` table.
     *
     * Columns:
     *
     *   - id: id
     *   - user_id: unsignedBigInteger
     *   - code: string
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'user_id' => null,
            //     'code' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('persistences')->insert($rows);
        }
    }
}
