<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Modules\Subscriptions\Models\Subscription;

class SubscriptionExpiredModal extends Component
{
    public $class;

    public function __construct($class = '')
    {
        $this->class = $class;
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $user = Auth::user();

        // Return empty if user is not logged in
        if (!$user) {
            return '';
        }

        // Get the most recent subscription for the user (including expired ones)
        $userId = $user->id ?? null;
        if (!$userId) {
            return '';
        }

        $subscription = Subscription::where('user_id', $userId)
            ->orderBy('end_date', 'desc')
            ->first();

        // Debugging line, remove in production       
        // Return empty if user has no subscription history
        if (!$subscription) {
            return '';
        }

        $currentDate = Carbon::now();
        $endDate = Carbon::parse($subscription->end_date);

        // Only show modal if subscription is expired (end date is in the past)
        if ($currentDate->lte($endDate) && $subscription->status === config('constant.SUBSCRIPTION_STATUS.ACTIVE')) {
            return '';
        }

        // Hide modal if current route is subscription plan 
        if (Route::currentRouteName() == 'subscriptionPlan') {
            return '';
        }

        $data = [
            'subscription_name' => $subscription->name ?? 'N/A',
            'end_date' => $endDate->format('d/m/Y'),
            'days_expired' => $currentDate->diffInDays($endDate),
            'subscription_status' => $subscription->status,
        ];

        return view('frontend::components.partials.modals.subscription-expired-modal', compact('data'))->with('class', $this->class);
    }
}
