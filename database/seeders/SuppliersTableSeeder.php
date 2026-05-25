<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Seed the `suppliers` table.
     *
     * Columns:
     *
     *   - id: id
     *   - code: string
     *   - name: string
     *   - contact_person: string (nullable)
     *   - email: string (nullable)
     *   - phone: string (nullable)
     *   - address: text (nullable)
     *   - city: string (nullable)
     *   - state: string (nullable)
     *   - country: string (nullable)
     *   - zip: string (nullable)
     *   - tax_number: string (nullable)
     *   - payment_terms: text (nullable)
     *   - vendor_id: unsignedBigInteger (nullable)
     *   - is_active: boolean (default=true)
     *   - notes: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     *   - deleted_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            [
                'code' => 'SUP-001',
                'name' => 'Default Supplier',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        if (! empty($rows)) {
            DB::table('suppliers')->insert($rows);
        }
    }
}
