# 🚀 Guia Completo - Teste com Checkout Externo

## 📌 Visão Geral

Você tem:
- **Checkout externo**: `https://pay.snaphubb.com/` (processa pagamentos)
- **Projeto local**: `http://127.0.0.1:8002` na branch `boss`
- **Objetivo**: Testar compra real pelo checkout e receber webhook no local

---

## 🔧 Passo 1: Expor localhost com ngrok

### 1.1 Instalar ngrok (se não tiver)
- Download: https://ngrok.com/download
- Ou via Chocolatey (Windows):
  ```bash
  choco install ngrok
  ```

### 1.2 Iniciar servidor Laravel
```bash
php artisan serve --host=127.0.0.1 --port=8002
```

### 1.3 Expor com ngrok (em outro terminal)
```bash
ngrok http 8002
```

### 1.4 Copiar URL pública
Você verá algo como:
```
Forwarding: https://a1b2-c3d4.ngrok-free.app -> http://127.0.0.1:8002
```

**Copie essa URL**: `https://a1b2-c3d4.ngrok-free.app`

---

## 🔗 Passo 2: Configurar webhook no checkout externo

### 2.1 URLs de webhook que você deve configurar no `pay.snaphubb.com`:

**Para Mercado Pago:**
```
https://SEU-NGROK.ngrok-free.app/api/webhook/mercadopago
```

**Para Stripe:**
```
https://SEU-NGROK.ngrok-free.app/api/webhook/stripe
```

**Para TriboPay (se aplicável):**
```
https://SEU-NGROK.ngrok-free.app/api/webhook/tribopay
```

### 2.2 Onde configurar?
No painel do seu checkout externo (`pay.snaphubb.com`), procure por:
- "Webhook URL"
- "Notification URL"
- "IPN URL"
- "Callback URL"

E cole a URL do ngrok correspondente.

---

## ✅ Passo 3: Preparar o plano no sistema local

### 3.1 Acessar admin local
```
http://127.0.0.1:8002/login
```

### 3.2 Criar/Editar plano
```
http://127.0.0.1:8002/app/plans
```

### 3.3 Configurar gateway no plano
- **Para Mercado Pago**:
  - Campo `custom_gateway`: deixe vazio ou `mercadopago`
  - Campo `external_product_id`: código/ID do produto no Mercado Pago
  
- **Para Stripe**:
  - Campo `custom_gateway`: `stripe`
  - Campo `external_product_id`: ID do produto/preço no Stripe

- **Para TriboPay**:
  - Campo `custom_gateway`: `TriboPay`
  - Campo `external_product_id`: hash do produto no TriboPay

### 3.4 Anotar o ID do plano
Exemplo: `Plan ID: 1`

---

## 🛒 Passo 4: Fazer compra de teste

### 4.1 Acessar checkout externo
```
https://pay.snaphubb.com/
```

### 4.2 Selecionar produto/plano
- Escolha o plano que corresponde ao ID configurado no sistema local

### 4.3 Preencher dados de pagamento
**Para Mercado Pago (Sandbox):**
- Cartão aprovado: `5031 4332 1540 6351`
- CVV: `123`
- Validade: `11/25`
- Nome: Qualquer nome
- Email: `teste@mercadopago.com`

**Para Stripe (Test Mode):**
- Cartão aprovado: `4242 4242 4242 4242`
- CVV: `123`
- Validade: qualquer futura
- Email: `teste@stripe.com`

### 4.4 Confirmar pagamento
- Complete o processo de pagamento
- Aguarde redirecionamento

---

## 📡 Passo 5: Monitorar webhook recebido

### 5.1 Abrir logs em tempo real (novo terminal)
```bash
tail -f storage/logs/laravel.log
```

### 5.2 O que você verá:
Quando o webhook chegar, verá logs como:
```
[2025-11-19] mercadopago.webhook.received
[2025-11-19] mercadopago.webhook: provisioned subscription
[2025-11-19] Mail: SubscriptionDetail
```

### 5.3 Verificar webhook bruto (opcional)
**Mercado Pago:**
```bash
ls -la storage/logs/mercadopago/
cat storage/logs/mercadopago/mercadopago_*.log
```

**TriboPay:**
```bash
ls -la storage/logs/tribopay/
cat storage/logs/tribopay/tribopay_*.log
```

---

## ✉️ Passo 6: Verificar e-mail enviado

### 6.1 Ver e-mail no log
```bash
# Ver últimas 100 linhas
tail -100 storage/logs/laravel.log

# Ou buscar por e-mail
grep -A 50 "SubscriptionDetail" storage/logs/laravel.log
```

### 6.2 O que procurar:
```
Mail: SubscriptionDetail
To: teste@mercadopago.com
Subject: Detalhes da Assinatura
```

O conteúdo completo do e-mail estará no log.

---

## 🔍 Passo 7: Verificar assinatura criada

### 7.1 Ver no admin
```
http://127.0.0.1:8002/app/subscriptions
```

### 7.2 Fazer login com usuário criado
- URL: `http://127.0.0.1:8002/login`
- Email: o e-mail usado na compra
- Senha: `P@55w0rd` (senha padrão criada automaticamente)

### 7.3 Verificar no banco (opcional)
```bash
php artisan tinker
```
```php
// Ver última assinatura criada
\Modules\Subscriptions\Models\Subscription::latest()->first();

// Ver transação
\Modules\Subscriptions\Models\SubscriptionTransactions::latest()->first();

// Ver usuário criado
\App\Models\User::where('email', 'teste@mercadopago.com')->first();
```

---

## 🐛 Troubleshooting

### ❌ Webhook não chegou
**Verificar:**
1. ngrok está rodando?
   ```bash
   # Ver status
   curl http://127.0.0.1:4040/api/tunnels
   ```

2. URL configurada correta no checkout?
   - Deve ser: `https://SEU-NGROK.ngrok-free.app/api/webhook/...`

3. Testar manualmente:
   ```bash
   curl -X POST https://SEU-NGROK.ngrok-free.app/api/webhook/mercadopago \
     -H "Content-Type: application/json" \
     -d '{"test": "ok"}'
   ```

### ❌ Erro 500 no webhook
```bash
# Ver erro exato
tail -50 storage/logs/laravel.log
```

Possíveis causas:
- Plano não existe no banco
- `external_reference` não bate com o plano
- Dados inválidos no webhook

### ❌ Assinatura não foi criada
Verificar logs:
```bash
grep "error\|Error\|ERROR" storage/logs/laravel.log | tail -20
```

### ❌ E-mail não aparece
Confirmar configuração:
```bash
php artisan tinker
```
```php
config('mail.mailer'); // deve ser 'log'
```

---

## 📊 Fluxo Completo (Resumo)

```
┌─────────────────────────┐
│  Cliente no Checkout    │
│  pay.snaphubb.com       │
└───────────┬─────────────┘
            │
            │ 1. Preenche dados
            │ 2. Paga com cartão
            ▼
┌─────────────────────────┐
│  Gateway (MP/Stripe)    │
│  Processa pagamento     │
└───────────┬─────────────┘
            │
            │ 3. Pagamento aprovado
            │ 4. Envia webhook
            ▼
┌─────────────────────────┐
│  ngrok (túnel público)  │
│  https://xxx.ngrok.app  │
└───────────┬─────────────┘
            │
            │ 5. Redireciona para localhost
            ▼
┌─────────────────────────┐
│  Seu Laravel Local      │
│  127.0.0.1:8002         │
│  branch: boss           │
└───────────┬─────────────┘
            │
            │ 6. Processa webhook
            │ 7. Cria usuário
            │ 8. Cria assinatura
            │ 9. Envia e-mail (log)
            ▼
┌─────────────────────────┐
│  storage/logs/          │
│  - laravel.log          │
│  - mercadopago/*.log    │
└─────────────────────────┘
```

---

## 🎯 Comandos Rápidos

```bash
# Terminal 1: Laravel
php artisan serve --port=8002

# Terminal 2: ngrok
ngrok http 8002

# Terminal 3: Monitorar logs
tail -f storage/logs/laravel.log

# Terminal 4: Comandos úteis
php artisan tinker
php artisan config:clear
```

---

## ✅ Checklist Final

Antes de testar, confirme:

- [ ] Servidor Laravel rodando (`php artisan serve`)
- [ ] ngrok rodando e URL copiada
- [ ] Webhook URL configurada no checkout externo
- [ ] Plano existe no banco local (`/app/plans`)
- [ ] `external_product_id` configurado no plano
- [ ] Logs sendo monitorados (`tail -f`)
- [ ] `.env` com `MAIL_MAILER=log`

**Agora pode fazer a compra!** 🚀

---

## 💡 Dica Final

Se quiser testar **SEM fazer compra real**, use o script:
```bash
php test-mercadopago-webhook.php
```

Ele simula exatamente o que o webhook faria, sem precisar do checkout externo.
