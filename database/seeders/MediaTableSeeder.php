<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaTableSeeder extends Seeder
{
    /**
     * Seed the `media` table.
     *
     * Columns:
     *
     *   - id: id
     *   - file_name: string
     *   - original_name: string
     *   - file_path: string
     *   - file_url: string
     *   - folder_path: string (nullable)
     *   - mime_type: string
     *   - file_extension: string
     *   - file_size: bigInteger
     *   - disk: string
     *   - file_type: string
     *   - metadata: json (nullable)
     *   - user_id: unsignedBigInteger (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'file_name' => null,
            //     'original_name' => null,
            //     'file_path' => null,
            //     'file_url' => null,
            //     'folder_path' => null,
            //     'mime_type' => null,
            //     'file_extension' => null,
            //     'file_size' => null,
            //     'disk' => null,
            //     'file_type' => null,
            //     'metadata' => null,
            //     'user_id' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('media')->insert($rows);
        }
    }
}
