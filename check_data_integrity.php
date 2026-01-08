<?php

require 'bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->make(Kernel::class)->bootstrap();

use App\Models\User;
use Modules\Subscriptions\Models\Subscription;
use Carbon\Carbon;

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║          RELATÓRIO DE INTEGRIDADE DE DADOS                  ║\n";
echo "║                  Snaphubb - Users & Subscriptions           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// 1. Estatísticas gerais
echo "📊 ESTATÍSTICAS GERAIS:\n";
echo "┌─────────────────────────────────────────────────────────┐\n";
echo "  • Total de usuários: " . User::count() . "\n";
echo "  • Total de assinaturas: " . Subscription::count() . "\n";
echo "  • Usuários ativos (status=1): " . User::where('status', 1)->count() . "\n";
echo "  • Usuários banidos (status=0): " . User::where('is_banned', 1)->count() . "\n";
echo "  • Usuários com assinatura marcada: " . User::where('is_subscribe', 1)->count() . "\n";
echo "└─────────────────────────────────────────────────────────┘\n\n";

// 2. Assinaturas por status
echo "📈 ASSINATURAS POR STATUS:\n";
echo "┌─────────────────────────────────────────────────────────┐\n";
$byStatus = Subscription::groupBy('status')
    ->selectRaw('status, count(*) as total')
    ->get();

foreach ($byStatus as $row) {
    $statusLabel = match($row->status) {
        'active' => '🟢 Ativa',
        'expired' => '🟡 Expirada',
        'cancelled' => '🔴 Cancelada',
        default => '⚪ ' . ucfirst($row->status),
    };
    printf("  %-20s %d\n", $statusLabel, $row->total);
}
echo "└─────────────────────────────────────────────────────────┘\n\n";

// 3. Assinaturas por plano
echo "🎯 ASSINATURAS POR PLANO:\n";
echo "┌─────────────────────────────────────────────────────────┐\n";
$byPlan = Subscription::with('plan')
    ->groupBy('plan_id')
    ->selectRaw('plan_id, status, count(*) as total')
    ->orderBy('plan_id')
    ->get()
    ->groupBy('plan_id');

foreach ($byPlan as $planId => $subs) {
    $plan = $subs->first()->plan;
    $planName = $plan ? $plan->name : 'Sem Plano (ID: ' . $planId . ')';
    echo "  📦 $planName:\n";
    
    foreach ($subs as $sub) {
        printf("      • %-15s %d\n", ucfirst($sub->status), $sub->total);
    }
}
echo "└─────────────────────────────────────────────────────────┘\n\n";

// 4. Verificar integridade referencial
echo "⚠️  VERIFICAÇÕES DE INTEGRIDADE:\n";
echo "┌─────────────────────────────────────────────────────────┐\n";

$orphanSubs = Subscription::whereNull('user_id')->count();
$status1 = $orphanSubs > 0 ? "❌ $orphanSubs" : "✅ OK";
echo "  Assinaturas órfãs (sem usuário): $status1\n";

$invalidPlans = Subscription::whereNull('plan_id')->count();
$status2 = $invalidPlans > 0 ? "❌ $invalidPlans" : "✅ OK";
echo "  Assinaturas sem plano: $status2\n";

$discrepancy = User::where('is_subscribe', 1)
    ->whereDoesntHave('subscriptions', function($q) {
        $q->where('status', 'active');
    })
    ->count();
$status3 = $discrepancy > 0 ? "⚠️  $discrepancy" : "✅ OK";
echo "  Usuários marcados como subscrito mas sem assinatura ativa: $status3\n";

$futureEnd = Subscription::where('end_date', '<', now())
    ->where('status', 'active')
    ->count();
$status4 = $futureEnd > 0 ? "⚠️  $futureEnd" : "✅ OK";
echo "  Assinaturas ativas com data final no passado: $status4\n";

echo "└─────────────────────────────────────────────────────────┘\n\n";

// 5. Usuários com múltiplas assinaturas
echo "👥 USUÁRIOS COM MÚLTIPLAS ASSINATURAS:\n";
echo "┌─────────────────────────────────────────────────────────┐\n";
$multiSub = User::withCount('subscriptions')
    ->having('subscriptions_count', '>', 1)
    ->orderBy('subscriptions_count', 'desc')
    ->limit(10)
    ->get();

if ($multiSub->count() > 0) {
    foreach ($multiSub as $user) {
        echo "  • {$user->full_name} ({$user->email})\n";
        echo "      └─ {$user->subscriptions_count} assinaturas\n";
    }
} else {
    echo "  ✅ Nenhum usuário com múltiplas assinaturas\n";
}
echo "└─────────────────────────────────────────────────────────┘\n\n";

// 6. Assinaturas próximas de expirar
echo "⏰ ASSINATURAS PRÓXIMAS DE EXPIRAR (< 7 dias):\n";
echo "┌─────────────────────────────────────────────────────────┐\n";
$expiring = Subscription::where('status', 'active')
    ->whereBetween('end_date', [now(), now()->addDays(7)])
    ->with('user')
    ->orderBy('end_date')
    ->get();

if ($expiring->count() > 0) {
    foreach ($expiring as $sub) {
        $daysLeft = $sub->end_date->diffInDays(now());
        echo "  • {$sub->user->full_name} - Expira em {$daysLeft} dias\n";
    }
} else {
    echo "  ✅ Nenhuma assinatura próxima de expirar\n";
}
echo "└─────────────────────────────────────────────────────────┘\n\n";

// 7. Valores financeiros
echo "💰 RESUMO FINANCEIRO:\n";
echo "┌─────────────────────────────────────────────────────────┐\n";

$totalValue = Subscription::sum('total_amount');
$activeValue = Subscription::where('status', 'active')->sum('total_amount');
$averageValue = Subscription::average('total_amount');

printf("  Total de receita (todas as assinaturas): R$ %.2f\n", $totalValue);
printf("  Receita ativa (assinaturas ativas): R$ %.2f\n", $activeValue);
printf("  Ticket médio: R$ %.2f\n", $averageValue);

echo "└─────────────────────────────────────────────────────────┘\n\n";

// 8. Resumo de saúde geral
echo "🏥 RESUMO DE SAÚDE:\n";
echo "┌─────────────────────────────────────────────────────────┐\n";

$healthScore = 100;
if ($orphanSubs > 0) $healthScore -= 20;
if ($invalidPlans > 0) $healthScore -= 20;
if ($discrepancy > 0) $healthScore -= 10;
if ($futureEnd > 0) $healthScore -= 10;

$healthIcon = match(true) {
    $healthScore >= 90 => '✅',
    $healthScore >= 70 => '⚠️ ',
    default => '❌'
};

echo "  Status geral: $healthIcon ($healthScore/100)\n";

if ($healthScore >= 90) {
    echo "  Base de dados está SAUDÁVEL e pronta para produção\n";
} elseif ($healthScore >= 70) {
    echo "  Alguns problemas detectados - revisar alertas acima\n";
} else {
    echo "  PROBLEMAS CRÍTICOS DETECTADOS - Ação imediata necessária!\n";
}

echo "└─────────────────────────────────────────────────────────┘\n\n";

// Rodapé
echo "📅 Gerado em: " . Carbon::now()->format('d/m/Y H:i:s') . "\n";
echo "✅ Relatório concluído!\n\n";

?>
