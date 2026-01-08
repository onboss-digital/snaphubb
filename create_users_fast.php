<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "Criando roles...\n";

// Criar roles
$roles = ['admin', 'demo_admin', 'user'];
foreach ($roles as $roleName) {
    Role::firstOrCreate(
        ['name' => $roleName, 'guard_name' => 'web'],
        ['title' => ucfirst($roleName)]
    );
    echo "✓ Role criado: " . $roleName . "\n";
}

// Deletar usuários existentes
User::truncate();

echo "\nCriando usuários...\n";

$users = [
    [
        'name' => 'Admin User',
        'email' => 'admin@streamit.com',
        'password' => Hash::make('12345678'),
        'user_type' => 'admin',
        'status' => 1,
        'email_verified_at' => now(),
    ],
    [
        'name' => 'Demo Admin',
        'email' => 'demo@streamit.com',
        'password' => Hash::make('12345678'),
        'user_type' => 'demo_admin',
        'status' => 1,
        'email_verified_at' => now(),
    ]
];

foreach ($users as $userData) {
    $user = User::create($userData);
    $user->assignRole($user->user_type);
    echo "✓ Criado: " . $user->email . " (tipo: " . $user->user_type . ")\n";
}

echo "\n=== USUÁRIOS NO BANCO ===\n";
$allUsers = User::select('id', 'email', 'status', 'user_type')->get();
foreach ($allUsers as $user) {
    echo $user->email . " | status: " . $user->status . " | type: " . $user->user_type . "\n";
}

echo "\nTotal: " . User::count() . " usuários\n";
