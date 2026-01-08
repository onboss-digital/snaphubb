<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class ResetAdminPasswordSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'admin@snaphubb.com')->first();
        
        if ($user) {
            $user->update([
                'password' => bcrypt('Meta10k@@')
            ]);
            echo "✅ Admin password reset to: Meta10k@@\n";
        }
    }
}
