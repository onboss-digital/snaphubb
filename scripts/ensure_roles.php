<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$adminEmail = 'admin@snaphubb.com';
$admin = User::where('email', $adminEmail)->first();
if (! $admin) {
    echo "Admin user not found: {$adminEmail}\n";
    exit(1);
}

// Ensure 'admin' role exists
if (! Role::where('name', 'admin')->exists()) {
    Role::create(['name' => 'admin']);
    echo "Created role 'admin'.\n";
}

// Ensure 'user' role exists
if (! Role::where('name', 'user')->exists()) {
    Role::create(['name' => 'user']);
    echo "Created role 'user'.\n";
}

// Set admin user role to admin
$admin->syncRoles(['admin']);
echo "Set {$adminEmail} role => admin\n";

// Set all other users to role 'user' (except admin)
$others = User::where('email', '!=', $adminEmail)->get();
foreach ($others as $u) {
    $u->syncRoles(['user']);
    echo "Set {$u->email} role => user\n";
}

echo "Roles ensured.\n";
