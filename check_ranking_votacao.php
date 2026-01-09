<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Modules\User\Models\Ranking;

echo "=== VERIFICANDO RANKING VOTAÇÃO ===\n\n";

$ranking = Ranking::where('name', 'VOTAÇÃO DA SEMANA')->first();

if (!$ranking) {
    echo "❌ Ranking 'VOTAÇÃO DA SEMANA' não encontrado\n";
    exit(1);
}

echo "✅ Ranking encontrado:\n";
echo "ID: " . $ranking->id . "\n";
echo "Nome: " . $ranking->name . "\n";
echo "Slug: " . $ranking->slug . "\n";
echo "Status: " . $ranking->status . "\n";
echo "Start Date: " . $ranking->start_date . "\n";
echo "End Date: " . $ranking->end_date . "\n";
echo "Today: " . now()->toDateString() . "\n\n";

// Verificar se está dentro do período
$today = now();
$inRange = $today->toDateString() >= $ranking->start_date && $today->toDateString() <= $ranking->end_date;
echo "Dentro do período? " . ($inRange ? "✅ Sim" : "❌ Não") . "\n\n";

// Verificar contents
$contents = $ranking->contents;
echo "Contents: " . json_encode($contents, JSON_PRETTY_PRINT) . "\n\n";

if (empty($contents)) {
    echo "⚠️  Contents está vazio!\n";
    echo "Precisamos adicionar conteúdo ao ranking.\n";
}

echo "Total de itens: " . (is_array($contents) ? count($contents) : 0) . "\n";
