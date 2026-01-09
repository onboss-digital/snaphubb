<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'admin@snaphubb.com')->first();
if (! $user) { echo "no user\n"; exit; }

echo "hasRole('admin'): "; var_export($user->hasRole('admin')); echo "\n";
echo "getRoleNames(): "; print_r($user->getRoleNames()->toArray());
