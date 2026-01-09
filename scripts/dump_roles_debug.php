<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

echo "Roles in DB:\n";
foreach (Role::all() as $r) {
    echo " - {$r->id}: {$r->name} (guard: {$r->guard_name})\n";
}

echo "\nModel role assignments (model_has_roles):\n";
$rows = DB::table('model_has_roles')->get();
foreach ($rows as $row) {
    echo " - role_id={$row->role_id}, model_type={$row->model_type}, model_id={$row->model_id}\n";
}

echo "\nUser role names via getRoleNames():\n";
foreach (User::all() as $u) {
    echo " - {$u->id} {$u->email}: [" . $u->getRoleNames()->implode(',') . "]\n";
}
