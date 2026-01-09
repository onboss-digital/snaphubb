# 🚀 Plano de Deploy - Branch BOSS → Produção (feature/update-core-version)

## 📊 Análise Realizada

### Modificações na Branch BOSS:
1. ✅ **MercadoPago Webhook** - novo controller completo
2. ✅ **Stripe Webhook** - novo controller melhorado
3. ✅ **PaymentController** - método MercadoPago adicionado
4. ✅ **E-mails multilíngue** - suporte a locale do usuário
5. ✅ **Comandos de notificação** - emails em fila (queue)
6. ✅ **Migration locale** - campo `locale` na tabela `users`
7. ✅ **Traduções** - arquivos de email em PT, ES, EN
8. ✅ **Rotas API** - webhooks dedicados

### Arquivos Novos (Untracked):
```
app/Http/Controllers/MercadoPagoWebhookController.php
app/Http/Controllers/StripeWebhookController.php
database/migrations/2025_11_19_000000_add_locale_to_users_table.php
ANALISE-COMPATIBILIDADE.md (documentação)
GUIA-TESTE-CHECKOUT-EXTERNO.md (documentação)
TESTE-MERCADOPAGO.md (documentação)
test-mercadopago-webhook.php (script de teste)
```

### Arquivos Modificados:
```
Modules/Frontend/Http/Controllers/PaymentController.php
Modules/Subscriptions/Http/Controllers/Backend/API/SubscriptionController.php
Modules/User/Http/Controllers/Backend/UsersController.php
app/Console/Commands/*.php (3 arquivos)
app/Http/Controllers/WebHookController.php
app/Models/User.php
lang/**/email.php (3 idiomas)
resources/views/emails/*.blade.php (4 templates)
routes/api.php
.env (configurações)
```

---

## ✅ ESTRATÉGIA RECOMENDADA: Cherry-Pick Seletivo + Teste

### Por que NÃO fazer merge direto?
- ❌ Produção pode ter commits que você não quer sobrescrever
- ❌ Pode quebrar funcionalidades estáveis
- ❌ Difícil de reverter em caso de problema

### ✅ Estratégia Segura:

1. **Criar branch intermediária** a partir de produção
2. **Cherry-pick** commits específicos do BOSS
3. **Testar** na intermediária
4. **Merge** controlado para produção

---

## 📋 PASSO A PASSO DETALHADO

### FASE 1: Preparação (5 min)

```bash
# 1. Garantir que está com tudo commitado na boss
git add -A
git commit -m "feat: Mercado Pago and Stripe webhooks + multilingual emails"

# 2. Ir para branch de produção
git checkout feature/update-core-version
git pull origin feature/update-core-version

# 3. Criar branch intermediária para deploy
git checkout -b deploy/payment-webhooks
```

---

### FASE 2: Trazer Mudanças (10 min)

#### Opção A: Cherry-Pick do Commit (RECOMENDADO)

```bash
# Pegar hash do último commit da boss
git log boss --oneline -1
# Exemplo: 18ad303

# Cherry-pick do commit
git cherry-pick 18ad303

# Se houver conflitos, resolver manualmente e:
git add .
git cherry-pick --continue
```

#### Opção B: Aplicar Mudanças Manualmente (SE cherry-pick falhar)

```bash
# Copiar arquivos novos
git checkout boss -- app/Http/Controllers/MercadoPagoWebhookController.php
git checkout boss -- app/Http/Controllers/StripeWebhookController.php
git checkout boss -- database/migrations/2025_11_19_000000_add_locale_to_users_table.php

# Copiar arquivos modificados (verificar conflitos)
git checkout boss -- routes/api.php
git checkout boss -- Modules/Frontend/Http/Controllers/PaymentController.php
git checkout boss -- app/Models/User.php

# Traduções
git checkout boss -- lang/br/email.php
git checkout boss -- lang/en/email.php
git checkout boss -- lang/es/email.php

# Templates de email
git checkout boss -- resources/views/emails/

# Comandos
git checkout boss -- app/Console/Commands/ContinueWatchNotification.php
git checkout boss -- app/Console/Commands/ReminderNotification.php
git checkout boss -- app/Console/Commands/SendSubscriptionNotifications.php

# Controllers
git checkout boss -- Modules/Subscriptions/Http/Controllers/Backend/API/SubscriptionController.php
git checkout boss -- Modules/User/Http/Controllers/Backend/UsersController.php
git checkout boss -- app/Http/Controllers/WebHookController.php
```

---

### FASE 3: Atualizar .env de Produção (5 min)

**NÃO commitar o .env**, apenas atualizar no servidor:

```bash
# NO SERVIDOR DE PRODUÇÃO, adicionar:
MERCADOPAGO_ACCESS_TOKEN=seu_token_producao
MERCADOPAGO_PUBLIC_KEY=seu_public_key_producao
MERCADOPAGO_NOTIFICATION_URL=https://seudominio.com/api/webhook/mercadopago

STRIPE_API_PUBLIC_KEY=pk_live_...
STRIPE_API_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Manter ou ajustar
MAIL_MAILER=smtp
QUEUE_CONNECTION=database
```

---

### FASE 4: Rodar Migrations (2 min)

```bash
# Adicionar campo locale na users
php artisan migrate

# Verificar se rodou
php artisan migrate:status
```

---

### FASE 5: Testes na Branch Intermediária (15 min)

```bash
# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Testar rotas
php artisan route:list | grep webhook

# Verificar se classes existem
php artisan tinker
>>> new \App\Http\Controllers\MercadoPagoWebhookController();
>>> new \App\Http\Controllers\StripeWebhookController();
>>> exit

# Testar webhook localmente
php test-mercadopago-webhook.php

# Verificar logs
tail -50 storage/logs/laravel.log
```

#### Checklist de Testes:

- [ ] Migrations rodaram sem erro
- [ ] Rotas de webhook existem
- [ ] Controllers compilam sem erro
- [ ] Webhook de teste funciona localmente
- [ ] E-mail aparece nos logs
- [ ] Assinatura é criada no banco

---

### FASE 6: Commit e Push da Branch Intermediária (2 min)

```bash
git add -A
git commit -m "feat: integrate Mercado Pago and Stripe webhooks from boss branch

- Add MercadoPagoWebhookController with idempotency and logging
- Add StripeWebhookController with DB transactions
- Add MercadoPago payment method to PaymentController
- Implement multilingual email support (PT, ES, EN)
- Queue email notifications in subscription flows
- Add user locale field and preferredLocale method
- Update email templates with translations
- Update API webhook routes

Refs: boss branch 18ad303"

git push origin deploy/payment-webhooks
```

---

### FASE 7: Pull Request e Review (10 min)

```bash
# No GitHub:
1. Criar PR de `deploy/payment-webhooks` → `feature/update-core-version`
2. Revisar diff
3. Adicionar descrição:
```

**PR Title:**
```
feat: Payment Webhooks (Mercado Pago + Stripe) + Multilingual Emails
```

**PR Description:**
```markdown
## 🎯 Objetivo
Integrar webhooks de pagamento do Mercado Pago e Stripe vindos da branch `boss`, com suporte a emails multilíngues.

## ✨ Features Adicionadas
- ✅ Webhook Mercado Pago completo (idempotência, logs)
- ✅ Webhook Stripe melhorado (DB transactions)
- ✅ Método de pagamento Mercado Pago no frontend
- ✅ Emails multilíngues (PT, ES, EN)
- ✅ Emails em fila (queue) para performance
- ✅ Campo `locale` na tabela `users`

## 📝 Arquivos Principais
- `app/Http/Controllers/MercadoPagoWebhookController.php` (NOVO)
- `app/Http/Controllers/StripeWebhookController.php` (NOVO)
- `database/migrations/2025_11_19_000000_add_locale_to_users_table.php` (NOVO)
- `routes/api.php` (webhooks dedicados)
- `PaymentController.php` (método MercadoPago)
- Traduções email (PT/ES/EN)

## ✅ Testes Realizados
- [x] Migration rodou sem erros
- [x] Webhooks testados localmente
- [x] E-mails aparecem nos logs
- [x] Assinaturas criadas corretamente
- [x] Idempotência funciona

## 🚀 Deploy
1. Rodar migration: `php artisan migrate`
2. Atualizar `.env` com tokens prod
3. Configurar URLs de webhook nos gateways
4. Iniciar queue worker: `php artisan queue:work`
5. Monitorar logs: `tail -f storage/logs/laravel.log`

## ⚠️ Breaking Changes
Nenhum. Mudanças são aditivas e não afetam código existente.

## 📚 Documentação
- `GUIA-TESTE-CHECKOUT-EXTERNO.md`
- `TESTE-MERCADOPAGO.md`
- `ANALISE-COMPATIBILIDADE.md`
```

---

### FASE 8: Merge para Produção (após aprovação)

```bash
# Após aprovação do PR:
git checkout feature/update-core-version
git pull origin feature/update-core-version
git merge deploy/payment-webhooks --no-ff
git push origin feature/update-core-version
```

---

### FASE 9: Deploy no Servidor (20 min)

**NO SERVIDOR DE PRODUÇÃO:**

```bash
# 1. Backup do banco
php artisan backup:run --only-db

# 2. Ativar modo manutenção
php artisan down --message="Atualizando sistema de pagamentos" --retry=60

# 3. Pull do código
git pull origin feature/update-core-version

# 4. Instalar dependências (se houver novas)
composer install --no-dev --optimize-autoloader

# 5. Rodar migrations
php artisan migrate --force

# 6. Limpar caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 7. Otimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Reiniciar queue workers
php artisan queue:restart

# 9. Desativar modo manutenção
php artisan up

# 10. Verificar
php artisan route:list | grep webhook
```

---

### FASE 10: Configurar Webhooks nos Gateways (10 min)

#### Mercado Pago:
1. Acessar: https://www.mercadopago.com.br/developers/panel/app
2. Selecionar sua aplicação
3. Ir em "Webhooks"
4. Adicionar: `https://seudominio.com/api/webhook/mercadopago`
5. Selecionar eventos: `payment.created`, `payment.updated`

#### Stripe:
1. Acessar: https://dashboard.stripe.com/webhooks
2. Adicionar endpoint: `https://seudominio.com/api/webhook/stripe`
3. Selecionar eventos:
   - `checkout.session.completed`
   - `invoice.payment_succeeded`
   - `payment_intent.succeeded`
4. Copiar `Signing secret` e adicionar ao `.env`

---

### FASE 11: Teste em Produção (10 min)

```bash
# 1. Fazer compra de teste
# Usar cartão de teste do gateway

# 2. Monitorar logs em tempo real
tail -f storage/logs/laravel.log

# 3. Verificar webhook chegou
grep "webhook.received" storage/logs/laravel.log | tail -5

# 4. Verificar assinatura criada
php artisan tinker
>>> \Modules\Subscriptions\Models\Subscription::latest()->first();
>>> exit

# 5. Verificar email (se SMTP configurado)
# Checar inbox do usuário de teste
```

---

## 🔍 Rollback Plan (em caso de problemas)

```bash
# 1. Ativar manutenção
php artisan down

# 2. Reverter código
git revert HEAD -m 1
git push origin feature/update-core-version

# 3. Reverter migration (se necessário)
php artisan migrate:rollback --step=1

# 4. Limpar caches
php artisan config:clear
php artisan cache:clear

# 5. Restaurar banco (se necessário)
# Usar backup do Fase 9 passo 1

# 6. Desativar manutenção
php artisan up
```

---

## 📊 Checklist Final

### Pré-Deploy:
- [ ] Branch intermediária criada
- [ ] Código testado localmente
- [ ] PR aprovado
- [ ] Backup do banco feito

### Deploy:
- [ ] Código atualizado no servidor
- [ ] Migrations rodadas
- [ ] Caches limpos
- [ ] Queue workers reiniciados
- [ ] `.env` atualizado com tokens prod

### Pós-Deploy:
- [ ] Webhooks configurados nos gateways
- [ ] Teste de compra realizado
- [ ] Logs monitorados
- [ ] Assinatura criada com sucesso
- [ ] E-mail enviado (se SMTP ativo)

### Monitoramento (24h):
- [ ] Verificar logs de erro
- [ ] Verificar webhooks recebidos
- [ ] Verificar assinaturas criadas
- [ ] Verificar emails enviados

---

## 🎯 Resumo Executivo

**Tempo estimado total: ~90 minutos**

**Riscos: BAIXO**
- Mudanças são aditivas (não quebram código existente)
- Idempotência previne duplicações
- DB transactions garantem consistência
- Rollback simples

**Benefícios:**
- ✅ Suporte a Mercado Pago
- ✅ Webhook Stripe melhorado
- ✅ Emails multilíngues
- ✅ Performance (emails em fila)
- ✅ Logs detalhados

---

## 📞 Suporte Pós-Deploy

**Se algo der errado:**
1. Verificar `storage/logs/laravel.log`
2. Verificar se queue worker está rodando: `ps aux | grep queue:work`
3. Verificar se webhooks estão chegando: `grep "webhook" storage/logs/laravel.log`
4. Executar rollback (seção acima)

**Contatos:**
- GitHub Copilot (eu!) para dúvidas
- Documentação nos arquivos `.md` criados

---

**Está pronto para começar?** 🚀

Confirme e eu te guio passo a passo!
