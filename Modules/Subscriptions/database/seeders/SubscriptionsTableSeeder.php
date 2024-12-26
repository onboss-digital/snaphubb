<?php

namespace Modules\Subscriptions\database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubscriptionsTableSeeder extends Seeder
{

    /**
     *
     * @return void
     */
    public function run()
    {
        \DB::table('subscriptions')->delete();

        $subscriptions = [
            [
                'plan_id' => 2,
                'user_id' => 14,
                'start_date' => Carbon::today()->subDays(rand(25, 30)),
                'status' => 'active',
                'amount' => 20,
                'total_amount' => 20,
                'tax_amount' => 0.0,
                'name' => 'Premium Plan',
                'identifier' => 'premium_plan',
                'type' => 'month',
                'duration' => 1,
                'level' => 2,
                'plan_type' => '[]',
                'payment_id' => 8,
                'device_id' => '5',

            ],
            [
                'plan_id' => 2,
                'user_id' => 5,
                'start_date' => Carbon::today()->subMonths(3)->addDays(rand(1, 7)),
                'status' => 'active',
                'amount' => 20,
                'total_amount' => 20,
                'tax_amount' => 0.0,
                'name' => 'Premium Plan',
                'identifier' => 'premium_plan',
                'type' => 'month',
                'duration' => 1,
                'level' => 2,
                'plan_type' => '[]',
                'payment_id' => 3,
                'device_id' => '3',
            ],
            [
                'plan_id' => 3,
                'user_id' => 3,
                'start_date' => Carbon::today()->subDays(rand(25, 30)),
                'status' => 'active',
                'amount' => 50,
                'total_amount' => 50,
                'tax_amount' => 0.0,
                'name' => 'Ultimate Plan',
                'identifier' => 'ultimate_plan',
                'type' => 'month',
                'duration' => 3,
                'level' => 3,
                'plan_type' => '[]',
                'payment_id' => 1,
                'device_id' => 'test11',
            ],
            [
                'plan_id' => 3,
                'user_id' => 6,
                'start_date' => Carbon::today()->subMonths(4)->addDays(rand(1, 7)),
                'status' => 'active',
                'amount' => 50,
                'total_amount' => 50,
                'tax_amount' => 0.0,
                'name' => 'Ultimate Plan',
                'identifier' => 'ultimate_plan',
                'type' => 'month',
                'duration' => 3,
                'level' => 3,
                'plan_type' => '[]',
                'payment_id' => 4,
                'device_id' => '3',
            ],
            [
                'plan_id' => 1,
                'user_id' => 4,
                'start_date' => Carbon::today()->addDays(rand(1, 30)),
                'status' => 'active',
                'amount' => 5,
                'total_amount' => 5,
                'tax_amount' => 0.0,
                'name' => 'Basic',
                'identifier' => 'basic',
                'type' => 'week',
                'duration' => 1,
                'level' => 1,
                'plan_type' => '[]',
                'payment_id' => 2,
                'device_id' => 'test11',
            ],
            [
                'plan_id' => 1,
                'user_id' => 10,
                'start_date' => Carbon::today()->subDays(rand(25, 30)),
                'status' => 'active',
                'amount' => 5,
                'total_amount' => 5,
                'tax_amount' => 0.0,
                'name' => 'Basic',
                'identifier' => 'basic',
                'type' => 'week',
                'duration' => 1,
                'level' => 1,
                'plan_type' => '[]',
                'payment_id' => 7,
                'device_id' => '4',
            ],
            [
                'plan_id' => 4,
                'user_id' => 8,
                'start_date' => Carbon::today()->subYears(1)->addDays(rand(1, 7)),
                'status' => 'active',
                'amount' => 80,
                'total_amount' => 80,
                'tax_amount' => 0.0,
                'name' => 'Elite Plan',
                'identifier' => 'elite_plan',
                'type' => 'year',
                'duration' => 1,
                'level' => 4,
                'plan_type' => '[]',
                'payment_id' => 5,
                'device_id' => '4',
            ],
            [
                'plan_id' => 4,
                'user_id' => 9,
                'start_date' => Carbon::today()->subMonths(1)->addDays(rand(1, 30)),
                'status' => 'active',
                'amount' => 80,
                'total_amount' => 80,
                'tax_amount' => 0.0,
                'name' => 'Elite Plan',
                'identifier' => 'elite_plan',
                'type' => 'year',
                'duration' => 1,
                'level' => 4,
                'plan_type' => '[]',
                'payment_id' => 6,
                'device_id' => '4',
            ],

        ];

        foreach ($subscriptions as &$subscription) {
            $start_date = Carbon::parse($subscription['start_date']);
            $duration = $subscription['duration'];
            $type = $subscription['type'];

            switch ($type) {
                case 'day':
                    $subscription['end_date'] = $start_date->addDays($duration);
                    break;
                case 'week':
                    $subscription['end_date'] = $start_date->addWeeks($duration);
                    break;
                case 'month':
                    $subscription['end_date'] = $start_date->addMonths($duration);
                    break;
                case 'year':
                    $subscription['end_date'] = $start_date->addYears($duration);
                    break;
                default:
                    break;
            }

            if ($subscription['end_date']->lt(Carbon::today())) {
                $subscription['status'] = 'inactive';
            }
        }

        \DB::table('subscriptions')->insert($subscriptions);
    }
}

