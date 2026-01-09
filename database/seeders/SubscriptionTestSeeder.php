<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Subscriptions\Models\Subscription;
use Modules\Subscriptions\Models\Plan;
use Carbon\Carbon;

class SubscriptionTestSeeder extends Seeder
{
    /**
     * Seed the application's database com dados realistas de teste
     */
    public function run(): void
    {
        // Cria 3 planos de teste
        $plans = $this->createPlans();

        // Cria usuários com diferentes estados de assinatura
        $this->createUsersWithSubscriptions($plans);

        $this->command->info('✓ Dados de teste criados com sucesso!');
    }

    /**
     * Cria planos de assinatura para teste
     */
    private function createPlans(): array
    {
        $plans = [];

        // Plano Basic
        $plans['basic'] = Plan::firstOrCreate(
            ['name' => 'Basic'],
            [
                'description' => 'Plano básico com acesso limitado',
                'price' => 9.99,
                'duration' => 30,
                'features' => json_encode(['ads' => true, 'hd' => false]),
            ]
        );

        // Plano Premium
        $plans['premium'] = Plan::firstOrCreate(
            ['name' => 'Premium'],
            [
                'description' => 'Plano premium sem anúncios',
                'price' => 19.99,
                'duration' => 30,
                'features' => json_encode(['ads' => false, 'hd' => true, 'devices' => 2]),
            ]
        );

        // Plano Pro
        $plans['pro'] = Plan::firstOrCreate(
            ['name' => 'Pro'],
            [
                'description' => 'Plano profissional com todos os recursos',
                'price' => 29.99,
                'duration' => 30,
                'features' => json_encode(['ads' => false, 'hd' => true, 'devices' => 4, '4k' => true]),
            ]
        );

        $this->command->info("Planos criados: " . count($plans));

        return $plans;
    }

    /**
     * Cria usuários com diferentes estados de assinatura
     */
    private function createUsersWithSubscriptions($plans): void
    {
        // Grupo 1: Usuário com assinatura ATIVA (Premium)
        $activeUser = User::firstOrCreate(
            ['email' => 'active@example.com'],
            [
                'first_name' => 'João',
                'last_name' => 'Ativo',
                'username' => 'joao_ativo',
                'password' => Hash::make('password123'),
                'mobile' => '5511999999999',
                'gender' => 'male',
                'status' => 1,
                'is_subscribe' => 1,
            ]
        );

        Subscription::firstOrCreate(
            ['user_id' => $activeUser->id, 'status' => 'active'],
            [
                'plan_id' => $plans['premium']->id,
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(15), // Vencimento em 15 dias
                'status' => 'active',
                'amount' => 19.99,
                'discount_percentage' => 0,
                'tax_amount' => 3.50,
                'total_amount' => 23.49,
                'type' => 'monthly',
                'duration' => 30,
                'identifier' => 'SUB-ACTIVE-001',
            ]
        );

        // Grupo 2: Usuário com assinatura EXPIRADA
        $expiredUser = User::firstOrCreate(
            ['email' => 'expired@example.com'],
            [
                'first_name' => 'Maria',
                'last_name' => 'Expirada',
                'username' => 'maria_expirada',
                'password' => Hash::make('password123'),
                'mobile' => '5511988888888',
                'gender' => 'female',
                'status' => 1,
                'is_subscribe' => 0,
            ]
        );

        Subscription::firstOrCreate(
            ['user_id' => $expiredUser->id, 'status' => 'expired'],
            [
                'plan_id' => $plans['basic']->id,
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->subDays(5), // Expirou há 5 dias
                'status' => 'expired',
                'amount' => 9.99,
                'discount_percentage' => 0,
                'tax_amount' => 1.75,
                'total_amount' => 11.74,
                'type' => 'monthly',
                'duration' => 30,
                'identifier' => 'SUB-EXPIRED-001',
            ]
        );

        // Grupo 3: Usuário com assinatura CANCELADA
        $cancelledUser = User::firstOrCreate(
            ['email' => 'cancelled@example.com'],
            [
                'first_name' => 'Pedro',
                'last_name' => 'Cancelado',
                'username' => 'pedro_cancelado',
                'password' => Hash::make('password123'),
                'mobile' => '5511977777777',
                'gender' => 'male',
                'status' => 1,
                'is_subscribe' => 0,
            ]
        );

        Subscription::firstOrCreate(
            ['user_id' => $cancelledUser->id, 'status' => 'cancelled'],
            [
                'plan_id' => $plans['pro']->id,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addDays(10),
                'status' => 'cancelled',
                'amount' => 29.99,
                'discount_percentage' => 10,
                'tax_amount' => 5.40,
                'total_amount' => 32.39,
                'type' => 'monthly',
                'duration' => 30,
                'identifier' => 'SUB-CANCELLED-001',
            ]
        );

        // Grupo 4: Usuário com MÚLTIPLAS assinaturas (renovações/histórico)
        $multiUser = User::firstOrCreate(
            ['email' => 'multi@example.com'],
            [
                'first_name' => 'Ana',
                'last_name' => 'Multi',
                'username' => 'ana_multi',
                'password' => Hash::make('password123'),
                'mobile' => '5511966666666',
                'gender' => 'female',
                'status' => 1,
                'is_subscribe' => 1,
            ]
        );

        // Assinatura expirada anterior
        Subscription::firstOrCreate(
            ['user_id' => $multiUser->id, 'status' => 'expired', 'identifier' => 'SUB-MULTI-001'],
            [
                'plan_id' => $plans['basic']->id,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->subMonth(),
                'status' => 'expired',
                'amount' => 9.99,
                'discount_percentage' => 0,
                'tax_amount' => 1.75,
                'total_amount' => 11.74,
                'type' => 'monthly',
                'duration' => 30,
            ]
        );

        // Assinatura ativa atual (premium)
        Subscription::firstOrCreate(
            ['user_id' => $multiUser->id, 'status' => 'active', 'identifier' => 'SUB-MULTI-002'],
            [
                'plan_id' => $plans['premium']->id,
                'start_date' => Carbon::now()->subMonth(),
                'end_date' => Carbon::now()->addMonth(),
                'status' => 'active',
                'amount' => 19.99,
                'discount_percentage' => 0,
                'tax_amount' => 3.50,
                'total_amount' => 23.49,
                'type' => 'monthly',
                'duration' => 30,
            ]
        );

        // Grupo 5: Usuário SEM assinatura
        $noSubscriptionUser = User::firstOrCreate(
            ['email' => 'nosubscription@example.com'],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Sem Plano',
                'username' => 'carlos_free',
                'password' => Hash::make('password123'),
                'mobile' => '5511955555555',
                'gender' => 'male',
                'status' => 1,
                'is_subscribe' => 0,
            ]
        );

        // Grupo 6: Usuários com desconto aplicado
        $discountUser = User::firstOrCreate(
            ['email' => 'discount@example.com'],
            [
                'first_name' => 'Sofia',
                'last_name' => 'Desconto',
                'username' => 'sofia_discount',
                'password' => Hash::make('password123'),
                'mobile' => '5511944444444',
                'gender' => 'female',
                'status' => 1,
                'is_subscribe' => 1,
            ]
        );

        Subscription::firstOrCreate(
            ['user_id' => $discountUser->id, 'status' => 'active'],
            [
                'plan_id' => $plans['pro']->id,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(20),
                'status' => 'active',
                'amount' => 29.99,
                'discount_percentage' => 20, // 20% de desconto
                'tax_amount' => 4.80,
                'total_amount' => 28.79, // 29.99 * 0.80 + 4.80
                'type' => 'monthly',
                'duration' => 30,
                'identifier' => 'SUB-DISCOUNT-001',
            ]
        );

        // Grupo 7: Criação de múltiplos usuários aleatórios com assinaturas
        $this->command->info("Criando 10 usuários adicionais com assinaturas variadas...");

        for ($i = 1; $i <= 10; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'first_name' => "Usuário",
                    'last_name' => "Teste {$i}",
                    'username' => "user_test_{$i}",
                    'password' => Hash::make('password123'),
                    'mobile' => '55119' . str_pad($i, 8, '0', STR_PAD_LEFT),
                    'gender' => $i % 2 === 0 ? 'female' : 'male',
                    'status' => 1,
                    'is_subscribe' => rand(0, 1),
                ]
            );

            // Alterna entre planos
            $planKey = ['basic', 'premium', 'pro'][rand(0, 2)];
            $status = ['active', 'expired', 'cancelled'][rand(0, 2)];
            $startDate = Carbon::now()->subDays(rand(5, 90));

            Subscription::firstOrCreate(
                ['user_id' => $user->id, 'identifier' => "SUB-TEST-{$i}"],
                [
                    'plan_id' => $plans[$planKey]->id,
                    'start_date' => $startDate,
                    'end_date' => $status === 'cancelled' 
                        ? $startDate->addDays(30)
                        : ($status === 'expired' 
                            ? Carbon::now()->subDays(rand(1, 30))
                            : Carbon::now()->addDays(rand(5, 30))
                        ),
                    'status' => $status,
                    'amount' => $plans[$planKey]->price,
                    'discount_percentage' => rand(0, 1) ? rand(5, 20) : 0,
                    'tax_amount' => round($plans[$planKey]->price * 0.175, 2),
                    'total_amount' => round($plans[$planKey]->price + ($plans[$planKey]->price * 0.175), 2),
                    'type' => 'monthly',
                    'duration' => 30,
                ]
            );
        }

        $this->command->info("✓ Usuários com assinaturas criados com sucesso!");
    }
}
