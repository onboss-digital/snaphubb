<?php

namespace Modules\Subscriptions\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\PlanLimitation;
use Modules\Subscriptions\Models\PlanLimitationMapping;

class AssignDefaultLimitationsToPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all plans
        $plans = Plan::all();
        $limitations = PlanLimitation::where('status', 1)->get();

        foreach ($plans as $plan) {
            // For each plan, create mappings for all limitations
            foreach ($limitations as $limitation) {
                PlanLimitationMapping::updateOrCreate(
                    [
                        'plan_id' => $plan->id,
                        'planlimitation_id' => $limitation->id,
                    ],
                    [
                        'limitation_slug' => $limitation->slug,
                        'limit' => 1, // Default to enabled
                    ]
                );
            }

            echo "Assigned " . count($limitations) . " limitations to plan: {$plan->name}\n";
        }

        echo "✓ All plans now have default limitations assigned!\n";
    }
}
