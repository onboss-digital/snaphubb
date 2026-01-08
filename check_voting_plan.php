<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Check plans
$plans = \Modules\Subscriptions\Models\Plan::where('slug', 'like', '%voting%')
    ->orWhere('slug', 'like', '%community%')
    ->orWhere('name', 'like', '%voting%')
    ->orWhere('name', 'like', '%community%')
    ->get();

echo "=== PLANOS COM VOTING ===\n";
if ($plans->count() === 0) {
    echo "❌ NENHUM PLANO ENCONTRADO!\n";
} else {
    foreach ($plans as $plan) {
        echo "- ID: {$plan->id}, Name: {$plan->name}, Slug: {$plan->slug}\n";
    }
}

// Check user assinante@test.com
echo "\n=== USUARIO assinante@test.com ===\n";
$user = \App\Models\User::where('email', 'assinante@test.com')->first();
if (!$user) {
    echo "❌ User não encontrado!\n";
} else {
    echo "✅ User encontrado: ID {$user->id}, Name: {$user->name}\n";
    
    // Check subscriptions
    echo "\n=== SUBSCRICOES DO USER ===\n";
    $subs = \Modules\Subscriptions\Models\Subscription::where('user_id', $user->id)->get();
    if ($subs->count() === 0) {
        echo "❌ SEM SUBSCRICOES!\n";
    } else {
        foreach ($subs as $sub) {
            $plan = $sub->plan;
            echo "- Status: {$sub->status}, Plan: {$plan->name} (ID: {$plan->id}), Slug: {$plan->slug}\n";
        }
    }
    
    // Check voting access
    echo "\n=== VERIFICAR ACESSO VOTING ===\n";
    $hasAccess = \Modules\Subscriptions\Models\Subscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->whereHas('plan', function ($query) {
            $query->where('slug', 'voting')
                ->orWhere('slug', 'community-voting')
                ->orWhere('name', 'like', '%Voting%')
                ->orWhere('name', 'like', '%Community%');
        })
        ->exists();
    
    echo $hasAccess ? "✅ TEM ACESSO\n" : "❌ SEM ACESSO\n";
}
