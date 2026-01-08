<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\User\Models\Ranking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Modules\User\Models\RankingResponse;

class RankingModal extends Component
{
    public $class;

    public function __construct($class = '')
    {
        $this->class = $class;
    }

    /**
     * Get the view / contents that represents the component.
     * This modal is now just a CTA to encourage users to vote
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $user = Auth::user();

        if (!$user) {
            return '';
        }

        // Check if user has active subscription
        if ($user->subscriptionPackage == null) {
            return '';
        }

        $plan = $user->subscriptionPackage->where('status', 'active')->first();
        if (!$plan) {
            return '';
        }

        $currentDate = now()->toDateString();
        
        // Find active ranking for this plan
        $ranking = Ranking::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->where('status', '1')
            ->whereHas('plans', function ($query) use ($plan) {
                $query->where('plan_id', $plan->plan_id);
            })
            ->first();

        if (!$ranking) {
            return '';
        }

        // Check if user has already reached the 3 vote limit for this ranking period
        $voteCount = RankingResponse::where('user_id', $user->id)
            ->where('ranking_id', $ranking->id)
            ->whereNotNull('content_slug')
            ->where('content_slug', '!=', '')
            ->count();

        // If user has voted 3 times or more, don't show the modal
        if ($voteCount >= 3) {
            return '';
        }

        // If user hasn't voted yet, show the CTA modal
        $data = [
            'ranking_id' => $ranking->id,
            'ranking_name' => $ranking->name,
            'votes_remaining' => 3 - $voteCount,
            'total_votes_allowed' => 3,
            'current_votes' => $voteCount,
        ];

        return view('frontend::components.partials.modals.ranking-cta-modal', compact('data'))->with('class', $this->class);
    }
}
