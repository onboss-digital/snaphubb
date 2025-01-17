<?php

namespace Database\Seeders\Auth;

use App\Events\Backend\UserCreated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\Address;
use App\Models\UserMultiProfile;
use Laravolt\Avatar\Facade as Avatar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class UserTableSeeder.
 */
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        // Add the master administrator, user id of 1
        $avatarPath = config('app.avatar_base_path');


        $users = [
            [
                'first_name' => 'Anderson',
                'last_name' => 'Isotton',
                'email' => 'anderson@isotton.com.br',
                'password' => Hash::make('12345678'),
                'mobile' => '+12123567890',
                'date_of_birth' => fake()->date,
                'file_url' => '/dummy-images/profile/admin/super_admin.png',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'admin',
                'is_subscribe' => 0,
            ],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@snaphubb.com',
                'password' => Hash::make('L9wd@scale'),
                'mobile' => '+12123567890',
                'date_of_birth' => fake()->date,
                'file_url' => '/dummy-images/profile/admin/super_admin.png',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'admin',
                'is_subscribe' => 0,
            ],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'contentmanager@snaphubb.com',
                'password' => Hash::make('12345678'),
                'mobile' => '+12123567890',
                'date_of_birth' => fake()->date,
                'file_url' => '/dummy-images/profile/admin/super_admin.png',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'content_manager',
                'is_subscribe' => 0,
            ],



        ];


        if (env('IS_DUMMY_DATA')) {
            foreach ($users as $key => $user_data) {
                $featureImage = $user_data['file_url'] ?? null;
                $userData = Arr::except($user_data, ['file_url']);
                $user = User::create($userData);

                $user->assignRole($user_data['user_type']);
                event(new UserCreated($user));


                if (isset($featureImage) &&  $featureImage !='') {


                  $profile_image = $this->uploadToSpaces($featureImage);

                 if ($profile_image) {
                    $user->file_url = extractFileNameFromUrl($profile_image);
                  }

                }

                $user->save();

                $this->createOrUpdateProfile($user);
            }

            Schema::enableForeignKeyConstraints();
        }
    }

    private function uploadToSpaces($publicPath)
    {

       $localFilePath = public_path($publicPath);
       $remoteFilePath = 'streamit-laravel/' . basename($publicPath);



       if (file_exists($localFilePath)) {           // Get the active storage disk from the environment
           $disk = env('ACTIVE_STORAGE', 'local');

           if ($disk === 'local') {
               // Store in the public directory for local storage
               Storage::disk($disk)->put('public/' . $remoteFilePath, file_get_contents($localFilePath));

            //    dd(asset('storage/' . $remoteFilePath));
               return asset('storage/' . $remoteFilePath);
           } else {

            // dd( $disk);
               // Upload to the specified storage disk
               Storage::disk($disk)->put($remoteFilePath, file_get_contents($localFilePath));
               return Storage::disk($disk)->url($remoteFilePath);
           }


       }

       return false;
   }




    private function createOrUpdateProfile(User $user)
    {
        $name = $user->first_name . ' ' . $user->last_name;

        UserMultiProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $name,
                'avatar' => $this->generateAvatar($name), // Generate avatar based on name
            ]
        );
    }

    private function attachFeatureImage($model, $publicPath)
    {
        if (!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('file_url');

        return $media->getUrl();
    }

    // Generate avatar based on user name and store it
    private function generateAvatar($name)
    {
        $name = $name ?? Str::random(10);

        $fileName = Str::random(10) . '.png';
        $filePath = 'avatars/' . $fileName;

        if (!Storage::exists('public/avatars')) {
            Storage::makeDirectory('public/avatars');
        }

        Avatar::create($name)->save(storage_path('app/public/' . $filePath));

        return asset('storage/' . $filePath);
    }
}
