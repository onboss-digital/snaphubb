<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserMultiProfilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_multi_profiles')->delete();
        
        \DB::table('user_multi_profiles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'name' => 'Super Admin',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/6vyLOEOWVZ.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 2,
                'name' => 'Ivan Norris',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/4L0sjjH3aQ.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 3,
                'name' => 'John Doe',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/btdiqqrnvl.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 4,
                'name' => 'Tristan Erickson',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/nvDyiQccLk.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 5,
                'name' => 'Felix Harris',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/8f11BHLdIX.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => 6,
                'name' => 'Harry Victoria',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/JSxhNUqBDJ.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            6 => 
            array (
                'id' => 7,
                'user_id' => 7,
                'name' => 'Jorge Perez',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/h2Ar8QdaY2.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            7 => 
            array (
                'id' => 8,
                'user_id' => 8,
                'name' => 'Joy Hanry',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/lC4Pzmj9XQ.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            8 => 
            array (
                'id' => 9,
                'user_id' => 9,
                'name' => 'Deborah Thomas',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/EAyyRmYPKz.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            9 => 
            array (
                'id' => 10,
                'user_id' => 10,
                'name' => 'Katie Brown',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/lKsHGgz1W2.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            10 => 
            array (
                'id' => 11,
                'user_id' => 11,
                'name' => 'Dorothy Erickson',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/6MDV13n23a.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            11 => 
            array (
                'id' => 12,
                'user_id' => 12,
                'name' => 'Lisa Lucas',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/WftB2bRzV7.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            12 => 
            array (
                'id' => 13,
                'user_id' => 13,
                'name' => 'Tracy Jones',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/4wdVsm8hQQ.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
            13 => 
            array (
                'id' => 14,
                'user_id' => 14,
                'name' => 'Stella Green',
                'avatar' => 'http://192.168.1.58:8000/storage/avatars/agbkyxBmCC.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-10-22 09:19:57',
                'updated_at' => '2024-10-22 09:19:57',
            ),
        ));
        
        
    }
}