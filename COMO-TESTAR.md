# 🎯 Como Testar - Guia Passo a Passo

## 📌 Seu Objetivo

Você quer garantir que:
1. ✅ Novos usuários sejam cadastrados corretamente
2. ✅ Assinaturas (ativas, expiradas, canceladas) sejam atualizadas sem perda de dados
3. ✅ Dados permaneçam seguros e consistentes
4. ✅ Tudo esteja testável em ambiente local
5. ✅ Tudo esteja pronto para produção

---

## 🚀 PASSO 1: Setup Inicial (10 minutos)

### 1.1 - Iniciar Docker

```bash
# Abra um terminal e navegue até a pasta do projeto
cd "e:\ONBOSS DIGITAL\SNAPHUBB\snaphubb"

# Inicie os containers
docker-compose up -d

# Verifique se MySQL está pronto
docker-compose ps

# Output esperado:
# NAME                    STATUS
# snaphubb_mysql_1        Up (healthy)
# snaphubb_redis_1        Up
# snaphubb_meilisearch_1  Up
```

### 1.2 - Instalar Dependências

```bash
# Instale dependências PHP
composer install

# Instale dependências JavaScript
npm install
```

### 1.3 - Preparar Banco de Dados

```bash
# Execute as migrations
php artisan migrate

# Popule com dados de teste
php artisan db:seed --class=SubscriptionTestSeeder

# Esperado: "✓ Dados de teste criados com sucesso!"
```

---

## ✅ PASSO 2: Validar os Dados (5 minutos)

### 2.1 - Ver Quantos Dados Foram Criados

```bash
# Abra o Tinker (console interativo)
php artisan tinker

# Conte usuários
> User::count()
# Esperado: ≥ 17

# Conte assinaturas
> Subscription::count()
# Esperado: ≥ 17

# Conte assinaturas ativas
> Subscription::where('status', 'active')->count()
# Esperado: ≥ 8

# Saia do Tinker
> exit
```

### 2.2 - Executar Relatório de Integridade

```bash
# Execute o script de diagnóstico
php check_data_integrity.php

# Esperado:
# 📊 ESTATÍSTICAS GERAIS
# 📈 ASSINATURAS POR STATUS
# 🎯 ASSINATURAS POR PLANO
# ⚠️  VERIFICAÇÕES DE INTEGRIDADE
# 🏥 RESUMO DE SAÚDE: ✅ (score ≥ 90)
```

---

## 🧪 PASSO 3: Executar Testes Automatizados (10 minutos)

### 3.1 - Rodar Todos os Testes

```bash
# Execute todos os testes
php artisan test

# Esperado: Todos os testes passando (PASSED)
# Example output:
# PASS  Tests\Feature\UserRegistrationTest
#   ✓ test_user_can_be_created_successfully
#   ✓ test_user_email_must_be_unique
#   ... mais testes

# PASS  Tests\Feature\SubscriptionFlowTest
#   ✓ test_can_create_active_subscription
#   ✓ test_subscription_can_be_marked_expired
#   ... mais testes
```

### 3.2 - Rodar Testes Específicos

```bash
# Só testes de usuários
php artisan test tests/Feature/UserRegistrationTest.php

# Só testes de assinaturas
php artisan test tests/Feature/SubscriptionFlowTest.php

# Um teste específico
php artisan test --filter=test_user_can_be_created_successfully
```

### 3.3 - Ver Cobertura de Testes

```bash
# Execute com cobertura
php artisan test --coverage

# Esperado: ≥ 80% de cobertura
```

---

## 🔄 PASSO 4: Testar Fluxos Manualmente (15 minutos)

### 4.1 - Fluxo 1: Criar Novo Usuário com Assinatura

```bash
# Abra o Tinker
php artisan tinker

# Passo 1: Crie um novo usuário
$user = User::create([
  'first_name' => 'João',
  'last_name' => 'Teste',
  'email' => 'joao.teste@example.com',
  'password' => Hash::make('password123'),
  'status' => 1,
  'is_subscribe' => 0,
]);

echo "✅ Usuário criado: " . $user->id;

# Passo 2: Crie uma assinatura para ele
$subscription = Subscription::create([
  'user_id' => $user->id,
  'plan_id' => 1,
  'start_date' => now(),
  'end_date' => now()->addMonth(),
  'status' => 'active',
  'amount' => 29.99,
  'discount_percentage' => 0,
  'tax_amount' => 5.25,
  'total_amount' => 35.24,
  'type' => 'monthly',
  'duration' => 30,
]);

echo "✅ Assinatura criada: " . $subscription->id;

# Passo 3: Verifique se está tudo conectado
$userFresh = User::with('subscriptions')->find($user->id);
echo "✅ Usuário tem " . $userFresh->subscriptions->count() . " assinatura(s)";

# Passo 4: Saia
> exit

# ✅ Resultado: Novo usuário e assinatura criados com sucesso!
```

### 4.2 - Fluxo 2: Atualizar Status da Assinatura

```bash
php artisan tinker

# Obtenha uma assinatura ativa
$subscription = Subscription::where('status', 'active')->first();

echo "📝 Assinatura encontrada: " . $subscription->id;

# Marque como expirada
$subscription->update(['status' => 'expired']);
echo "✅ Status atualizado para 'expired'";

# Verifique
$fresh = $subscription->fresh();
echo "✅ Confirmação: Status é '" . $fresh->status . "'";

# Renove a assinatura
$fresh->update([
  'status' => 'active',
  'start_date' => now(),
  'end_date' => now()->addMonth(),
]);
echo "✅ Assinatura renovada";

> exit

# ✅ Resultado: Assinatura foi atualizada e renovada com sucesso!
```

### 4.3 - Fluxo 3: Cancelar Assinatura

```bash
php artisan tinker

# Obtenha uma assinatura ativa
$subscription = Subscription::where('status', 'active')->first();
$userId = $subscription->user_id;

echo "📝 Cancelando assinatura " . $subscription->id;

# Cancele
$subscription->update(['status' => 'cancelled']);
echo "✅ Status atualizado para 'cancelled'";

# Verifique se há outras ativas
$user = User::find($userId);
$hasActiveSubscription = $user->subscriptions()
  ->where('status', 'active')
  ->exists();

# Se não há mais ativas, marque usuário como sem assinatura
if (!$hasActiveSubscription) {
  $user->update(['is_subscribe' => 0]);
  echo "✅ Usuário marcado como sem assinatura ativa";
}

> exit

# ✅ Resultado: Assinatura cancelada e usuário atualizado!
```

### 4.4 - Fluxo 4: Verificar Múltiplas Assinaturas

```bash
php artisan tinker

# Encontre um usuário com múltiplas assinaturas
$user = User::where('email', 'multi@example.com')->first();

# Liste todas
$all = $user->subscriptions;
echo "Total de assinaturas: " . $all->count();

# Encontre a ativa
$active = $user->subscriptions()->where('status', 'active')->first();
echo "Assinatura ativa: " . $active->id . " (" . $active->status . ")";

# Veja todas as expiradas
$expired = $user->subscriptions()->where('status', 'expired')->get();
echo "Assinaturas expiradas: " . $expired->count();

> exit

# ✅ Resultado: Histórico de assinaturas acessível!
```

---

## 📊 PASSO 5: Analisar Dados e Relatórios (5 minutos)

### 5.1 - Executar Diagnóstico Completo

```bash
php check_data_integrity.php
```

Verifique se:
- ✅ Total de usuários está correto
- ✅ Total de assinaturas está correto
- ✅ Distribuição por status está equilibrada
- ✅ Score de saúde é ≥ 90
- ✅ Nenhum alerta crítico

### 5.2 - Analisar via Tinker

```bash
php artisan tinker

# Assinaturas próximas de expirar
> Subscription::where('status', 'active')
>   ->whereBetween('end_date', [now(), now()->addDays(7)])
>   ->with('user')
>   ->get();

# Usuários com múltiplas assinaturas
> User::withCount('subscriptions')
>   ->having('subscriptions_count', '>', 1)
>   ->get();

# Total de receita
> Subscription::sum('total_amount');

# Receita ativa
> Subscription::where('status', 'active')->sum('total_amount');

> exit
```

---

## 🔐 PASSO 6: Validar Segurança (5 minutos)

### 6.1 - Senhas Hasheadas

```bash
php artisan tinker

# Obtenha um usuário
$user = User::first();

# Verifique se a senha funciona
$passwordCorrect = Hash::check('password123', $user->password);
echo $passwordCorrect ? "✅ Senha funciona" : "❌ Erro na senha";

# A senha armazenada deve ser diferente
echo "Senha armazenada (hasheada): " . substr($user->password, 0, 20) . "...";

> exit
```

### 6.2 - Soft Deletes

```bash
php artisan tinker

# Crie e delete um usuário
$user = User::create([
  'first_name' => 'Teste',
  'last_name' => 'Exclusão',
  'email' => 'teste.delete@example.com',
  'password' => 'test',
]);

$userId = $user->id;
echo "✅ Usuário criado: " . $userId;

# Delete
$user->delete();
echo "✅ Usuário deletado";

# Não aparece na query normal
$exists = User::where('id', $userId)->exists();
echo $exists ? "❌ Ainda aparece!" : "✅ Não aparece (soft delete funcionando)";

# Aparece com withTrashed
$existsWithTrashed = User::withTrashed()->where('id', $userId)->exists();
echo $existsWithTrashed ? "✅ Aparece com withTrashed (dados preservados)" : "❌ Erro!";

# Restaure
User::withTrashed()->find($userId)->restore();
echo "✅ Usuário restaurado";

> exit
```

---

## 📝 PASSO 7: Documentação (Leia para Referência)

Leia estes guias para mais detalhes:

1. **[RESUMO-EXECUTIVO.md](RESUMO-EXECUTIVO.md)** - Visão geral do que foi criado
2. **[GUIA-TESTES-VALIDACAO.md](GUIA-TESTES-VALIDACAO.md)** - Instruções completas
3. **[GUIA-TESTES-API.md](GUIA-TESTES-API.md)** - Endpoints e requisições
4. **[CHECKLIST-IMPLEMENTACAO.md](CHECKLIST-IMPLEMENTACAO.md)** - Acompanhe seu progresso

---

## ✅ CHECKLIST FINAL

Após executar os passos acima, verifique:

- [ ] Docker está rodando
- [ ] MySQL está conectado
- [ ] Banco de dados foi migrado
- [ ] Dados de teste foram criados (≥17 usuários e assinaturas)
- [ ] Script de integridade passou (score ≥ 90)
- [ ] Todos os testes automatizados passaram
- [ ] Fluxo 1 (novo usuário) funcionou
- [ ] Fluxo 2 (atualizar status) funcionou
- [ ] Fluxo 3 (cancelar) funcionou
- [ ] Fluxo 4 (múltiplas assinaturas) funcionou
- [ ] Senhas estão hasheadas
- [ ] Soft deletes funcionam
- [ ] Você tem documentação para referência

---

## 🎉 PRONTO!

Se todos os testes e fluxos passaram, você pode ter **TOTAL CONFIANÇA** de que:

✅ Novos usuários serão cadastrados corretamente  
✅ Assinaturas serão atualizadas sem problemas  
✅ Dados nunca serão perdidos  
✅ Tudo está seguro e consistente  
✅ Tudo está pronto para produção  

---

## 🆘 Problemas?

### Erro: "Connection refused" do MySQL
```bash
docker-compose up -d
docker-compose logs mysql
```

### Erro nos testes
```bash
php artisan cache:clear
php artisan test
```

### Dados não criados
```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=SubscriptionTestSeeder
```

### Ainda com dúvidas?
Consulte [GUIA-TESTES-VALIDACAO.md](GUIA-TESTES-VALIDACAO.md) - Seção Troubleshooting

---

**Tempo estimado total:** ~45 minutos  
**Dificuldade:** ⭐⭐ (Fácil)  
**Resultado:** ✅ Confiança 100% em seus dados

Boa sorte! 🚀
