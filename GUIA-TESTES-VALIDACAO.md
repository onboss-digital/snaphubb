# 🧪 Guia Completo de Testes e Validação - Snaphubb

## 📋 Índice
1. [Preparação do Ambiente](#preparação-do-ambiente)
2. [Executar Testes](#executar-testes)
3. [Scripts de Validação](#scripts-de-validação)
4. [Fluxos de Teste Manual](#fluxos-de-teste-manual)
5. [Checklist de Produção](#checklist-de-produção)

---

## 🔧 Preparação do Ambiente

### 1. Iniciar Docker e Banco de Dados

```bash
# Inicie os containers Docker
docker-compose up -d

# Aguarde ~10 segundos para MySQL ficar pronto
docker-compose ps

# Verifique se MySQL está saudável
docker-compose logs mysql | grep "ready for connections"
```

### 2. Instalar Dependências

```bash
# PHP dependencies
composer install

# JavaScript dependencies
npm install
```

### 3. Configurar Banco de Dados

```bash
# Executar migrations
php artisan migrate

# Limpar dados e semear com dados de teste
php artisan migrate:fresh --seed

# OU, se quiser semear apenas dados de assinatura
php artisan db:seed --class=SubscriptionTestSeeder
```

---

## 🧪 Executar Testes

### Testes Automatizados (PHPUnit)

#### 1. **Executar TODOS os testes**
```bash
php artisan test
```

#### 2. **Executar testes de usuários**
```bash
php artisan test tests/Feature/UserRegistrationTest.php
```

#### 3. **Executar testes de assinaturas**
```bash
php artisan test tests/Feature/SubscriptionFlowTest.php
```

#### 4. **Executar teste específico**
```bash
php artisan test tests/Feature/UserRegistrationTest.php --filter=test_user_can_be_created_successfully
```

#### 5. **Executar com output detalhado**
```bash
php artisan test --verbose
```

#### 6. **Executar com coverage (cobertura de código)**
```bash
php artisan test --coverage
```

---

## 📊 Scripts de Validação

### 1. Validar Integridade do Banco de Dados

```bash
php artisan tinker
```

Dentro do Tinker, execute:

```php
# Contar usuários
User::count();
// Output: número total de usuários

# Contar assinaturas
Subscription::count();
// Output: número total de assinaturas

# Verificar usuários com assinatura ativa
User::whereHas('subscription', function($q) {
    $q->where('status', 'active');
})->count();

# Listar assinaturas expiradas
Subscription::where('status', 'expired')->get();

# Verificar se há dados órfãos (assinaturas sem usuário)
Subscription::whereNull('user_id')->count();

# Verificar relacionamentos
$user = User::with('subscriptions')->find(1);
$user->subscriptions;

# Sair do Tinker
exit
```

### 2. Script: Relatório Completo de Dados

Crie o arquivo `check_data_integrity.php` na raiz do projeto:

```php
<?php

require 'bootstrap/app.php';

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Modules\Subscriptions\Models\Subscription;

echo "\n╔════════════════════════════════════════╗\n";
echo "║     RELATÓRIO DE INTEGRIDADE DE DADOS    ║\n";
echo "╚════════════════════════════════════════╝\n\n";

// 1. Estatísticas gerais
echo "📊 ESTATÍSTICAS GERAIS:\n";
echo "  • Total de usuários: " . User::count() . "\n";
echo "  • Total de assinaturas: " . Subscription::count() . "\n";
echo "  • Usuários ativos: " . User::where('status', 1)->count() . "\n";
echo "  • Usuários com assinatura: " . User::where('is_subscribe', 1)->count() . "\n\n";

// 2. Assinaturas por status
echo "📈 ASSINATURAS POR STATUS:\n";
$byStatus = Subscription::groupBy('status')->selectRaw('status, count(*) as total')->get();
foreach ($byStatus as $row) {
    echo "  • " . ucfirst($row->status) . ": " . $row->total . "\n";
}
echo "\n";

// 3. Assinaturas por plano
echo "🎯 ASSINATURAS POR PLANO:\n";
$byPlan = Subscription::with('plan')
    ->groupBy('plan_id')
    ->selectRaw('plan_id, count(*) as total, status')
    ->get();
foreach ($byPlan as $row) {
    $planName = $row->plan ? $row->plan->name : 'Sem Plano';
    echo "  • $planName: " . $row->total . "\n";
}
echo "\n";

// 4. Verificar integridade referencial
echo "⚠️  VERIFICAÇÕES DE INTEGRIDADE:\n";

$orphanSubs = Subscription::whereNull('user_id')->count();
echo "  • Assinaturas órfãs (sem usuário): " . ($orphanSubs > 0 ? "❌ $orphanSubs" : "✅ Nenhuma") . "\n";

$invalidPlans = Subscription::whereNull('plan_id')->count();
echo "  • Assinaturas sem plano: " . ($invalidPlans > 0 ? "❌ $invalidPlans" : "✅ Nenhuma") . "\n";

// 5. Usuários com múltiplas assinaturas
echo "\n👥 USUÁRIOS COM MÚLTIPLAS ASSINATURAS:\n";
$multiSub = User::withCount('subscriptions')
    ->having('subscriptions_count', '>', 1)
    ->get();

if ($multiSub->count() > 0) {
    foreach ($multiSub as $user) {
        echo "  • {$user->full_name} ({$user->email}): {$user->subscriptions_count} assinaturas\n";
    }
} else {
    echo "  ✅ Nenhum usuário com múltiplas assinaturas\n";
}

echo "\n✅ Relatório concluído!\n\n";
```

Execute com:
```bash
php check_data_integrity.php
```

### 3. Validar Backup e Recuperação

```bash
# Fazer backup do banco de dados
php artisan db:backup

# Verificar backups
ls -la storage/backups/

# Restaurar a partir de backup
php artisan db:restore
```

---

## 🔄 Fluxos de Teste Manual

### Fluxo 1: Criar Novo Usuário com Assinatura

```bash
php artisan tinker
```

```php
// 1. Criar usuário
$user = User::create([
    'first_name' => 'Teste',
    'last_name' => 'Manual',
    'email' => 'teste.manual@example.com',
    'password' => Hash::make('password123'),
    'status' => 1,
    'is_subscribe' => 0,
]);

echo "✅ Usuário criado: {$user->email} (ID: {$user->id})\n";

// 2. Atribuir assinatura
$plan = Plan::first(); // Pega o primeiro plano

$subscription = Subscription::create([
    'user_id' => $user->id,
    'plan_id' => $plan->id,
    'start_date' => now(),
    'end_date' => now()->addMonth(),
    'status' => 'active',
    'amount' => $plan->price,
    'tax_amount' => $plan->price * 0.175,
    'total_amount' => $plan->price + ($plan->price * 0.175),
    'type' => 'monthly',
    'duration' => 30,
]);

echo "✅ Assinatura criada: {$subscription->id}\n";

// 3. Verificar dados
$userWithSub = User::with('subscriptions')->find($user->id);
echo "✅ Verificação: Usuário tem " . $userWithSub->subscriptions->count() . " assinatura(s)\n";

exit
```

### Fluxo 2: Atualizar Status de Assinatura

```bash
php artisan tinker
```

```php
// 1. Obter assinatura ativa
$subscription = Subscription::where('status', 'active')->first();

if (!$subscription) {
    echo "❌ Nenhuma assinatura ativa encontrada\n";
    exit;
}

echo "📝 Assinatura encontrada: {$subscription->id} (Status: {$subscription->status})\n";

// 2. Marcar como expirada
$subscription->update(['status' => 'expired']);
echo "✅ Status atualizado para 'expired'\n";

// 3. Verificar atualização
$fresh = $subscription->fresh();
echo "✅ Confirmação: Status atual é '{$fresh->status}'\n";

// 4. Renovar assinatura
$fresh->update([
    'status' => 'active',
    'start_date' => now(),
    'end_date' => now()->addMonth(),
]);
echo "✅ Assinatura renovada\n";

exit
```

### Fluxo 3: Cancelar Assinatura

```bash
php artisan tinker
```

```php
// 1. Obter assinatura
$subscription = Subscription::where('status', 'active')->first();

// 2. Cancelar
$subscription->update(['status' => 'cancelled']);

// 3. Atualizar usuário se não tiver outras ativas
$user = $subscription->user;
$hasActiveSubscription = $user->subscriptions()
    ->where('status', 'active')
    ->exists();

if (!$hasActiveSubscription) {
    $user->update(['is_subscribe' => 0]);
    echo "✅ Usuário marcado como sem assinatura ativa\n";
}

echo "✅ Assinatura cancelada\n";

exit
```

---

## ✅ Checklist de Produção

Antes de fazer deploy para produção, verifique:

### 1. Banco de Dados
- [ ] Todas as migrations foram executadas: `php artisan migrate:status`
- [ ] Não há dados órfãos (assinaturas sem usuário)
- [ ] Foreign keys estão configuradas corretamente
- [ ] Índices estão presentes nas colunas de busca frequente
- [ ] Backup automático está configurado

### 2. Testes
- [ ] Todos os testes passam: `php artisan test`
- [ ] Cobertura de teste > 80%: `php artisan test --coverage`
- [ ] Sem avisos ou deprecações

### 3. Segurança
- [ ] Senhas são hasheadas com bcrypt
- [ ] Timestamps de criação/atualização estão corretos
- [ ] Soft deletes funcionam corretamente
- [ ] Não há dados sensíveis em logs

### 4. Performance
- [ ] Eager loading de relacionamentos está implementado
- [ ] Queries N+1 foram eliminadas
- [ ] Índices de banco de dados estão otimizados
- [ ] Cache está configurado

### 5. Documentação
- [ ] README atualizado com instruções
- [ ] Variáveis de ambiente documentadas
- [ ] Fluxos de API documentados

### 6. Monitoramento
- [ ] Logs estão sendo coletados
- [ ] Alertas estão configurados para erros críticos
- [ ] Dashboard de monitoramento está acessível

---

## 🚀 Comandos Rápidos

| Tarefa | Comando |
|--------|---------|
| **Executar testes** | `php artisan test` |
| **Teste específico** | `php artisan test tests/Feature/UserRegistrationTest.php` |
| **Resetar DB** | `php artisan migrate:fresh --seed` |
| **Semear dados** | `php artisan db:seed --class=SubscriptionTestSeeder` |
| **Verificar banco** | `php artisan tinker` |
| **Limpar cache** | `php artisan cache:clear` |
| **Regenerar keys** | `php artisan ide-helper:generate` |

---

## 📞 Troubleshooting

### Erro: "Connection refused" no MySQL
```bash
docker-compose up -d
docker-compose logs mysql
```

### Erro: "SQLSTATE[HY000]"
```bash
# Verificar se MySQL está rodando
docker-compose ps

# Reiniciar MySQL
docker-compose restart mysql
```

### Testes falhando
```bash
# Limpar cache antes de rodar testes
php artisan cache:clear
php artisan test --no-cache
```

### Dados perdidos
```bash
# Restaurar do backup
php artisan db:restore

# OU, refazer migrations
php artisan migrate:fresh --seed
```

---

**Última atualização:** Janeiro 2026
**Status:** ✅ Pronto para produção
