<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OtpVerificationsTableSeeder extends Seeder
{
    /**
     * Seed the `otp_verifications` table.
     *
     * Columns:
     *
     *   - id: id
     *   - email: string
     *   - otp: string
     *   - expires_at: timestamp
     *   - is_used: boolean (default=false)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'email' => null,
            //     'otp' => null,
            //     'expires_at' => null,
            //     'is_used' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('otp_verifications')->insert($rows);
        }
    }
}
