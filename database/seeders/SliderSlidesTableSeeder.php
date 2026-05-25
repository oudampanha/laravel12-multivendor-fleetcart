<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SliderSlidesTableSeeder extends Seeder
{
    /**
     * Seed the `slider_slides` table.
     *
     * Columns:
     *
     *   - id: id
     *   - slider_id: unsignedBigInteger
     *   - options: text (nullable)
     *   - call_to_action_url: string (nullable)
     *   - open_in_new_window: boolean (nullable)
     *   - position: integer (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'slider_id' => null,
            //     'options' => null,
            //     'call_to_action_url' => null,
            //     'open_in_new_window' => null,
            //     'position' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('slider_slides')->insert($rows);
        }
    }
}
