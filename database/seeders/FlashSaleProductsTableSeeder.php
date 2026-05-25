<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlashSaleProductsTableSeeder extends Seeder
{
    /**
     * Seed the `flash_sale_products` table.
     *
     * Columns:
     *
     *   - id: id
     *   - flash_sale_id: unsignedBigInteger
     *   - product_id: unsignedBigInteger
     *   - end_date: date
     *   - price: decimal
     *   - qty: integer
     *   - position: integer
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'flash_sale_id' => null,
            //     'product_id' => null,
            //     'end_date' => null,
            //     'price' => null,
            //     'qty' => null,
            //     'position' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('flash_sale_products')->insert($rows);
        }
    }
}
