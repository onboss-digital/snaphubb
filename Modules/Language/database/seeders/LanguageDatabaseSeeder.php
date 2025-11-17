<?php

namespace Modules\Language\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Language\Models\Language;

class LanguageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks (driver-aware)
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        /*
         * Languages Seed
         * ------------------
         */

        // DB::table('languages')->truncate();
        // echo "Truncate: languages \n";

        \Modules\Language\Models\Language::factory()->count(20)->create();
        $rows = Language::all();
        echo " Insert: languages \n\n";

        // Enable foreign key checks (driver-aware)
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }
}
