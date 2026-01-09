# 📊 Análise de Compatibilidade - Checkout Externo vs Sistema Atual

## ✅ O QUE JÁ ESTÁ CORRETO

### 1. Webhook Endpoint ✓
- **Rota existe**: `/api/webhook/mercadopago` ✅
- **Controller**: `MercadoPagoWebhookController@handle` ✅
- **Arquivo**: `routes/api.php` linha 76 ✅

### 2. Idempotência ✓
```php
// Linha 95-99 do MercadoPagoWebhookController.php
$existing = SubscriptionTransactions::where('transaction_id', $paymentId)->first();
if ($existing) {
    Log::info('mercadopago.webhook: payment already processed');
    return response()->json(['status' => 'already_processed'], 200);
}
```
✅ **Checagem de transaction_id antes de criar assinatura**

### 3. Busca do Payment no Mercado Pago ✓
```php
// Linha 40-61
$resp = HttpClient::withToken($accessToken)
    ->acceptJson()
    ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");
```
✅ **Faz GET /v1/payments/{id} para validar status**

### 4. Validação de Status ✓
```php
// Linha 65-69
if (!in_array($status, ['approved', 'paid'])) {
    Log::info('mercadopago.webhook: payment not approved yet');
    return response()->json(['status' => 'ignored'], 200);
}
```
✅ **Só processa pagamentos aprovados**

### 5. Mapeamento de Plano ✓
```php
// Linha 72-89
$external = $mp['external_reference'] ?? $mp['metadata']['plan_id'] ?? null;
if (preg_match('/plan:(\d+)/', $external, $m)) {
    $planId = (int)$m[1];
    $plan = Plan::find($planId);
}
```
✅ **Mapeia external_reference para plano**

### 6. Logs Detalhados ✓
```php
Log::channel('daily')->info('mercadopago.webhook.received', $payload);
Log::info('mercadopago.webhook: provisioned subscription');
Log::error('mercadopago.webhook: provisioning error');
```
✅ **Logging completo do fluxo**

---

## ⚠️ PONTOS QUE PRECISAM MELHORAR

### 1. ❌ Provisionamento não está em transação DB
**Problema:** Se falhar no meio, pode criar subscription sem transaction ou vice-versa.

**Como está:**
```php
$subscription = Subscription::create([...]);
SubscriptionTransactions::create([...]);
$user->update(['is_subscribe' => 1]);
```

**Como deveria ser:**
```php
DB::transaction(function () use ($plan, $user, $mp, $paymentId) {
    $subscription = Subscription::create([...]);
    SubscriptionTransactions::create([...]);
    $user->update(['is_subscribe' => 1]);
});
```

### 2. ❌ Provisionamento não está enfileirado
**Problema:** Webhook síncrono pode causar timeout se demorar muito.

**Como está:**
```php
// Tudo roda diretamente no webhook
$subscription = Subscription::create([...]);
```

**Como deveria ser:**
```php
// Enfileirar job de provisionamento
dispatch(new ProvisionSubscriptionJob($paymentId, $planId, $userEmail));
return response()->json(['status' => 'accepted'], 200);
```

### 3. ❌ Lógica de provisionamento duplicada
**Problema:** Mesma lógica existe em outros webhooks (TriboPay, Stripe).

**Como deveria ser:**
- Criar `SubscriptionProvisionService` centralizado
- Todos os webhooks usam o mesmo serviço

### 4. ⚠️ Queue worker pode não estar rodando
**Problema:** E-mails ficam enfileirados mas nunca são enviados.

**Verificação necessária:**
```bash
php artisan queue:work
```

### 5. ⚠️ MERCADOPAGO_NOTIFICATION_URL não é usado na criação
**Problema:** URL do webhook está hardcoded ou não é enviada na preferência.

---

## 🔧 MELHORIAS NECESSÁRIAS

### Prioridade ALTA (fazer AGORA)

#### 1. Adicionar transação DB
#### 2. Criar serviço centralizado de provisionamento
#### 3. Enfileirar provisionamento
#### 4. Garantir queue worker rodando

### Prioridade MÉDIA

#### 5. Adicionar retry logic no webhook
#### 6. Melhorar tratamento de erros
#### 7. Adicionar webhooks de status (pending, failed)

### Prioridade BAIXA

#### 8. Adicionar testes automatizados
#### 9. Dashboard de monitoramento de webhooks

---

## 🎯 COMPATIBILIDADE COM SEU CHECKOUT

### ✅ O que funciona 100%:

| Recurso | Status |
|---------|--------|
| Endpoint `/api/webhook/mercadopago` | ✅ Funciona |
| Validação de status `approved` | ✅ Funciona |
| Busca payment via API | ✅ Funciona |
| Idempotência (transaction_id) | ✅ Funciona |
| Mapeamento `external_reference` | ✅ Funciona |
| Criação de usuário/subscription | ✅ Funciona |
| Logs detalhados | ✅ Funciona |

### ⚠️ O que precisa ajustar:

| Recurso | Status | Ação |
|---------|--------|------|
| Transação DB | ⚠️ Faltando | Adicionar `DB::transaction()` |
| Provisionamento async | ⚠️ Faltando | Criar Job |
| Serviço centralizado | ⚠️ Faltando | Criar Service |
| Queue worker | ❓ Verificar | Confirmar se está rodando |
| Separação sandbox/prod | ⚠️ Melhorar | Usar env correta |

---

## 📝 CHECKLIST PARA O DEV (VOCÊ)

### Antes de fazer compra de teste:

- [x] ✅ Endpoint webhook existe
- [x] ✅ MERCADOPAGO_ACCESS_TOKEN configurado no .env
- [x] ✅ MERCADOPAGO_NOTIFICATION_URL aponta para ngrok
- [x] ✅ Idempotência implementada
- [ ] ⚠️ Provisioning dentro de transação DB
- [ ] ⚠️ Queue worker rodando (`php artisan queue:work`)
- [x] ✅ Logs detalhados ativados
- [ ] ⚠️ Tokens sandbox vs produção separados

### Após receber primeiro webhook:

- [ ] Verificar logs: `storage/logs/laravel.log`
- [ ] Verificar subscription criada: `/app/subscriptions`
- [ ] Verificar transaction registrada: `subscriptions_transactions`
- [ ] Verificar e-mail enviado (se SMTP configurado)
- [ ] Testar login com usuário criado

---

## 🚀 AÇÃO IMEDIATA RECOMENDADA

Vou gerar para você:

1. ✅ **SubscriptionProvisionService** (centralizar lógica)
2. ✅ **ProvisionSubscriptionJob** (processar async)
3. ✅ **Melhorias no MercadoPagoWebhookController** (transação DB)
4. ✅ **Script de verificação** (queue worker, env, etc)

Confirma que quer que eu crie isso agora?
