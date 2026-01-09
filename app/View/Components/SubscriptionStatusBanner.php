<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Modules\Subscriptions\Models\Subscription;

class SubscriptionStatusBanner extends Component
{
    public $status;
    public $daysRemaining;
    public $endDate;
    public $subscription;

    public function __construct()
    {
        $user = Auth::user();

        // Return empty if user is not logged in
        if (!$user) {
            $this->status = null;
            return;
        }

        // Hide on subscription plan page
        if (Route::currentRouteName() == 'subscriptionPlan') {
            $this->status = null;
            return;
        }

        // Get the most recent subscription for the user
        $subscription = Subscription::where('user_id', $user->id)
            ->orderBy('end_date', 'desc')
            ->first();

        if (!$subscription) {
            $this->status = null;
            return;
        }

        $this->subscription = $subscription;
        $currentDate = Carbon::now();
        $endDate = Carbon::parse($subscription->end_date);
        $this->endDate = $endDate->format('d/m/Y');
        
        // Only show banner for active or expired subscriptions (not cancelled)
        if ($subscription->status !== config('constant.SUBSCRIPTION_STATUS.ACTIVE') && 
            $subscription->status !== config('constant.SUBSCRIPTION_STATUS.INACTIVE')) {
            $this->status = null;
            return;
        }

        $daysRemaining = $endDate->diffInDays($currentDate, false); // false = signed difference

        // Only show banner for warnings: 1-7 days before expiration
        // Don't show for active (>7 days) or expired (<=0 days) - modal handles expired
        
        if ($daysRemaining <= 0 && $daysRemaining > -1) {
            // Last day (expires today or tomorrow)
            $this->status = '1_day';
            $this->daysRemaining = 1;
        } elseif ($daysRemaining <= -1 && $daysRemaining > -3) {
            // 2-3 days remaining
            $this->status = '3_days';
            $this->daysRemaining = abs($daysRemaining);
        } elseif ($daysRemaining <= -3 && $daysRemaining > -7) {
            // 4-7 days remaining
            $this->status = '7_days';
            $this->daysRemaining = abs($daysRemaining);
        } else {
            // Don't show banner for:
            // - Active subscriptions (>7 days remaining)
            // - Expired subscriptions (<=0 days) - modal antigo handles this
            $this->status = null;
        }
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        if (!$this->status) {
            return '';
        }

        return view('frontend::components.partials.subscription-status-banner');
    }
}
