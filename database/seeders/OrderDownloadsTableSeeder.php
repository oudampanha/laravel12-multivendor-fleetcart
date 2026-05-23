<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderDownloadsTableSeeder extends Seeder
{
    /**
     * Seed the `order_downloads` table.
     *
     * Columns:
     *
     *   - id: id
     *   - order_id: unsignedBigInteger
     *   - file_id: unsignedBigInteger
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'order_id' => null,
            //     'file_id' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('order_downloads')->insert($rows);
        }
    }
}
