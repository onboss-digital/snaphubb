<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$users = User::select('id', 'email', 'name', 'status', 'user_type')->get();

echo "=== USUARIOS NO BANCO ===\n";
foreach ($users as $user) {
    echo "ID: " . $user->id . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Status: " . $user->status . "\n";
    echo "User Type: " . $user->user_type . "\n";
    echo "---\n";
}

echo "\n=== VERIFICANDO SENHA ===\n";
$user = User::where('email', 'admin@streamit.com')->first();
if ($user) {
    echo "Usuario encontrado: " . $user->email . "\n";
    echo "Status: " . $user->status . "\n";
    echo "Senha correta? " . (Hash::check('12345678', $user->password) ? 'SIM' : 'NÃO') . "\n";
} else {
    echo "Usuario NÃO encontrado\n";
}
