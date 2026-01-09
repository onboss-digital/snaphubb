<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$roleName = 'admin';
$role = Role::where('name', $roleName)->first();
if (! $role) {
    echo "Role not found: {$roleName}\n";
    exit(1);
}

$permissions = Permission::pluck('name')->toArray();
if (empty($permissions)) {
    echo "No permissions found in the database.\n";
    exit(1);
}

$role->syncPermissions($permissions);

echo "Granted " . count($permissions) . " permissions to role '{$roleName}'.\n";
