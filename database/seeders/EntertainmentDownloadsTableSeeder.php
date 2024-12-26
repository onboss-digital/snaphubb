<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EntertainmentDownloadsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('entertainment_downloads')->delete();
        
        
        
    }
}