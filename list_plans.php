<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Check all plans
echo "=== TODOS OS PLANOS ===\n";
$plans = \Modules\Subscriptions\Models\Plan::get();
foreach ($plans as $plan) {
    echo "- {$plan->name} (identifier: {$plan->identifier})\n";
}

// Check user
echo "\n=== USUARIO assinante@test.com ===\n";
$user = \App\Models\User::where('email', 'assinante@test.com')->first();
if ($user) {
    echo "✅ User ID: {$user->id}\n";
    
    $subs = \Modules\Subscriptions\Models\Subscription::where('user_id', $user->id)->get();
    echo "\nSubscriptions:\n";
    foreach ($subs as $sub) {
        echo "- Status: {$sub->status}, Plan: {$sub->plan->name} (ID: {$sub->plan_id})\n";
    }
} else {
    echo "❌ User não encontrado\n";
}
