<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Seed the `transactions` table.
     *
     * Columns:
     *
     *   - id: id
     *   - order_id: unsignedBigInteger
     *   - transaction_id: string
     *   - payment_method: string
     *   - deleted_at: timestamp (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'order_id' => null,
            //     'transaction_id' => null,
            //     'payment_method' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('transactions')->insert($rows);
        }
    }
}
