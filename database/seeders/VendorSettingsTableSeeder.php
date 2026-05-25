<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSettingsTableSeeder extends Seeder
{
    /**
     * Seed the `vendor_settings` table.
     *
     * Columns:
     *
     *   - id: id
     *   - vendor_id: unsignedBigInteger
     *   - key: string
     *   - value: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'vendor_id' => null,
            //     'key' => null,
            //     'value' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('vendor_settings')->insert($rows);
        }
    }
}
