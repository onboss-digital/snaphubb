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
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {

        // dd('RankingModal render method called');
        $user = Auth::user();


        if (!$user) {
            return '';
        }
        if ($user->subscriptionPackage == null) {
            return '';
        }
        $plan = $user->subscriptionPackage->where('status', 'active')->first();

        if (!$plan) {
            return '';
        }


        $currentDate = now()->toDateString();
        $ranking = Ranking::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->where('status','=', '1')
            ->whereHas('plans', function ($query) use ($plan) {
                $query->where('plan_id', $plan->plan_id);
            })
            ->first();


        if (!$ranking) {
            return '';
        }

        // Check if the user has already responded to the ranking
        $responseExists = RankingResponse::where('user_id', $user->id)
            ->where('ranking_id', $ranking->id)
            ->exists();


        if ($responseExists) {
            return '';
        }

        $contents = json_decode($ranking->contents);

        $data = [
            'id' => $ranking->id,
            'description' => $ranking->description ?? 'No description available',
            'title' => $ranking->name ?? 'No title available',
            'contents' => $contents,
        ];

        return view('frontend::components.partials.modals.ranking-modal', compact('data'))->with('class', $this->class);
    }

    public function vote(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'ranking_id' => 'required',
            'content_slug' => 'required',
        ]);

        $plan = $user->subscriptionPackage->where('status', 'active')->first();
        $currentDate = now()->toDateString();

        try {
            if ($request->has('sugestion_name') && $request->has('sugestion_link') && $request->get('sugestion_name') != null && $request->get('sugestion_link') != null) {
                RankingResponse::create([
                    'user_id' => $user->id,
                    'ranking_id' => $data['ranking_id'],
                    'response_date' => $currentDate,
                    'sugestion_name' => $request->get('sugestion_name'),
                    'sugestion_link' => $request->get('sugestion_link'),
                ]);

                return response()->json(['message' => __('placeholder.lbl_ranking_modal_return_save_sugestion')]);
            }

            $ranking = Ranking::where('id', $data['ranking_id'])
                ->where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->whereHas('plans', function ($query) use ($plan) {
                    $query->where('plan_id', $plan->plan_id);
                })
                ->firstOrFail();

            $contents = collect(json_decode($ranking->contents));

            foreach ($contents as &$content) {
                if ($content->slug === $data['content_slug']) {
                    $content->votes = $content->votes + 1;
                }
            }

            $ranking->contents = $contents->toJson();
            $ranking->save();

            // Save the user's response
            RankingResponse::create([
                'user_id' => $user->id,
                'ranking_id' => $ranking->id,
                'response_date' => $currentDate,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => __('placeholder.lbl_ranking_modal_return_save_error')], 500);
        }

        return response()->json(['message' => __('placeholder.lbl_ranking_modal_return_save_vote')]);
    }
}
