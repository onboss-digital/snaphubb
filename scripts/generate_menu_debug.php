<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Middleware\GenerateMenus;
use App\Models\User;

// login as admin
$user = User::where('email', 'admin@snaphubb.com')->first();
auth()->login($user);

$gm = new GenerateMenus();
$gm->handle();

$menu = \Menu::get('menu');
foreach ($menu->roots() as $item) {
    echo $item->title . "\n";
}
