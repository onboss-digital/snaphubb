<?php

// Teste de votação na API

$baseURL = 'http://127.0.0.1:8002';
$userEmail = 'assinante@test.com';

// Passo 1: Login
echo "\n=== PASSO 1: LOGIN ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseURL/login");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "email=$userEmail&password=password&_token=");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);
echo "Login feito: " . ($response ? "OK" : "ERRO") . "\n";

// Passo 2: Obter CSRF token
echo "\n=== PASSO 2: OBTER CSRF TOKEN ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseURL/voting");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$page = curl_exec($ch);
curl_close($ch);

if (preg_match('/csrf-token["\']?\s*["\']?content["\']?\s*[=:]?\s*["\']([^"\']+)/', $page, $matches)) {
    $csrf_token = $matches[1];
    echo "CSRF Token: $csrf_token\n";
} else {
    echo "Erro ao encontrar CSRF token\n";
    $csrf_token = '';
}

// Passo 3: Testar GET /voting/get-all-candidates
echo "\n=== PASSO 3: OBTER CANDIDATOS ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseURL/voting/get-all-candidates");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
echo "Resposta: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";

// Passo 4: Obter top 3
echo "\n=== PASSO 4: OBTER TOP 3 ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseURL/voting/get-top-3");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
echo "Resposta: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";

// Passo 5: Testar votação
echo "\n=== PASSO 5: TENTAR VOTAR ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseURL/voting/vote");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['content_slug' => 'modelo-1']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest',
    'X-CSRF-TOKEN: ' . $csrf_token
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
$data = json_decode($response, true);
echo "Resposta: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";

// Passo 6: Verificar candidatos novamente
echo "\n=== PASSO 6: VERIFICAR VOTOS ATUALIZADOS ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseURL/voting/get-all-candidates");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
echo "Resposta: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";

echo "\n=== FIM DO TESTE ===\n";
