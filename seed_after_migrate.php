<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "Aguardando migrações completarem...\n";
sleep(10);

// Verificar se roles existem
$adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'], ['title' => 'Admin']);
$demoRole = Role::firstOrCreate(['name' => 'demo_admin', 'guard_name' => 'web'], ['title' => 'Demo Admin']);
$userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web'], ['title' => 'User']);

echo "✓ Roles criados\n";

// Criar usuários
$admin = User::firstOrCreate(
    ['email' => 'admin@streamit.com'],
    [
        'name' => 'Admin User',
        'password' => Hash::make('12345678'),
        'user_type' => 'admin',
        'status' => 1,
        'email_verified_at' => now(),
    ]
);
$admin->assignRole('admin');
echo "✓ Admin criado: admin@streamit.com\n";

$demo = User::firstOrCreate(
    ['email' => 'demo@streamit.com'],
    [
        'name' => 'Demo Admin',
        'password' => Hash::make('12345678'),
        'user_type' => 'demo_admin',
        'status' => 1,
        'email_verified_at' => now(),
    ]
);
$demo->assignRole('demo_admin');
echo "✓ Demo criado: demo@streamit.com\n";

echo "\n✓ Setup completo!\n";
echo "Credenciais de teste:\n";
echo "  Email: admin@streamit.com\n";
echo "  Senha: 12345678\n";
