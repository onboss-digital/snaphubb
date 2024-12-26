<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EntertainmentStreamContentMappingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('entertainment_stream_content_mapping')->delete();
        
        \DB::table('entertainment_stream_content_mapping')->insert(array (
            0 => 
            array (
                'id' => 1,
                'entertainment_id' => 21,
                'type' => 'YouTube',
                'quality' => '480p',
                'url' => 'https://youtu.be/5zSPGLoN9lQ?si=-BRLpMNIEJrnKm6f',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:22',
                'updated_at' => '2024-10-22 09:20:22',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'entertainment_id' => 21,
                'type' => 'YouTube',
                'quality' => '720p',
                'url' => 'https://youtu.be/5zSPGLoN9lQ?si=sygr-NcCZcS00O0p',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:22',
                'updated_at' => '2024-10-22 09:20:22',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'entertainment_id' => 21,
                'type' => 'YouTube',
                'quality' => '1080p',
                'url' => 'https://youtu.be/5zSPGLoN9lQ?si=eckyQwNdCsW6Pao6',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:22',
                'updated_at' => '2024-10-22 09:20:22',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'entertainment_id' => 21,
                'type' => 'YouTube',
                'quality' => '2K',
                'url' => 'https://youtu.be/5zSPGLoN9lQ?si=eckyQwNdCsW6Pao6',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:22',
                'updated_at' => '2024-10-22 09:20:22',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'entertainment_id' => 22,
                'type' => 'YouTube',
                'quality' => '480p',
                'url' => 'https://youtu.be/PdxPlbKFkaM?si=NydEmXECOvT1blJL',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:23',
                'updated_at' => '2024-10-22 09:20:23',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'entertainment_id' => 22,
                'type' => 'YouTube',
                'quality' => '720p',
                'url' => 'https://youtu.be/PdxPlbKFkaM?si=zaa1bCmFWRbSxZEB',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:23',
                'updated_at' => '2024-10-22 09:20:23',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'entertainment_id' => 22,
                'type' => 'YouTube',
                'quality' => '1080p',
                'url' => 'https://youtu.be/PdxPlbKFkaM?si=zlHHbalMgDJWz9Tp',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:23',
                'updated_at' => '2024-10-22 09:20:23',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'entertainment_id' => 22,
                'type' => 'YouTube',
                'quality' => '2K',
                'url' => 'https://youtu.be/PdxPlbKFkaM?si=qaZ1H82OVU3sVx0V',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:23',
                'updated_at' => '2024-10-22 09:20:23',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'entertainment_id' => 26,
                'type' => 'YouTube',
                'quality' => '480p',
                'url' => 'https://youtu.be/hlKFxyxOWIQ?si=d5nuCs6BYaIZJhSn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:24',
                'updated_at' => '2024-10-22 09:20:24',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'entertainment_id' => 26,
                'type' => 'YouTube',
                'quality' => '720p',
                'url' => 'https://youtu.be/hlKFxyxOWIQ?si=0NmD4yAoShQigs07',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:24',
                'updated_at' => '2024-10-22 09:20:24',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'entertainment_id' => 26,
                'type' => 'YouTube',
                'quality' => '1080p',
                'url' => 'https://youtu.be/hlKFxyxOWIQ?si=_KagBhO3OxIJxdyx',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:24',
                'updated_at' => '2024-10-22 09:20:24',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'entertainment_id' => 26,
                'type' => 'YouTube',
                'quality' => '1440p',
                'url' => 'https://youtu.be/hlKFxyxOWIQ?si=H096nrbHzq3_2hWF',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:24',
                'updated_at' => '2024-10-22 09:20:24',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'entertainment_id' => 27,
                'type' => 'YouTube',
                'quality' => '480p',
                'url' => 'https://youtu.be/U-KfnCpEEl4?si=Vc70N3_zFcBD0yR4',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:24',
                'updated_at' => '2024-10-22 09:20:24',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'entertainment_id' => 27,
                'type' => 'YouTube',
                'quality' => '720p',
                'url' => 'https://youtu.be/U-KfnCpEEl4?si=HUmROBp9MupZ_mAa',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:24',
                'updated_at' => '2024-10-22 09:20:24',
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'entertainment_id' => 27,
                'type' => 'YouTube',
                'quality' => '1080p',
                'url' => 'https://youtu.be/U-KfnCpEEl4?si=Wd3qSh7kodL-LvxC',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:24',
                'updated_at' => '2024-10-22 09:20:24',
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'entertainment_id' => 27,
                'type' => 'YouTube',
                'quality' => '1440p',
                'url' => 'https://youtu.be/U-KfnCpEEl4?si=QcjXOGpAHgsq1IJl',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:24',
                'updated_at' => '2024-10-22 09:20:24',
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'entertainment_id' => 29,
                'type' => 'YouTube',
                'quality' => '480p',
                'url' => 'https://youtu.be/so2XtxcSLHQ?si=ac0V29WoRwQyTNc7',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:25',
                'updated_at' => '2024-10-22 09:20:25',
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'entertainment_id' => 29,
                'type' => 'YouTube',
                'quality' => '720p',
                'url' => 'https://youtu.be/so2XtxcSLHQ?si=N97AW29RFILE1nZ0',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:25',
                'updated_at' => '2024-10-22 09:20:25',
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'entertainment_id' => 29,
                'type' => 'YouTube',
                'quality' => '1080p',
                'url' => 'https://youtu.be/so2XtxcSLHQ?si=yk7Cvs-MlKkT8MQy',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:25',
                'updated_at' => '2024-10-22 09:20:25',
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'entertainment_id' => 36,
                'type' => 'YouTube',
                'quality' => '480p',
                'url' => 'https://youtu.be/WltJPKFo_J4?si=zz4-zHhey7CK-d3N',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:27',
                'updated_at' => '2024-10-22 09:20:27',
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'entertainment_id' => 36,
                'type' => 'YouTube',
                'quality' => '720p',
                'url' => 'https://youtu.be/WltJPKFo_J4?si=0wIlovLv2RVlfjxt6',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:27',
                'updated_at' => '2024-10-22 09:20:27',
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'entertainment_id' => 36,
                'type' => 'YouTube',
                'quality' => '1080p',
                'url' => 'https://youtu.be/WltJPKFo_J4?si=BvEAyAoOkOdLnFr4',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:27',
                'updated_at' => '2024-10-22 09:20:27',
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'entertainment_id' => 40,
                'type' => 'YouTube',
                'quality' => '480p',
                'url' => 'https://youtu.be/22l6w8n9iCc?si=ojEDxNeMZ9DEFg8J',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:27',
                'updated_at' => '2024-10-22 09:20:27',
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'entertainment_id' => 40,
                'type' => 'YouTube',
                'quality' => '720p',
                'url' => 'https://youtu.be/22l6w8n9iCc?si=4gAqMfc4DUSUyg3G',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:27',
                'updated_at' => '2024-10-22 09:20:27',
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'entertainment_id' => 40,
                'type' => 'YouTube',
                'quality' => '1080p',
                'url' => 'https://youtu.be/22l6w8n9iCc?si=gVdCokIa76dm3gJy',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:27',
                'updated_at' => '2024-10-22 09:20:27',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}