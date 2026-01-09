<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Set user_type based on role
$users = User::all();
foreach ($users as $u) {
    $roles = $u->getRoleNames()->toArray();
    if (in_array('admin', $roles)) {
        $u->user_type = 'admin';
    } else {
        $u->user_type = 'user';
    }
    $u->save();
    echo "Set {$u->email} user_type => {$u->user_type}\n";
}

echo "Done\n";
