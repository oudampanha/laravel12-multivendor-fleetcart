<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    /**
     * Seed the `orders` table.
     *
     * Columns:
     *
     *   - id: id
     *   - customer_id: unsignedBigInteger (nullable)
     *   - customer_email: string
     *   - customer_phone: string (nullable)
     *   - customer_first_name: string
     *   - customer_last_name: string
     *   - billing_first_name: string
     *   - billing_last_name: string
     *   - billing_address_1: string
     *   - billing_address_2: string (nullable)
     *   - billing_city: string
     *   - billing_state: string
     *   - billing_zip: string
     *   - billing_country: string
     *   - shipping_first_name: string
     *   - shipping_last_name: string
     *   - shipping_address_1: string
     *   - shipping_address_2: string (nullable)
     *   - shipping_city: string
     *   - shipping_state: string
     *   - shipping_zip: string
     *   - shipping_country: string
     *   - sub_total: decimal
     *   - shipping_method: string (nullable)
     *   - shipping_cost: decimal
     *   - coupon_id: unsignedBigInteger (nullable)
     *   - discount: decimal
     *   - total: decimal
     *   - payment_method: string
     *   - currency: string
     *   - currency_rate: decimal
     *   - locale: string
     *   - status: string
     *   - note: text (nullable)
     *   - tracking_reference: text (nullable)
     *   - deleted_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'customer_id' => null,
            //     'customer_email' => null,
            //     'customer_phone' => null,
            //     'customer_first_name' => null,
            //     'customer_last_name' => null,
            //     'billing_first_name' => null,
            //     'billing_last_name' => null,
            //     'billing_address_1' => null,
            //     'billing_address_2' => null,
            //     'billing_city' => null,
            //     'billing_state' => null,
            //     'billing_zip' => null,
            //     'billing_country' => null,
            //     'shipping_first_name' => null,
            //     'shipping_last_name' => null,
            //     'shipping_address_1' => null,
            //     'shipping_address_2' => null,
            //     'shipping_city' => null,
            //     'shipping_state' => null,
            //     'shipping_zip' => null,
            //     'shipping_country' => null,
            //     'sub_total' => null,
            //     'shipping_method' => null,
            //     'shipping_cost' => null,
            //     'coupon_id' => null,
            //     'discount' => null,
            //     'total' => null,
            //     'payment_method' => null,
            //     'currency' => null,
            //     'currency_rate' => null,
            //     'locale' => null,
            //     'status' => null,
            //     'note' => null,
            //     'tracking_reference' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('orders')->insert($rows);
        }
    }
}
