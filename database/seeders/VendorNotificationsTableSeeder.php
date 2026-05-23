<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorNotificationsTableSeeder extends Seeder
{
    /**
     * Seed the `vendor_notifications` table.
     *
     * Columns:
     *
     *   - id: id
     *   - vendor_id: unsignedBigInteger
     *   - type: string
     *   - title: string
     *   - message: text
     *   - data: json (nullable)
     *   - is_read: boolean (default=false)
     *   - read_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'vendor_id' => null,
            //     'type' => null,
            //     'title' => null,
            //     'message' => null,
            //     'data' => null,
            //     'is_read' => null,
            //     'read_at' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('vendor_notifications')->insert($rows);
        }
    }
}
