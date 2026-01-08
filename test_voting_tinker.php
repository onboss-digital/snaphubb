<?php
// Este teste testa via navegador usando cookies de sessão
// Execute em um navegador: http://127.0.0.1:8002/voting/test-api

namespace Modules\Voting\Tests;

// Simulação do teste via navegador simulado
echo "Teste da API de votação\n\n";

// Vamos fazer teste direto pelo artisan
shell_exec('cd "e:/ONBOSS DIGITAL/SNAPHUBB/snaphubb" && php artisan tinker << \'EOF\'

$user = \Modules\User\Models\User::where("email", "assinante@test.com")->first();
if ($user) {
    echo "Usuário encontrado: {$user->email}\n";
    
    $ranking = \Modules\User\Models\Ranking::find(1);
    if ($ranking) {
        echo "Ranking encontrado: {$ranking->name}\n";
        echo "Status: {$ranking->status}\n";
        echo "Contents:\n";
        $contents = json_decode($ranking->contents, true);
        echo json_encode($contents, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Ranking não encontrado\n";
    }
} else {
    echo "Usuário não encontrado\n";
}

EOF
');
