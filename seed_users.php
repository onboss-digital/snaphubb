<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;

// Limpar usuários anteriores
User::query()->delete();

// Criar admin com senha que sabemos
User::create([
    'first_name' => 'Super',
    'last_name' => 'Admin',
    'email' => 'admin@streamit.com',
    'password' => bcrypt('12345678'),
    'email_verified_at' => now(),
    'status' => 1,
    'user_type' => 'admin',
]);

echo "✓ Admin criado: admin@streamit.com / 12345678\n";

// Criar demo admin
User::create([
    'first_name' => 'Demo',
    'last_name' => 'Admin',
    'email' => 'demo@streamit.com',
    'password' => bcrypt('12345678'),
    'email_verified_at' => now(),
    'status' => 1,
    'user_type' => 'demo_admin',
]);

echo "✓ Demo admin criado: demo@streamit.com / 12345678\n";

// Criar usuário comum
User::create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@streamit.com',
    'password' => bcrypt('secret'),
    'email_verified_at' => now(),
    'status' => 1,
    'user_type' => 'user',
]);

echo "✓ Usuário criado: john@streamit.com / secret\n";

$count = User::count();
echo "\nTotal de usuários: $count\n";
