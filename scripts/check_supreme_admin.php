<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = 'admin@snaphubb.com';
$user = User::where('email', $email)->first();
if (! $user) {
    echo "User not found: {$email}\n";
    exit(1);
}

$hasAdminRole = $user->hasRole('admin') ? 'yes' : 'no';
$countAdmins = User::role('admin')->count();

echo "User: {$user->email}\n";
echo "Has 'admin' role: {$hasAdminRole}\n";
echo "Total users with 'admin' role: {$countAdmins}\n";

if ($hasAdminRole === 'yes' && $countAdmins === 1) {
    echo "OK: This user is the sole admin/super-admin.\n";
} else {
    echo "Note: Either user lacks role 'admin' or there are other admin users.\n";
}
