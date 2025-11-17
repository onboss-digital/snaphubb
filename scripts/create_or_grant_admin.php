<?php
// scripts/create_or_grant_admin.php
// Usage: php scripts/create_or_grant_admin.php
// This will create or update the user with the given credentials, assign 'admin' role and sync permissions.

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

$email = 'onbossdigital@gmail.com';
$password = 'Meta10k@@';
$name = 'OnBoss Digital';

echo "Target user: $email\n";

$user = User::where('email', $email)->first();
if (! $user) {
    echo "User not found, creating...\n";
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => bcrypt($password),
    ]);
    echo "Created user id: {$user->id}\n";
} else {
    echo "User exists (id: {$user->id}), updating password...\n";
    $user->password = bcrypt($password);
    $user->save();
    echo "Password updated for user id: {$user->id}\n";
}

$role = Role::firstOrCreate(['name' => 'admin']);
$perms = Permission::all();
if ($perms->count()) {
    $role->syncPermissions($perms);
    echo "Synced {$perms->count()} permissions to role 'admin'\n";
} else {
    echo "No permissions found to sync to role 'admin' (0 permissions)\n";
}

if (! $user->hasRole('admin')) {
    $user->assignRole($role);
    echo "Assigned role 'admin' to user id: {$user->id}\n";
} else {
    echo "User already has role 'admin'\n";
}

// Clear permission cache (use instance)
$permissionRegistrar = app(Spatie\Permission\PermissionRegistrar::class);
$permissionRegistrar->forgetCachedPermissions();

echo "Permission cache cleared.\n";

// Show final roles for the user
$roles = $user->roles->pluck('name')->toArray();
echo "User roles: " . implode(',', $roles) . "\n";
