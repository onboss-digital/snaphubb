<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GenresTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('genres')->delete();
        
        \DB::table('genres')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Action',
                'slug' => 'action',
                'file_url' => 'action_genre.png',
                'description' => 'Action movies are packed with high-energy sequences, intense battles, and thrilling adventures. These films deliver non-stop excitement and adrenaline-pumping scenes that captivate audiences. 💥🏃‍♂️',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:09',
                'updated_at' => '2024-10-22 09:20:09',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Animation',
                'slug' => 'animation',
                'file_url' => 'animation_genre.png',
                'description' => 'Captivating animated stories that bring imaginative worlds and characters to life. These films use creative visuals and storytelling to enchant audiences of all ages. 🎨✨',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:09',
                'updated_at' => '2024-10-22 09:20:09',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Comedy',
                'slug' => 'comedy',
                'file_url' => 'comedy_genre.png',
                'description' => 'Light-hearted films designed to entertain and amuse with humor and wit. These movies offer a delightful escape filled with laughter and joy. 😂🎬',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:09',
                'updated_at' => '2024-10-22 09:20:09',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Historical',
                'slug' => 'historical',
                'file_url' => 'historical_genre.png',
                'description' => 'Movies that delve into significant historical events, figures, and eras. They offer a glimpse into the past, bringing history to life with compelling narratives. 📜🏰',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:09',
                'updated_at' => '2024-10-22 09:20:09',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Horror',
                'slug' => 'horror',
                'file_url' => 'horror_genre.png',
                'description' => 'Spine-chilling movies that evoke fear and suspense, often featuring supernatural elements. These films are designed to haunt and thrill viewers. 👻🕯️',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:09',
                'updated_at' => '2024-10-22 09:20:09',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Inspirational',
                'slug' => 'inspirational',
                'file_url' => 'inspirational_genre.png',
                'description' => 'Uplifting films that motivate and inspire with stories of courage, perseverance, and triumph. They often highlight the resilience of the human spirit. 🌟💪',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:09',
                'updated_at' => '2024-10-22 09:20:09',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Romantic',
                'slug' => 'romantic',
                'file_url' => 'romantic_genre.png',
                'description' => 'Heartwarming stories focusing on love, relationships, and the complexities of romance. These films explore the beauty and challenges of romantic connections. 💖🌹',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:09',
                'updated_at' => '2024-10-22 09:20:09',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Thriller',
                'slug' => 'thriller',
                'file_url' => 'thriller_genre.png',
                'description' => 'High-stakes scenarios and intense suspense that keep you on the edge of your seat. Expect unexpected twists and heart-pounding moments. 🔪🎬',
                'status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-10-22 09:20:09',
                'updated_at' => '2024-10-22 09:20:09',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}