<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehousesTableSeeder extends Seeder
{
    /**
     * Seed the `warehouses` table.
     *
     * Columns:
     *
     *   - id: id
     *   - code: string
     *   - name: string
     *   - vendor_id: unsignedBigInteger (nullable)
     *   - address: text (nullable)
     *   - city: string (nullable)
     *   - state: string (nullable)
     *   - country: string (nullable)
     *   - zip: string (nullable)
     *   - phone: string (nullable)
     *   - email: string (nullable)
     *   - contact_person: string (nullable)
     *   - is_active: boolean (default=true)
     *   - is_default: boolean (default=false)
     *   - position: unsignedInteger (default=0)
     *   - notes: text (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     *   - deleted_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            [
                'code' => 'MAIN',
                'name' => 'Main Warehouse',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        if (!empty($rows)) {
            DB::table('warehouses')->insert($rows);
        }
    }
}
