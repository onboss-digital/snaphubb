<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Check plan table columns
echo "=== ESTRUTURA DA TABELA 'plan' ===\n";
$columns = \Illuminate\Support\Facades\DB::connection()->getSchemaBuilder()->getColumns('plan');
foreach ($columns as $col) {
    echo "- {$col['name']} ({$col['type']})\n";
}

// Check some plans
echo "\n=== PRIMEIROS 5 PLANOS ===\n";
$plans = \Modules\Subscriptions\Models\Plan::limit(5)->get();
foreach ($plans as $plan) {
    echo "ID: {$plan->id}, Name: {$plan->name}, Attributes: " . json_encode($plan->toArray(), JSON_PRETTY_PRINT) . "\n";
}
