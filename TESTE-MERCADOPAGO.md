# 🧪 Guia de Teste - Pagamento Mercado Pago

## ✅ Pré-requisitos

1. ✔️ `.env` configurado com:
   ```env
   MERCADOPAGO_ACCESS_TOKEN=TEST-1381168815917986-111904-04c563a72d48d231540a4991b4e0f82f-1819882050
   MERCADOPAGO_PUBLIC_KEY=TEST-701a917c-8d55-4b41-ba50-d20201eea588
   MERCADOPAGO_NOTIFICATION_URL=https://hugo-delitescent-countercurrently.ngrok-free.dev/api/webhook/mercadopago
   MAIL_MAILER=log
   ```

2. ✔️ Servidor Laravel rodando:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8002
   ```

3. ✔️ Pelo menos 1 plano criado no sistema (admin)

---

## 🎯 Opção 1: Teste Manual (Fluxo Completo)

### 1️⃣ Acessar página de planos
```
http://127.0.0.1:8002/subscription-plan
```

### 2️⃣ Fazer login ou cadastro
- Crie uma conta de teste ou use existente

### 3️⃣ Selecionar um plano
- Clique em "Assinar" ou "Escolher plano"
- Na página de pagamento, selecione **Mercado Pago**
- Clique em "Pagar"

### 4️⃣ Completar pagamento no Mercado Pago
- Você será redirecionado para a página de checkout do Mercado Pago
- Use cartões de teste:
  - **Aprovado**: `5031 4332 1540 6351` (CVV: 123, Validade: 11/25)
  - **Recusado**: `5031 7557 3453 0604`

### 5️⃣ Verificar e-mail e assinatura
- Veja o log: `storage/logs/laravel.log`
- Acesse admin: `http://127.0.0.1:8002/app/subscriptions`

---

## 🚀 Opção 2: Teste via Script (Webhook Direto)

Simula um pagamento aprovado direto no webhook, sem passar pelo Mercado Pago:

### 1️⃣ Edite o arquivo `test-mercadopago-webhook.php`
```php
$planId = 1; // Altere para ID de um plano existente
```

### 2️⃣ Execute o script
```bash
php test-mercadopago-webhook.php
```

### 3️⃣ Verifique os resultados
```bash
# Ver logs
tail -f storage/logs/laravel.log

# Ou abrir o arquivo diretamente
code storage/logs/laravel.log
```

### 4️⃣ Login com usuário criado
- Email: `teste@exemplo.com.br`
- Senha: `P@55w0rd`
- URL: `http://127.0.0.1:8002/login`

---

## 📧 Verificar E-mail Enviado

Como está configurado `MAIL_MAILER=log`, os e-mails ficam salvos em:
```
storage/logs/laravel.log
```

Procure por:
- `Mail: SubscriptionDetail`
- Conteúdo do e-mail com dados da assinatura

---

## 🌐 Teste com Webhook Real (Opcional)

Para receber webhooks reais do Mercado Pago:

### 1️⃣ Instale e inicie o ngrok
```bash
ngrok http 8002
```

### 2️⃣ Atualize o `.env`
```env
MERCADOPAGO_NOTIFICATION_URL=https://SEU-DOMINIO.ngrok-free.app/api/webhook/mercadopago
```

### 3️⃣ Limpe cache
```bash
php artisan config:clear
```

### 4️⃣ Faça compra real
Acesse `http://127.0.0.1:8002/subscription-plan` e complete o pagamento.

---

## 🔍 Troubleshooting

### ❌ Erro: "Plan not found"
- Verifique se existe pelo menos 1 plano: `/app/plans`
- Ajuste `$planId` no script de teste

### ❌ Webhook não recebe dados
- Verifique se o servidor está rodando: `php artisan serve --port=8002`
- Teste a URL: `curl -X POST http://127.0.0.1:8002/api/webhook/mercadopago -H "Content-Type: application/json" -d "{}"`

### ❌ E-mail não aparece
- Confirme: `MAIL_MAILER=log` no `.env`
- Verifique: `storage/logs/laravel.log`

### ❌ Erro 500
- Veja logs: `storage/logs/laravel.log`
- Execute: `php artisan config:clear`

---

## 📊 Endpoints Importantes

| Rota | Método | Descrição |
|------|--------|-----------|
| `/subscription-plan` | GET | Listar planos disponíveis |
| `/select-plan` | POST | Selecionar plano |
| `/process-payment` | POST | Processar pagamento |
| `/api/webhook/mercadopago` | POST | Webhook do Mercado Pago |
| `/app/subscriptions` | GET | Admin: ver assinaturas |
| `/app/plans` | GET | Admin: gerenciar planos |

---

## 💡 Dicas

1. **Sempre limpe o cache após alterar `.env`:**
   ```bash
   php artisan config:clear
   ```

2. **Monitore logs em tempo real:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Veja dados completos do webhook:**
   ```bash
   ls -la storage/logs/mercadopago/
   ```

4. **Cartões de teste Mercado Pago:** [Documentação Oficial](https://www.mercadopago.com.br/developers/pt/docs/checkout-api/testing)

---

✅ **Está tudo configurado! Escolha uma opção de teste e comece.**
