<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorShippingZonesTableSeeder extends Seeder
{
    /**
     * Seed the `vendor_shipping_zones` table.
     *
     * Columns:
     *
     *   - id: id
     *   - vendor_id: unsignedBigInteger
     *   - name: string
     *   - countries: json
     *   - states: json (nullable)
     *   - zip_codes: json (nullable)
     *   - shipping_method: enum
     *   - rate: decimal (nullable)
     *   - minimum_order: decimal (nullable)
     *   - is_active: boolean (default=true)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'vendor_id' => null,
            //     'name' => null,
            //     'countries' => null,
            //     'states' => null,
            //     'zip_codes' => null,
            //     'shipping_method' => null,
            //     'rate' => null,
            //     'minimum_order' => null,
            //     'is_active' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('vendor_shipping_zones')->insert($rows);
        }
    }
}
