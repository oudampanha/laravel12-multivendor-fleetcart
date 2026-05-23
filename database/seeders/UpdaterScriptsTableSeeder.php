<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdaterScriptsTableSeeder extends Seeder
{
    /**
     * Seed the `updater_scripts` table.
     *
     * Columns:
     *
     *   - id: id
     *   - script: string
     */
    public function run(): void
    {
        $rows = [
            // [
            //     'script' => null,
            // ],
        ];

        if (!empty($rows)) {
            DB::table('updater_scripts')->insert($rows);
        }
    }
}
