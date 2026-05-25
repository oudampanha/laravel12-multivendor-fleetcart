<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionUserTableSeeder extends Seeder
{
    /**
     * Seed the `permission_user` table.
     *
     * Columns:
     *
     *   - user_id: unsignedBigInteger
     *   - permission_id: unsignedBigInteger
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'user_id' => null,
            //     'permission_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('permission_user')->insert($rows);
        }
    }
}
