<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Create voting plan
echo "=== CRIANDO PLANO DE VOTAÇÃO ===\n";

$votingPlan = \Modules\Subscriptions\Models\Plan::create([
    'name' => 'Community Voting',
    'identifier' => 'community-voting',
    'price' => 9.90,
    'currency' => 'BRL',
    'language' => 'br',
    'duration' => 'monthly',
    'duration_value' => 1,
    'level' => 2,
    'status' => 1,
    'custom_gateway' => 'Stripe',
    'description' => '<p>Acesso à Votação da Comunidade! Vote nas atrizes favoritas e veja quem é a mais votada da semana.</p>',
    'created_by' => 1,
    'updated_by' => 1
]);

echo "✅ Plano criado!\n";
echo "- ID: {$votingPlan->id}\n";
echo "- Name: {$votingPlan->name}\n";
echo "- Identifier: {$votingPlan->identifier}\n";
echo "- Price: {$votingPlan->price}\n";

// Now subscribe assinante@test.com to this plan
echo "\n=== ADICIONANDO SUBSCRIPTION ===\n";
$user = \App\Models\User::where('email', 'assinante@test.com')->first();

if ($user) {
    // Remove old subscriptions
    \Modules\Subscriptions\Models\Subscription::where('user_id', $user->id)->forceDelete();
    
    // Create new subscription
    $sub = \Modules\Subscriptions\Models\Subscription::create([
        'user_id' => $user->id,
        'plan_id' => $votingPlan->id,
        'status' => 'active',
        'amount' => $votingPlan->price,
        'name' => $votingPlan->name,
        'identifier' => $votingPlan->identifier,
        'type' => 'monthly'
    ]);
    
    echo "✅ Subscription criada!\n";
    echo "- User: {$user->email}\n";
    echo "- Plan: {$votingPlan->name}\n";
    echo "- Status: active\n";
    
    // Verify access
    $hasAccess = \Modules\Subscriptions\Models\Subscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->whereHas('plan', function ($query) {
            $query->where('identifier', 'community-voting')
                ->orWhere('identifier', 'voting')
                ->orWhere('identifier', 'voting-community')
                ->orWhere('name', 'like', '%Voting%')
                ->orWhere('name', 'like', '%Community%');
        })
        ->exists();
    
    echo "\n=== VERIFICAR ACESSO ===\n";
    echo $hasAccess ? "✅ TEM ACESSO À VOTAÇÃO!\n" : "❌ SEM ACESSO\n";
} else {
    echo "❌ User assinante@test.com não encontrado\n";
}
