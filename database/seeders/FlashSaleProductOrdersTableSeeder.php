<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlashSaleProductOrdersTableSeeder extends Seeder
{
    /**
     * Seed the `flash_sale_product_orders` table.
     *
     * Columns:
     *
     *   - flash_sale_product_id: unsignedBigInteger
     *   - order_id: unsignedBigInteger
     *   - qty: integer
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'flash_sale_product_id' => null,
            //     'order_id' => null,
            //     'qty' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('flash_sale_product_orders')->insert($rows);
        }
    }
}
