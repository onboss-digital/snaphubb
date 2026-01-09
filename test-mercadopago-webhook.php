<?php
/**
 * Script para testar webhook do Mercado Pago localmente
 * 
 * Como usar:
 * 1. Certifique-se de que o servidor Laravel está rodando (php artisan serve --port=8002)
 * 2. Execute: php test-mercadopago-webhook.php
 */

// URL do webhook (ajuste conforme necessário)
$webhookUrl = 'http://127.0.0.1:8002/api/webhook/mercadopago';

// ID do plano que você quer testar (pegue de: /app/plans)
$planId = 1; // ALTERE para o ID do plano que existe no banco

// Dados simulados de um pagamento aprovado do Mercado Pago
$webhookPayload = [
    'action' => 'payment.created',
    'api_version' => 'v1',
    'data' => [
        'id' => 'test_payment_' . time() // ID único para cada teste
    ],
    'date_created' => date('c'),
    'id' => rand(10000000, 99999999),
    'live_mode' => false,
    'type' => 'payment',
    'user_id' => '123456',
    
    // Incluindo objeto payment completo para teste local
    'payment' => [
        'id' => 'test_payment_' . time(),
        'status' => 'approved',
        'status_detail' => 'accredited',
        'payment_type_id' => 'credit_card',
        'payment_method_id' => 'visa',
        'transaction_amount' => 99.90,
        'transaction_details' => [
            'total_paid_amount' => 99.90,
        ],
        'external_reference' => "plan:{$planId}",
        'metadata' => [
            'plan_id' => $planId,
        ],
        'payer' => [
            'email' => 'teste@exemplo.com.br',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'name' => 'João Silva',
        ],
        'date_approved' => date('c'),
        'date_created' => date('c'),
        'date_last_updated' => date('c'),
    ]
];

echo "🚀 Enviando webhook de teste para: {$webhookUrl}\n";
echo "📦 Plan ID: {$planId}\n";
echo "💳 Payment ID: test_payment_" . time() . "\n";
echo "📧 Email: teste@exemplo.com.br\n";
echo "\n";

// Enviar requisição
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📊 Resposta HTTP: {$httpCode}\n";
echo "📄 Resposta: {$response}\n\n";

if ($httpCode == 200) {
    echo "✅ Webhook processado com sucesso!\n\n";
    echo "📝 Próximos passos:\n";
    echo "1. Verifique os logs: storage/logs/laravel.log\n";
    echo "2. Verifique o e-mail enviado nos logs\n";
    echo "3. Acesse: http://127.0.0.1:8002/app/subscriptions para ver a assinatura criada\n";
    echo "4. Faça login com: teste@exemplo.com.br / P@55w0rd\n";
} else {
    echo "❌ Erro ao processar webhook!\n";
    echo "Verifique se o servidor Laravel está rodando e se o plano existe.\n";
}
