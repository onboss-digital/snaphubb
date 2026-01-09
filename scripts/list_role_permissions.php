<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;

$roleName = $argv[1] ?? 'admin';
$role = Role::where('name', $roleName)->first();
if (! $role) {
    echo "Role not found: {$roleName}\n";
    exit(1);
}

$perms = $role->permissions->pluck('name')->toArray();
echo "Role: {$roleName}\n";
echo "Permissions (" . count($perms) . "):\n";
foreach ($perms as $p) {
    echo " - {$p}\n";
}
