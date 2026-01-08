<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

echo "=== VERIFICAR RANKING ===\n";

// Get active ranking
$ranking = \Modules\User\Models\Ranking::where('status', 1)->first();

if (!$ranking) {
    echo "❌ Nenhum ranking ativo encontrado!\n";
    exit;
}

echo "✅ Ranking encontrado:\n";
echo "- ID: {$ranking->id}\n";
echo "- Name: {$ranking->name}\n";
echo "- Status: {$ranking->status}\n";
echo "- Start Date: {$ranking->start_date}\n";
echo "- End Date: {$ranking->end_date}\n";

// Check dates
$today = now()->toDateString();
echo "\n=== VERIFICAR DATAS ===\n";
echo "Hoje: {$today}\n";
echo "Start: {$ranking->start_date}\n";
echo "End: {$ranking->end_date}\n";

if ($ranking->start_date <= $today && $today <= $ranking->end_date) {
    echo "✅ Ranking está no período ativo!\n";
} else {
    echo "❌ Ranking FORA do período ativo!\n";
    echo "   Start ({$ranking->start_date}) <= Hoje ({$today}) <= End ({$ranking->end_date})?\n";
}

// Check contents
echo "\n=== CONTENTS (JSON) ===\n";
$contents = json_decode($ranking->contents, true);
if (!$contents || !is_array($contents)) {
    echo "❌ Contents é NULL ou inválido!\n";
    echo "Raw contents: " . $ranking->contents . "\n";
} else {
    echo "✅ Contents é um array com " . count($contents) . " itens\n";
    foreach ($contents as $i => $item) {
        echo "\n[$i] " . ($item['name'] ?? $item['title'] ?? 'SEM NOME') . "\n";
        echo "    Slug: " . ($item['slug'] ?? 'SEM SLUG') . "\n";
        echo "    Votes: " . ($item['votes'] ?? 0) . "\n";
        echo "    Image: " . ($item['image_url'] ?? 'SEM IMAGEM') . "\n";
    }
}

// Check responses
echo "\n=== RANKING RESPONSES ===\n";
$responses = \Modules\User\Models\RankingResponse::where('ranking_id', $ranking->id)->get();
echo "Total de respostas: {$responses->count()}\n";
foreach ($responses as $resp) {
    echo "- User {$resp->user_id} votou em: {$resp->content_slug}\n";
}
