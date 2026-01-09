<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Check CastCrew
echo "=== VERIFICAR CASTCREW ===\n";
$castcrews = \Modules\CastCrew\Models\CastCrew::limit(5)->get();
echo "Total: " . \Modules\CastCrew\Models\CastCrew::count() . "\n";

if ($castcrews->count() === 0) {
    echo "❌ SEM ATRIZES NO BANCO!\n\n";
    echo "=== CRIANDO 5 ATRIZES DE TESTE ===\n";
    
    $names = [
        ['name' => 'Ana Silva', 'bio' => 'Atriz premiada'],
        ['name' => 'Beatriz Costa', 'bio' => 'Atriz e produtora'],
        ['name' => 'Carla Santos', 'bio' => 'Atriz e modelo'],
        ['name' => 'Diana Oliveira', 'bio' => 'Atriz brasileira'],
        ['name' => 'Erica Gomes', 'bio' => 'Atriz em produção']
    ];
    
    foreach ($names as $data) {
        $cast = \Modules\CastCrew\Models\CastCrew::create([
            'name' => $data['name'],
            'bio' => $data['bio'],
            'position' => 'actress',
            'status' => 1
        ]);
        echo "✅ {$cast->name} criada (ID: {$cast->id})\n";
    }
    
    echo "\n✅ 5 Atrizes criadas com sucesso!\n";
} else {
    echo "✅ Atrizes encontradas:\n";
    foreach ($castcrews as $cast) {
        echo "- {$cast->name} (ID: {$cast->id})\n";
    }
}
