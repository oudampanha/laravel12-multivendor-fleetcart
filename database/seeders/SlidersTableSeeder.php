<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlidersTableSeeder extends Seeder
{
    /**
     * Seed the `sliders` table.
     *
     * Columns:
     *
     *   - id: id
     *   - speed: integer (nullable)
     *   - autoplay: boolean (nullable)
     *   - autoplay_speed: integer (nullable)
     *   - fade: boolean (default=false)
     *   - dots: boolean (nullable)
     *   - arrows: boolean (nullable)
     *   - created_at: timestamp (nullable)
     *   - updated_at: timestamp (nullable)
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'speed' => null,
            //     'autoplay' => null,
            //     'autoplay_speed' => null,
            //     'fade' => null,
            //     'dots' => null,
            //     'arrows' => null,
            // ],
        ];

        if (! empty($rows)) {
            DB::table('sliders')->insert($rows);
        }
    }
}
