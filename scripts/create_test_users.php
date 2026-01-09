<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\User;
use Spatie\Permission\Models\Role;

$roles = ['admin','demo_admin','user'];
foreach($roles as $r) {
    Role::firstOrCreate(
        ['name' => $r, 'guard_name' => 'web'],
        ['title' => ucfirst(str_replace('_',' ',$r))]
    );
}

$users = [
    ['name'=>'Admin Principal','email'=>'admin@snaphubb.com','password'=>'Meta10k@@','role'=>'admin'],
    ['name'=>'Demo Admin','email'=>'demo@snaphubb.bom','password'=>'12345678','role'=>'demo_admin'],
    ['name'=>'Usuário Regular','email'=>'assinante@test.com','password'=>'123456','role'=>'user'],
];

foreach ($users as $u) {
    $user = User::where('email', $u['email'])->first();
    if (! $user) {
        $user = User::create([
            'name' => $u['name'],
            'email' => $u['email'],
            'password' => bcrypt($u['password']),
            'email_verified_at' => now(),
        ]);
        echo "Created: {$u['email']}\n";
    } else {
        echo "Exists: {$u['email']}\n";
    }
    $user->assignRole($u['role']);
    echo "Assigned role {$u['role']} to {$u['email']}\n";
}

echo "Done\n";
