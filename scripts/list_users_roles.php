<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::all();
if ($users->isEmpty()) {
    echo "No users found\n";
    exit(0);
}

foreach ($users as $u) {
    $roles = method_exists($u, 'roles') ? $u->roles->pluck('name')->implode(',') : '';
    $perms = method_exists($u, 'getAllPermissions') ? $u->getAllPermissions()->pluck('name')->implode(',') : '';
    echo "{$u->id} {$u->email} | Roles: {$roles} | Perms: {$perms}\n";
}

exit(0);
