# 📊 Guia Completo: Gestão de Usuários e Assinantes no Snaphubb

---

## 🎯 Introdução

Este guia explica **como funciona** a gestão de usuários e assinantes no Snaphubb, como o sistema identifica status de assinaturas (ativas/expiradas), e como isso impacta o negócio.

---

## 📋 Tabela de Conteúdos

1. [Estrutura de Dados](#1-estrutura-de-dados)
2. [Como o Sistema Funciona](#2-como-o-sistema-funciona)
3. [Acessando a Base de Dados](#3-acessando-a-base-de-dados)
4. [Impacto no Negócio](#4-impacto-no-negócio)
5. [Ações Automáticas vs Manuais](#5-ações-automáticas-vs-manuais)
6. [Exemplo Prático](#6-exemplo-prático)
7. [Relatórios e Dashboard](#7-relatórios-e-dashboard)

---

## 1. Estrutura de Dados

### 📊 Tabelas Principais

```
┌─────────────────────────────────────────────────────────────┐
│                        USERS (Usuários)                     │
├─────────────────────────────────────────────────────────────┤
│ id, username, email, status, is_subscribe, created_at, ... │
└─────────────────────────────────────────────────────────────┘
                            ↓
        ┌───────────────────┴───────────────────┐
        ↓                                       ↓
┌──────────────────────┐          ┌──────────────────────┐
│  SUBSCRIPTIONS       │          │  PLANS               │
│  (Assinaturas)       │          │  (Pacotes/Planos)    │
├──────────────────────┤          ├──────────────────────┤
│ • user_id            │          │ • id                 │
│ • plan_id            │          │ • name               │
│ • start_date         │          │ • duration           │
│ • end_date ⭐        │  ◄───────┤ • price              │
│ • status (ativo)     │          │ • currency           │
│ • amount             │          │ • status             │
│ • created_at         │          │ • planLimititation   │
└──────────────────────┘          └──────────────────────┘
```

### 🔑 Campos Críticos

#### **Tabela: `users`**
```sql
Column          | Tipo    | Descrição
─────────────────────────────────────────────────────────────
id              | INT     | ID único do usuário
username        | STRING  | Nome de usuário
email           | STRING  | Email (único)
status          | TINYINT | 0=Inativo, 1=Ativo ⭐
is_subscribe    | TINYINT | 0=Sem assinatura, 1=Tem assinatura ⭐
is_banned       | TINYINT | 0=Normal, 1=Banido
created_at      | TIMESTAMP | Data de cadastro
updated_at      | TIMESTAMP | Última atualização
```

#### **Tabela: `subscriptions`**
```sql
Column          | Tipo      | Descrição
─────────────────────────────────────────────────────────────
id              | INT       | ID da assinatura
user_id         | INT       | FK → users.id
plan_id         | INT       | FK → plans.id
start_date      | DATETIME  | Quando começou a assinatura
end_date        | DATETIME  | Quando EXPIRA a assinatura ⭐
status          | STRING    | 'active' ou 'inactive' ⭐
amount          | DOUBLE    | Valor pago
discount_percentage | DOUBLE | Desconto aplicado
tax_amount      | DOUBLE    | Imposto
total_amount    | DOUBLE    | Total pago
created_at      | TIMESTAMP | Quando foi criada
deleted_at      | TIMESTAMP | Soft delete (manutenção)
```

---

## 2. Como o Sistema Funciona

### 🔄 Fluxo de Assinatura

```
┌─────────────────────────────────────────────────────────┐
│ 1️⃣  USUÁRIO SE CADASTRA                                │
│  • Criado em: users.created_at                          │
│  • Status: status = 1 (ativo)                           │
│  • Assinatura: is_subscribe = 0 (sem assinatura)       │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 2️⃣  USUÁRIO COMPRA UM PLANO                             │
│  • Cria registro em: subscriptions                       │
│  • Define: start_date (hoje)                            │
│  • Define: end_date (hoje + duração do plano)           │
│  • Status: 'active'                                     │
│  • Atualiza: users.is_subscribe = 1 ✅                  │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 3️⃣  USUÁRIO USA A PLATAFORMA (tempo passa)             │
│  • Pode assistir conteúdo conforme plano               │
│  • Pode ter limitações por plano (dispositivos, etc)    │
│  • Histórico registrado em: user_watch_histories       │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 4️⃣  DIA X: ASSINATURA EXPIRA (end_date chegou)        │
│  • Sistema identifica: HOJE > end_date                  │
│  • Status muda: 'inactive' ou 'expired'                 │
│  • users.is_subscribe pode mudar para 0                 │
│  • Usuário PERDE ACESSO ao conteúdo                    │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 5️⃣  USUÁRIO PODE RENOVAR OU NÃO                        │
│  • Se renovar: nova assinatura criada ✅               │
│  • Se não renovar: fica como "ex-assinante"            │
│  • users.is_subscribe continua 0 (sem assinatura)      │
└─────────────────────────────────────────────────────────┘
```

---

## 3. Acessando a Base de Dados

### 🖥️ Via Backend (Admin Panel)

**Localização:** http://127.0.0.1:8002/app/admin

**Painel de Controle > Subscriptions**

Lá você vê:
- ✅ Todos os usuários com assinatura ativa
- ✅ Data de expiração de cada assinatura
- ✅ Status (ativo/expirado)
- ✅ Valor pago por assinatura
- ✅ Histórico de transações

---

### 💾 Via Banco de Dados Direto (DBEaver)

#### **Query: Ver todos os usuários com assinatura ATIVA**

```sql
SELECT 
    u.id,
    u.username,
    u.email,
    u.status,
    u.is_subscribe,
    s.id as subscription_id,
    s.start_date,
    s.end_date,
    s.status as subscription_status,
    p.name as plan_name,
    s.amount,
    DATEDIFF(s.end_date, NOW()) as days_remaining
FROM users u
LEFT JOIN subscriptions s ON u.id = s.user_id
LEFT JOIN plans p ON s.plan_id = p.id
WHERE s.status = 'active'
  AND s.end_date > NOW()  -- Ainda não expirou
ORDER BY s.end_date ASC;
```

**Resultado esperado:**
```
id | username   | email              | is_subscribe | end_date   | days_remaining
───┼────────────┼────────────────────┼──────────────┼────────────┼────────────────
1  | user_john  | john@example.com   | 1            | 2026-02-15 | 44
2  | user_maria | maria@example.com  | 1            | 2026-03-01 | 58
3  | user_pedro | pedro@example.com  | 1            | 2026-01-20 | 18
```

---

#### **Query: Ver assinaturas QUE VÃO EXPIRAR EM 7 DIAS**

```sql
SELECT 
    u.id,
    u.username,
    u.email,
    s.end_date,
    DATEDIFF(s.end_date, NOW()) as days_remaining,
    p.name as plan_name
FROM users u
JOIN subscriptions s ON u.id = s.user_id
JOIN plans p ON s.plan_id = p.id
WHERE s.status = 'active'
  AND s.end_date <= DATE_ADD(NOW(), INTERVAL 7 DAY)  -- Vence nos próximos 7 dias
  AND s.end_date > NOW()  -- Ainda não venceu
ORDER BY s.end_date ASC;
```

---

#### **Query: Ver TODAS as assinaturas expiradas (que não renovaram)**

```sql
SELECT 
    u.id,
    u.username,
    u.email,
    s.end_date as expiration_date,
    DATEDIFF(NOW(), s.end_date) as days_expired,
    u.is_subscribe as still_marked_as_subscriber
FROM users u
JOIN subscriptions s ON u.id = s.user_id
WHERE s.end_date < NOW()
  AND s.status IN ('inactive', 'expired')
ORDER BY s.end_date DESC;
```

---

#### **Query: Contagem de usuários por status (Dashboard)**

```sql
SELECT 
    COUNT(DISTINCT u.id) as total_users,
    SUM(CASE WHEN u.status = 1 THEN 1 ELSE 0 END) as active_users,
    SUM(CASE WHEN u.is_subscribe = 1 THEN 1 ELSE 0 END) as users_with_active_subscription,
    SUM(CASE WHEN u.is_subscribe = 0 THEN 1 ELSE 0 END) as users_without_subscription,
    COUNT(DISTINCT CASE 
        WHEN s.status = 'active' AND s.end_date > NOW() 
        THEN u.id 
    END) as current_active_subscribers
FROM users u
LEFT JOIN subscriptions s ON u.id = s.user_id;
```

**Resultado:**
```
total_users | active_users | users_with_active_subscription | current_active_subscribers
─────────────┼──────────────┼────────────────────────────────┼──────────────────────────
600         | 550          | 45                             | 42
```

---

## 4. Impacto no Negócio

### 💰 Receita e Métricas

#### **Como impacta os números do Dashboard?**

| Métrica | Cálculo | Status |
|---------|---------|--------|
| **Total Usuários** | `COUNT(users)` | Sempre cresce |
| **Usuários Ativos** | `COUNT(users WHERE status=1)` | Pode diminuir se você desativar |
| **Assinantes Ativos** | `COUNT(subscriptions WHERE status='active' AND end_date > NOW())` | ⭐ **O MAIS IMPORTANTE** |
| **Receita Total** | `SUM(subscriptions.amount)` | Aumenta com novas assinaturas |
| **Usuários a Expirar** | `COUNT(subscriptions WHERE end_date BETWEEN NOW AND NOW+7days)` | **Precisa de ação!** |

---

#### **Exemplo Prático de Impacto**

**Cenário Inicial (1º de Janeiro):**
```
Total Usuários:        1000
Usuários Ativos:       950
Assinantes Ativos:     450  ← ESTE É O MAIS IMPORTANTE
Receita Total:         R$ 45.000
```

**Novos Usuários (10 cadastros):**
```
Entram 10 novos usuários
Total Usuários:        1010  (1000 + 10)
Usuários Ativos:       960
Assinantes Ativos:     450  (ainda não compraram)
```

**2 dos 10 novos compraram (plano mensal):**
```
Total Usuários:        1010  (unchanged)
Usuários Ativos:       960   (unchanged)
Assinantes Ativos:     452   (450 + 2) ⭐
Receita Total:         R$ 45.xxx (aumentou!)
```

**Depois de 30 dias (vencimento):**
```
Se os 2 NÃO RENOVAREM:
Total Usuários:        1010  (usuarios nunca são deletados)
Usuários Ativos:       960   (unchanged)
Assinantes Ativos:     450   (452 - 2) ⭐ PERDEU 2
Receita Total:         Diminui!
```

---

### 📊 O que muda quando assinatura expira?

**Tabela: users**
```
ANTES (assinante ativo):
id | username | status | is_subscribe
1  | john     | 1      | 1  ✅ Tem acesso completo

DEPOIS (expirada, não renovou):
id | username | status | is_subscribe
1  | john     | 1      | 0  ❌ Perde acesso ao conteúdo premium
```

**Tabela: subscriptions**
```
ANTES:
id | user_id | status  | end_date   | days_left
1  | 1       | active  | 2026-02-01 | 30 dias

DEPOIS (após 2026-02-01):
id | user_id | status  | end_date   | Acesso
1  | 1       | expired | 2026-02-01 | ❌ BLOQUEADO
```

---

## 5. Ações Automáticas vs Manuais

### 🤖 Ações Automáticas do Sistema

| Ação | Quando | Automático? | Código |
|------|--------|------------|--------|
| Criar assinatura | Usuário paga | ✅ Sim | `SubscriptionController@store` |
| Marcar como expirada | `end_date < NOW()` | ⚠️ **Não!** | Necessário cron job |
| Atualizar `is_subscribe` para 0 | Assinatura expira | ⚠️ **Não!** | Necessário cron job |
| Bloquear acesso | Assinatura expirada | ⚠️ Parcial | `checkPlanSupportDevice()` |
| Enviar aviso de expiração | 7 dias antes | ⚠️ **Não!** | Necessário implementar |

---

### ⚠️ **IMPORTANTE: Cron Job Necessário**

Você PRECISA de um cron job que rode **diariamente** para:

1. **Identificar assinaturas expiradas**
2. **Atualizar status para 'expired'**
3. **Desabilitar acesso do usuário**
4. **Enviar notificação de expiração**

---

### 🛠️ Como Implementar Cron Job

**Arquivo:** `app/Console/Commands/ProcessExpiredSubscriptions.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Subscriptions\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class ProcessExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:process-expired';
    protected $description = 'Process expired subscriptions and update user access';

    public function handle()
    {
        // Encontrar assinaturas que expiraram hoje
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('end_date', '<', Carbon::now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            // Marcar como expirada
            $subscription->update([
                'status' => 'expired'
            ]);

            // Verificar se há outras assinaturas ativas
            $hasActiveSubscription = Subscription::where('user_id', $subscription->user_id)
                ->where('status', 'active')
                ->where('end_date', '>', Carbon::now())
                ->exists();

            // Se não há outras ativas, desabilitar acesso
            if (!$hasActiveSubscription) {
                User::where('id', $subscription->user_id)->update([
                    'is_subscribe' => 0
                ]);
            }

            // Enviar notificação ao usuário
            $this->sendExpirationNotification($subscription);
        }

        $this->info("Processed {$expiredSubscriptions->count()} expired subscriptions");
    }

    private function sendExpirationNotification($subscription)
    {
        // Implementar notificação aqui
        // Ex: Email, push notification, SMS
    }
}
```

---

**Registrar no Schedule:** `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('subscriptions:process-expired')
        ->daily()
        ->at('00:00')  // Toda noite à meia-noite
        ->runInBackground();
}
```

---

## 6. Exemplo Prático

### 📈 Cenário Completo: Os 10 Novos Usuários

**DIA 1 (2º de Janeiro)**
```
Entram 10 novos usuários: @novo1, @novo2, ... @novo10

Banco de dados (users):
───────────────────────────────────────────────────────────
id  | username | email            | status | is_subscribe
────┼──────────┼──────────────────┼────────┼──────────────
591 | novo1    | novo1@email.com  | 1      | 0
592 | novo2    | novo2@email.com  | 1      | 0
593 | novo3    | novo3@email.com  | 1      | 0
... | ...      | ...              | ...    | ...
600 | novo10   | novo10@email.com | 1      | 0

Dashboard:
Total Usuários: 600
Assinantes: 42 (inalterado)
```

---

**DIA 2 (3º de Janeiro)**
```
Novo2 compra plano "Premium Plus" (30 dias, R$ 49,90)

Banco de dados (subscriptions):
───────────────────────────────────────────────────────────
id  | user_id | plan_id | start_date | end_date   | status | amount
────┼─────────┼─────────┼────────────┼────────────┼────────┼────────
... | ...     | ...     | ...        | ...        | ...    | ...
999 | 592     | 5       | 2026-01-03 | 2026-02-03 | active | 49.90

Banco de dados (users) - Novo2 atualizado:
──────────────────────────────────────────────────────
id  | username | is_subscribe
────┼──────────┼──────────────
592 | novo2    | 1  ✅ Agora é assinante!

Dashboard:
Total Usuários: 600
Assinantes: 43 (42 + 1)
Receita: +R$ 49,90
```

---

**DIA 3 (4º de Janeiro)**
```
Novo8 e Novo10 também compram!

Novo8 → Plano "Standard" (30 dias, R$ 29,90)
Novo10 → Plano "Premium Plus" (30 dias, R$ 49,90)

Dashboard:
Total Usuários: 600
Assinantes: 45 (43 + 2)
Receita: +R$ 79,80 = Total R$ 45.129,70
```

---

**DIA 34 (3º de Fevereiro) - VENCIMENTO COMEÇA**
```
Novo2's subscription expira hoje (end_date = 2026-02-03)

Se NÃO RENOVAR:

1️⃣  Cron job detecta: NOW() > end_date
2️⃣  Atualiza subscriptions: status = 'expired'
3️⃣  Verifica: Novo2 tem outra assinatura ativa? NÃO
4️⃣  Atualiza users: is_subscribe = 0

Banco de dados (subscriptions):
──────────────────────────────────────────────────────
id  | user_id | status  | end_date
────┼─────────┼─────────┼──────────
999 | 592     | expired | 2026-02-03  ❌ Expirada!

Banco de dados (users):
──────────────────────────────
id  | username | is_subscribe
────┼──────────┼──────────────
592 | novo2    | 0  ❌ Perdeu acesso!

Dashboard:
Total Usuários: 600 (inalterado, nunca deleta)
Assinantes: 44 (45 - 1)  ⭐ DIMINUIU!
Receita: Diminui pela falta de renovação
```

---

**DIA 34 - NOVO2 RENOVA**
```
Novo2 clica em "Renovar Assinatura" (no email recebido no dia 27)

Nova assinatura criada:
id  | user_id | plan_id | start_date | end_date   | status | amount
────┼─────────┼─────────┼────────────┼────────────┼────────┼────────
1000| 592     | 5       | 2026-02-03 | 2026-03-05 | active | 49.90

Atualiza users:
id  | username | is_subscribe
────┼──────────┼──────────────
592 | novo2    | 1  ✅ Voltou a ser assinante!

Dashboard:
Assinantes: 45 (44 + 1) ✅ Voltou ao normal
```

---

## 7. Relatórios e Dashboard

### 📊 Como Acessar os Dados no Sistema

#### **Opção 1: Admin Panel**
- URL: http://127.0.0.1:8002/app/admin
- Menu: **Subscriptions** → **Manage Subscriptions**
- Vê: Lista de todas as assinaturas com filtros

#### **Opção 2: Dashboard**
- URL: http://127.0.0.1:8002/app/dashboard
- Mostra:
  - Total Users
  - Active Users
  - Total Subscribers ← **Assinantes ativos**
  - Subscriptions expiring in 7 days ← **Ação necessária**
  - Recent Transactions

#### **Opção 3: Banco de Dados (DBEaver)**
```sql
-- Abra uma conexão com seu banco MySQL
-- E execute as queries acima
```

---

### 🎯 Métricas Importantes para Acompanhar

```
┌──────────────────────────────────────────────────────┐
│          MÉTRICAS CRÍTICAS DO NEGÓCIO                │
├──────────────────────────────────────────────────────┤
│                                                      │
│ 1. CHURN RATE (Cancelamento)                        │
│    = (Perdidos neste mês) / (Total no início)      │
│    ⚠️ Se > 5% → Problema sério!                    │
│                                                      │
│ 2. NEW SUBSCRIPTIONS                                 │
│    = Novas assinaturas por mês                      │
│    ✅ Deve ser > Churn para crescer                │
│                                                      │
│ 3. EXPIRING SOON (7 dias)                           │
│    = Quantos vão perder acesso em 7 dias?          │
│    → Enviar email de renovação!                     │
│                                                      │
│ 4. LIFETIME VALUE (LTV)                             │
│    = Receita total / Número de assinantes          │
│    ✅ Quanto cada assinante vale?                  │
│                                                      │
│ 5. MRR (Monthly Recurring Revenue)                  │
│    = Receita mensal previsível                      │
│    ✅ Mais importante que receita total!            │
│                                                      │
└──────────────────────────────────────────────────────┘
```

---

### 💡 Dicas Práticas

**NUNCA FAÇA ISSO:**
```
❌ Deletar usuários (perde histórico)
❌ Deletar assinaturas (perde transações)
❌ Ignorar expiração (perde receita!)
```

**SEMPRE FAÇA ISSO:**
```
✅ Ter um cron job que rode diariamente
✅ Enviar email 7 dias ANTES de expirar
✅ Oferecer desconto para renovação
✅ Rastrear quem não renova (churn analysis)
✅ Backup automático do banco diariamente
```

---

## 📞 Próximas Etapas

1. **Ligar o servidor Vultr** (já feito ✅)
2. **Conectar ao banco MySQL antigo** (via DBEaver)
3. **Fazer backup do banco antigo** (arquivo `.sql`)
4. **Restaurar dados antigos localmente** (para testes)
5. **Configurar cron job de expiração** (crítico!)
6. **Implementar emails de renovação** (retenção)
7. **Monitorar métricas diárias** (dashboard)

---

**Dúvidas? Procure a seção relevante acima ou me mande uma mensagem! 🚀**
