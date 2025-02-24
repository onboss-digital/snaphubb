<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\User\Models\Ranking;
use Illuminate\Support\Facades\Auth;

class RankingModal extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $user = Auth::user();

        if(!$user) {
            return '';
        }
        $plan = $user->subscriptionPackage->where('status', 'active')->first();
        $currentDate = now()->toDateString();
        $ranking = Ranking::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->whereHas('plans', function ($query) use ($plan) {
                $query->where('plan_id', $plan->plan_id);
            })
            ->first();

        if(!$ranking) {
            return '';
        }
        $data = [
            'description' => $ranking->description ?? 'No description available',
            'title' => $ranking->name ?? 'No title available',
            'contents' => (array)json_decode($ranking->contents) ?? [],
        ];

        return view('frontend::components.partials.modals.ranking-modal', compact('data'));
    }
}