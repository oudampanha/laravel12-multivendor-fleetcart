<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressesTableSeeder extends Seeder
{
    /**
     * Seed the `addresses` table.
     *
     * Columns:
     *
     *   - id: id
     *   - customer_id: unsignedBigInteger
     *   - first_name: string
     *   - last_name: string
     *   - address_1: string
     *   - address_2: string (nullable)
     *   - city: string
     *   - state: string
     *   - zip: string
     *   - country: string
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'customer_id' => null,
            //     'first_name' => null,
            //     'last_name' => null,
            //     'address_1' => null,
            //     'address_2' => null,
            //     'city' => null,
            //     'state' => null,
            //     'zip' => null,
            //     'country' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('addresses')->insert($rows);
        }
    }
}
