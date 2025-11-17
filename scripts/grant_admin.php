<?php
// scripts/grant_admin.php
// Run from project root: php scripts/grant_admin.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

$email = env('MAIL_ADMIN_EMAIL', 'marketing.boss7@gmail.com');
echo "Target email: $email\n";

$user = User::where('email', $email)->first();
if (! $user) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}

$role = Role::firstOrCreate(['name' => 'admin']);
$perms = Permission::all();
if ($perms->count()) {
    $role->syncPermissions($perms);
    echo "Synced " . $perms->count() . " permissions to role 'admin'\n";
} else {
    echo "No permissions found to sync\n";
}

$user->assignRole($role);
echo "Assigned role 'admin' to user id: {$user->id}\n";

// Clear permission cache
PermissionRegistrar::forgetCachedPermissions();

echo "Done.\n";
