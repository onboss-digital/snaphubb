<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class FixAdminPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Get or create admin role
        $adminRole = Role::where('name', 'admin')->first() ?? 
            Role::create(['name' => 'admin', 'guard_name' => 'web', 'title' => 'Administrator']);
        
        // Get all permissions
        $allPermissions = Permission::all();
        
        // Sync all permissions to admin role
        $adminRole->syncPermissions($allPermissions);
        
        // Find first user (admin or any)
        $user = User::first();
        
        if ($user) {
            // Remove all roles first
            $user->syncRoles([]);
            // Assign admin role
            $user->assignRole('admin');
            // Sync all permissions
            $user->syncPermissions($allPermissions);
            echo "✅ User {$user->email} assigned as admin with all permissions!\n";
            echo "Total permissions: " . $allPermissions->count() . "\n";
        } else {
            // Create a new admin user
            $user = User::create([
                'name' => 'Administrator',
                'email' => 'admin@snaphubb.local',
                'password' => bcrypt('admin123456'),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('admin');
            $user->syncPermissions($allPermissions);
            echo "✅ New admin user created: admin@snaphubb.local\n";
            echo "Total permissions: " . $allPermissions->count() . "\n";
        }
    }
}
