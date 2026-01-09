<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

$email = 'admin@snaphubb.com';
$user = User::where('email', $email)->first();
if (! $user) {
    echo "User not found: {$email}\n";
    exit(1);
}

$role = Role::where('name', 'admin')->first();
if (! $role) {
    echo "Role admin not found\n";
    exit(1);
}

try {
    $user->assignRole($role);
    echo "assignRole returned\n";
} catch (\Throwable $e) {
    echo "assignRole error: " . $e->getMessage() . "\n";
}

echo "Model roles now:\n";
foreach (DB::table('model_has_roles')->get() as $r) {
    echo " - role_id={$r->role_id}, model_id={$r->model_id}\n";
}

echo "User getRoleNames: [" . $user->getRoleNames()->implode(',') . "]\n";
