<?php
// Adicionar ao início de public/index.php para debug
// Acessar: http://localhost:8002/debug-ranking.php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\User\Models\Ranking;

header('Content-Type: application/json; charset=utf-8');

try {
    echo "<pre style='background: #f5f5f5; padding: 20px; font-family: monospace;'>\n";
    echo "=== DEBUG RANKING VOTAÇÃO ===\n\n";
    
    // 1. Verificar se há rankings
    $allRankings = Ranking::all();
    echo "Total de rankings: " . $allRankings->count() . "\n\n";
    
    // 2. Procurar por ranking ativo com 'VOTAÇÃO'
    $ranking = Ranking::where('name', 'like', '%VOTAÇÃO%')
        ->where('status', 1)
        ->first();
    
    if (!$ranking) {
        // Tentar apenas com VOTAÇÃO
        $ranking = Ranking::where('name', 'like', '%VOTAÇÃO%')->first();
        if ($ranking) {
            echo "❌ Encontrado mas NÃO está ativo (status=" . $ranking->status . ")\n\n";
        } else {
            echo "❌ Nenhum ranking com 'VOTAÇÃO' encontrado\n";
            echo "Rankings existentes:\n";
            foreach ($allRankings as $r) {
                echo "  - {$r->name} (status={$r->status})\n";
            }
            exit;
        }
    }
    
    echo "✅ Ranking encontrado: {$ranking->name}\n";
    echo "   ID: {$ranking->id}\n";
    echo "   Status: {$ranking->status}\n";
    echo "   Start: {$ranking->start_date}\n";
    echo "   End: {$ranking->end_date}\n";
    echo "   Hoje: " . now()->toDateString() . "\n\n";
    
    // Verificar datas
    $now = now();
    $startDate = \Carbon\Carbon::parse($ranking->start_date);
    $endDate = \Carbon\Carbon::parse($ranking->end_date);
    $inRange = $now->between($startDate, $endDate);
    
    echo "Entre as datas? " . ($inRange ? "✅ SIM" : "❌ NÃO") . "\n";
    echo "   Start: {$startDate->format('Y-m-d')} <= Hoje: {$now->format('Y-m-d')} <= End: {$endDate->format('Y-m-d')}\n\n";
    
    // Verificar contents
    $contents = $ranking->contents;
    echo "Contents type: " . gettype($contents) . "\n";
    echo "Contents count: " . (is_array($contents) ? count($contents) : 'N/A') . "\n\n";
    
    if (empty($contents)) {
        echo "❌ PROBLEMA: Contents está VAZIO!\n";
    } else {
        echo "✅ Contents tem " . count($contents) . " itens\n\n";
        echo "Primeiro item:\n";
        echo json_encode($contents[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
    
    echo "\n=== API TEST ===\n";
    echo "Teste /api/v1/voting/all-candidates\n";
    
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
