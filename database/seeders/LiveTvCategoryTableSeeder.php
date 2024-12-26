<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LiveTvCategoryTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('live_tv_category')->delete();
        
        \DB::table('live_tv_category')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'News & Current Affairs',
                'file_url' => 'news_current_affairs.png',
                'description' => 'Stay informed with the latest updates from around the world. This category brings you live news broadcasts, in-depth analysis, and breaking news coverage. From politics to finance, and global events to local happenings, never miss a moment of what\'s happening. 📰🌍🕒',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:48',
                'updated_at' => '2024-10-22 09:20:48',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Sports & Action',
                'file_url' => 'sports_action.png',
                'description' => 'Catch all the live sports action from your favorite games and tournaments. Whether it\'s football, basketball, tennis, or any other sport, this category covers live matches, expert commentary, and thrilling highlights. Cheer for your teams and witness unforgettable moments. 🏆⚽🏀',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:48',
                'updated_at' => '2024-10-22 09:20:48',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Entertainment & Variety',
                'file_url' => 'entertainment_variety.png',
                'description' => 'Enjoy a diverse range of live entertainment shows, from reality TV and talent competitions to talk shows and award ceremonies. This category offers something for everyone, featuring your favorite stars and hosts bringing you laughter, drama, and excitement. 🎤🎬🎉',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:48',
                'updated_at' => '2024-10-22 09:20:48',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Music & Concerts',
                'file_url' => 'music_concerts.png',
                'description' => 'Experience live music like never before with concerts, festivals, and exclusive performances from top artists. This category brings the stage to your screen, allowing you to enjoy your favorite genres and discover new talents from the comfort of your home. 🎸🎤🎶',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:48',
                'updated_at' => '2024-10-22 09:20:48',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Educational & Documentary',
                'file_url' => 'educational_documentary.png',
                'description' => 'Expand your knowledge with live educational programs and documentaries covering a wide range of topics. From science and history to nature and technology, this category provides informative content that enlightens and inspires. Ideal for curious minds of all ages. 📚🔬🌿',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:48',
                'updated_at' => '2024-10-22 09:20:48',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}