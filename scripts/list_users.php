<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::with('roles')->limit(50)->get();
foreach ($users as $u) {
    $roles = $u->roles->pluck('name')->toArray();
    echo "{$u->id}\t{$u->name}\t{$u->email}\troles:" . implode(',', $roles) . "\n";
}
