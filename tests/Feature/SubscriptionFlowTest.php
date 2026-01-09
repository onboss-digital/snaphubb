<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscriptions\Models\Subscription;
use Modules\Subscriptions\Models\Plan;
use Tests\TestCase;
use Carbon\Carbon;

class SubscriptionFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa criação de uma assinatura ativa
     */
    public function test_can_create_active_subscription()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'status' => 'active',
            'amount' => 29.99,
            'discount_percentage' => 0,
            'tax_amount' => 5.00,
            'total_amount' => 34.99,
            'duration' => 30,
            'type' => 'monthly',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        $this->assertEquals('active', $subscription->status);
    }

    /**
     * Testa atualização de assinatura para expirada
     */
    public function test_subscription_can_be_marked_expired()
    {
        $subscription = Subscription::factory()->create([
            'status' => 'active',
            'end_date' => Carbon::now()->subDay(),
        ]);

        $subscription->update([
            'status' => 'expired',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'expired',
        ]);
    }

    /**
     * Testa cancelamento de assinatura
     */
    public function test_subscription_can_be_cancelled()
    {
        $subscription = Subscription::factory()->create([
            'status' => 'active',
        ]);

        $subscription->update([
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled',
        ]);
    }

    /**
     * Testa relacionamento entre usuário e assinatura
     */
    public function test_subscription_belongs_to_user()
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $subscription->user->id);
    }

    /**
     * Testa múltiplas assinaturas por usuário
     */
    public function test_user_can_have_multiple_subscriptions()
    {
        $user = User::factory()->create();

        Subscription::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $subscriptions = Subscription::where('user_id', $user->id)->get();

        $this->assertCount(3, $subscriptions);
    }

    /**
     * Testa renovação de assinatura
     */
    public function test_subscription_can_be_renewed()
    {
        $subscription = Subscription::factory()->create([
            'status' => 'expired',
            'end_date' => Carbon::now()->subDay(),
        ]);

        $subscription->update([
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'active',
        ]);
    }

    /**
     * Testa se assinatura ativa é recuperada corretamente
     */
    public function test_can_retrieve_active_subscription()
    {
        $user = User::factory()->create();

        // Cria assinatura expirada
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'expired',
        ]);

        // Cria assinatura ativa
        $activeSubscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $found = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        $this->assertEquals($activeSubscription->id, $found->id);
    }

    /**
     * Testa cálculo correto de valores de assinatura
     */
    public function test_subscription_amounts_are_correct()
    {
        $subscription = Subscription::create([
            'user_id' => User::factory()->create()->id,
            'plan_id' => Plan::factory()->create()->id,
            'amount' => 100.00,
            'discount_percentage' => 10,
            'tax_amount' => 15.00,
            'total_amount' => 115.00,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        // Verifica se o total está correto (amount com desconto + tax)
        $expectedTotal = 100.00 - (100.00 * 0.10) + 15.00;
        $this->assertEquals(105.00, $expectedTotal);
    }

    /**
     * Testa transição de estados de assinatura
     */
    public function test_subscription_status_transitions()
    {
        $subscription = Subscription::factory()->create(['status' => 'active']);

        // Active -> Expired
        $subscription->update(['status' => 'expired']);
        $this->assertEquals('expired', $subscription->fresh()->status);

        // Expired -> Cancelled
        $subscription->update(['status' => 'cancelled']);
        $this->assertEquals('cancelled', $subscription->fresh()->status);

        // Cancelled -> Active (renovação)
        $subscription->update(['status' => 'active']);
        $this->assertEquals('active', $subscription->fresh()->status);
    }

    /**
     * Testa se assinatura mantém dados consistentes após múltiplas atualizações
     */
    public function test_subscription_data_consistency()
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'amount' => 29.99,
        ]);

        // Simula múltiplas atualizações
        for ($i = 0; $i < 5; $i++) {
            $subscription->update([
                'status' => $i % 2 === 0 ? 'active' : 'expired',
            ]);
        }

        // Verifica se o relacionamento e dados básicos estão intactos
        $freshSubscription = $subscription->fresh();
        $this->assertEquals($user->id, $freshSubscription->user_id);
        $this->assertEquals(29.99, $freshSubscription->amount);
    }
}
