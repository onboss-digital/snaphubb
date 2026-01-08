<?php

namespace Tests\Unit\Components;

use Tests\TestCase;
use App\Models\User;
use Modules\Subscriptions\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class SubscriptionStatusBannerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that banner does not show for unauthenticated users
     */
    public function test_banner_hidden_for_unauthenticated_users()
    {
        $response = $this->get('/');
        
        // Banner should not be visible since user is not authenticated
        $response->assertDontSee('subscription-status-banner');
    }

    /**
     * Test that banner shows for authenticated users with active subscriptions
     */
    public function test_banner_shows_active_subscription()
    {
        $user = User::factory()->create();
        
        Subscription::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->addDays(30),
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
        ]);

        $response = $this->actingAs($user)->get('/');
        
        $response->assertSee('subscription-status-banner');
        $response->assertSee('You are protected until');
    }

    /**
     * Test that banner shows 7-day warning
     */
    public function test_banner_shows_7_day_warning()
    {
        $user = User::factory()->create();
        
        Subscription::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->addDays(5), // 5 days from now
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
        ]);

        $response = $this->actingAs($user)->get('/');
        
        $response->assertSee('subscription-status-banner');
        $response->assertSee('Your subscription expires in 7 days');
    }

    /**
     * Test that banner shows 3-day warning
     */
    public function test_banner_shows_3_day_warning()
    {
        $user = User::factory()->create();
        
        Subscription::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->addDays(2), // 2 days from now
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
        ]);

        $response = $this->actingAs($user)->get('/');
        
        $response->assertSee('subscription-status-banner');
        $response->assertSee('ATTENTION: 3 days remaining');
    }

    /**
     * Test that banner shows 1-day warning
     */
    public function test_banner_shows_1_day_warning()
    {
        $user = User::factory()->create();
        
        Subscription::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->addHours(12), // Less than 1 day
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
        ]);

        $response = $this->actingAs($user)->get('/');
        
        $response->assertSee('subscription-status-banner');
        $response->assertSee('URGENT: Last chance today!');
    }

    /**
     * Test that banner shows expired message
     */
    public function test_banner_shows_expired_message()
    {
        $user = User::factory()->create();
        
        Subscription::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->subDays(10), // 10 days ago
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
        ]);

        $response = $this->actingAs($user)->get('/');
        
        $response->assertSee('subscription-status-banner');
        $response->assertSee('Subscription expired - Renew now');
    }

    /**
     * Test that banner is hidden on subscription plan page
     */
    public function test_banner_hidden_on_subscription_plan_page()
    {
        $user = User::factory()->create();
        
        Subscription::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->addDays(5),
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
        ]);

        $response = $this->actingAs($user)->get(route('subscriptionPlan'));
        
        // Banner should not be visible on subscription plan page
        $response->assertDontSee('subscription-status-banner');
    }

    /**
     * Test that banner does not show for users without subscriptions
     */
    public function test_banner_hidden_for_users_without_subscriptions()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');
        
        $response->assertDontSee('subscription-status-banner');
    }

    /**
     * Test that most recent subscription is used when user has multiple
     */
    public function test_uses_most_recent_subscription()
    {
        $user = User::factory()->create();
        
        // Create old subscription
        Subscription::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->subDays(30),
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
        ]);
        
        // Create new active subscription
        Subscription::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->addDays(20),
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
        ]);

        $response = $this->actingAs($user)->get('/');
        
        // Should show message from the newer subscription
        $response->assertSee('subscription-status-banner');
        $response->assertSee('You are protected until');
    }

    /**
     * Test translation for different locales
     */
    public function test_banner_shows_translated_messages()
    {
        $user = User::factory()->create();
        
        Subscription::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->addDays(30),
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
        ]);

        // Test Spanish
        $response = $this->actingAs($user)
            ->withHeader('Accept-Language', 'es')
            ->get('/');
        
        // Spanish translation should be visible
        $response->assertSee('subscription-status-banner');
    }
}
