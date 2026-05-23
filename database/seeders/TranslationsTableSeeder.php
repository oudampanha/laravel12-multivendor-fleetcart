<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationsTableSeeder extends Seeder
{
    /**
     * Seed the `translations` table.
     *
     * Columns:
     *
     *   - id: id
     *   - translatable_type: string
     *   - translatable_id: unsignedBigInteger
     *   - locale: string
     *   - field: string
     *   - value: longText (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'translatable_type' => null,
            //     'translatable_id' => null,
            //     'locale' => null,
            //     'field' => null,
            //     'value' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('translations')->insert($rows);
        }
    }
}
