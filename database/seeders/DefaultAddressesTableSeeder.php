<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultAddressesTableSeeder extends Seeder
{
    /**
     * Seed the `default_addresses` table.
     *
     * Columns:
     *
     *   - id: id
     *   - customer_id: unsignedBigInteger
     *   - address_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'customer_id' => null,
            //     'address_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('default_addresses')->insert($rows);
        }
    }
}
