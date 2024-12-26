<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EpisodeDownloadMappingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('episode_download_mapping')->delete();
        
        
        
    }
}