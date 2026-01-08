<?php

namespace Database\Seeders;

use App\Models\User;
use Modules\Subscriptions\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestSubscriptionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates test subscriptions with different statuses
     * to help test the subscription banner component.
     * 
     * Usage: php artisan db:seed --class=TestSubscriptionStatusSeeder
     */
    public function run(): void
    {
        $testUser = User::firstOrCreate(
            ['email' => 'test-subscription@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Subscription',
                'password' => bcrypt('password'),
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        // 1. Active Subscription (30 days remaining) - GREEN
        Subscription::create([
            'user_id' => $testUser->id,
            'plan_id' => 1,
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
            'start_date' => Carbon::now()->subDays(30),
            'end_date' => Carbon::now()->addDays(30),
            'price' => 99,
            'currency' => 'USD',
            'transaction_id' => 'TEST-ACTIVE-001',
            'payment_gateway' => 'stripe',
        ]);

        // 2. 7 Days Warning (5 days remaining) - YELLOW
        $user7Days = User::firstOrCreate(
            ['email' => 'test-7days@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => '7Days',
                'password' => bcrypt('password'),
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        Subscription::create([
            'user_id' => $user7Days->id,
            'plan_id' => 1,
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
            'start_date' => Carbon::now()->subDays(25),
            'end_date' => Carbon::now()->addDays(5),
            'price' => 99,
            'currency' => 'USD',
            'transaction_id' => 'TEST-7DAYS-001',
            'payment_gateway' => 'stripe',
        ]);

        // 3. 3 Days Warning (2 days remaining) - ORANGE
        $user3Days = User::firstOrCreate(
            ['email' => 'test-3days@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => '3Days',
                'password' => bcrypt('password'),
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        Subscription::create([
            'user_id' => $user3Days->id,
            'plan_id' => 1,
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
            'start_date' => Carbon::now()->subDays(28),
            'end_date' => Carbon::now()->addDays(2),
            'price' => 99,
            'currency' => 'USD',
            'transaction_id' => 'TEST-3DAYS-001',
            'payment_gateway' => 'stripe',
        ]);

        // 4. 1 Day Warning (12 hours remaining) - RED
        $user1Day = User::firstOrCreate(
            ['email' => 'test-1day@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => '1Day',
                'password' => bcrypt('password'),
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        Subscription::create([
            'user_id' => $user1Day->id,
            'plan_id' => 1,
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
            'start_date' => Carbon::now()->subDays(29),
            'end_date' => Carbon::now()->addHours(12),
            'price' => 99,
            'currency' => 'USD',
            'transaction_id' => 'TEST-1DAY-001',
            'payment_gateway' => 'stripe',
        ]);

        // 5. Expired Subscription (expired 5 days ago) - DARK RED
        $userExpired = User::firstOrCreate(
            ['email' => 'test-expired@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Expired',
                'password' => bcrypt('password'),
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        Subscription::create([
            'user_id' => $userExpired->id,
            'plan_id' => 1,
            'status' => config('constant.SUBSCRIPTION_STATUS.ACTIVE'),
            'start_date' => Carbon::now()->subDays(35),
            'end_date' => Carbon::now()->subDays(5),
            'price' => 99,
            'currency' => 'USD',
            'transaction_id' => 'TEST-EXPIRED-001',
            'payment_gateway' => 'stripe',
        ]);

        $this->command->info('Test subscription statuses created successfully!');
        $this->command->info('');
        $this->command->info('Test Users (all with password "password"):');
        $this->command->info('1. Active (30 days):   test-subscription@example.com');
        $this->command->info('2. 7 Days Warning:     test-7days@example.com');
        $this->command->info('3. 3 Days Warning:     test-3days@example.com');
        $this->command->info('4. 1 Day Warning:      test-1day@example.com');
        $this->command->info('5. Expired:            test-expired@example.com');
    }
}
