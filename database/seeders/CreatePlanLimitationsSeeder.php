<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscriptions\Models\PlanLimitation;

class CreatePlanLimitationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $limitations = [
            [
                'title' => 'Device Limit',
                'slug' => 'device-limit',
                'description' => 'Maximum number of devices that can access the subscription',
                'status' => 1,
            ],
            [
                'title' => 'Simultaneous Streams',
                'slug' => 'simultaneous-streams',
                'description' => 'Maximum number of simultaneous streams allowed',
                'status' => 1,
            ],
            [
                'title' => 'Video Quality',
                'slug' => 'video-quality',
                'description' => 'Maximum video quality available',
                'status' => 1,
            ],
            [
                'title' => 'Download Limit',
                'slug' => 'download-limit',
                'description' => 'Maximum number of downloads allowed',
                'status' => 1,
            ],
            [
                'title' => 'Profile Limit',
                'slug' => 'profile-limit',
                'description' => 'Maximum number of user profiles',
                'status' => 1,
            ],
        ];

        foreach ($limitations as $limitation) {
            PlanLimitation::updateOrCreate(
                ['slug' => $limitation['slug']],
                $limitation
            );
        }

        echo "✅ Plan Limitations created successfully!\n";
    }
}
