<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'admin@snaphubb.com')->first();
if (! $user) { echo "no user\n"; exit; }

echo "roles() relation count: " . $user->roles()->count() . "\n";
echo "roles() rows: \n";
print_r($user->roles()->get()->toArray());

echo "roles dynamic property: \n";
print_r($user->roles->toArray());
