<?php

// Teste direto: simula a votação pelo backend
namespace Modules\Voting\Tests;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = \Illuminate\Http\Request::create('/');
$response = $kernel->handle($request);

// Agora temos acesso ao container
$container = $app;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

$auth = $container->make('Illuminate\Contracts\Auth\Factory');

echo "\n=== TESTE DE VOTAÇÃO ===\n";

// Passo 1: Obter usuário
$user = \Modules\User\Models\User::where('email', 'assinante@test.com')->first();
if (!$user) {
    echo "Usuário não encontrado!\n";
    exit();
}

echo "✅ Usuário encontrado: {$user->email}\n";

// Passo 2: Obter ranking ativo
$ranking = \Modules\User\Models\Ranking::where('status', 1)
    ->where('start_date', '<=', \Illuminate\Support\Carbon::now())
    ->where('end_date', '>=', \Illuminate\Support\Carbon::now())
    ->first();

if (!$ranking) {
    echo "❌ Nenhum ranking ativo!\n";
    exit();
}

echo "✅ Ranking encontrado: {$ranking->name}\n";

// Passo 3: Simular votação
$contents = json_decode($ranking->contents, true);
echo "\n=== CONTEÚDO DO RANKING (ANTES) ===\n";
print_r($contents);
echo "\n";

// Incrementar votos do modelo-1
$found = false;
foreach ($contents as &$item) {
    if ($item['slug'] === 'modelo-1') {
        $item['votes'] = ($item['votes'] ?? 0) + 1;
        echo "\n✅ Votação registrada para: {$item['name']}\n";
        $found = true;
        break;
    }
}

if (!$found) {
    echo "❌ Modelo-1 não encontrado!\n";
    exit();
}

// Salvar no banco
$ranking->save();

// Registrar na tabela ranking_responses
\Modules\User\Models\RankingResponse::updateOrCreate(
    ['user_id' => $user->id, 'ranking_id' => $ranking->id],
    ['content_slug' => 'modelo-1', 'response_date' => \Illuminate\Support\Carbon::now()->toDateString()]
);

echo "✅ Voto salvo no banco de dados!\n";

// Passo 4: Verificar resultado
$ranking = \Modules\User\Models\Ranking::find($ranking->id);
$contents = json_decode($ranking->contents, true);

echo "\n=== CONTEÚDO DO RANKING (DEPOIS) ===\n";
echo json_encode($contents, JSON_PRETTY_PRINT) . "\n";

// Verificar ranking_responses
$response = \Modules\User\Models\RankingResponse::where('user_id', $user->id)->where('ranking_id', $ranking->id)->first();
echo "\n=== RANKING_RESPONSE ===\n";
echo "User ID: {$response->user_id}\n";
echo "Ranking ID: {$response->ranking_id}\n";
echo "Content Slug: {$response->content_slug}\n";
echo "Response Date: {$response->response_date}\n";

echo "\n=== FIM DO TESTE ===\n";
