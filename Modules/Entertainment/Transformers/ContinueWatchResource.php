<?php

namespace Modules\Entertainment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Genres\Transformers\GenresResource;
use Modules\Subscriptions\Transformers\PlanResource;
use Modules\Subscriptions\Models\Plan;

class ContinueWatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        $entertainment = null;
        $plans = [];
        if($this->entertainment_type == 'movie'){
            $entertainment = $this->entertainment;
        }
        else if($this->entertainment_type == 'episode'){
            $entertainment = $this->episode;
        }
        else if($this->entertainment_type == 'video'){
            $entertainment = $this->video;
        }

        if($entertainment !== null && $entertainment->plan){
            $plans = Plan::where('level', '<=', $entertainment->plan->level)->get() ?? null;
        }

        return [
            'id' => $this->id,
            'entertainment_id' => $this->entertainment_id,
            'user_id' => $this->user_id,
            'entertainment_type' => $this->entertainment_type,
            'watched_time' => $this->watched_time ?? '00:00:01',
            'total_watched_time' => $this->total_watched_time ?? '00:00:01',
            'episode_id' => $entertainment->id ?? null,
            'name' => $entertainment->name ?? null,
            'description' => $entertainment->description ?? null,
            'trailer_url_type' => $entertainment->trailer_url_type ??null ,
         'trailer_url' => isset($entertainment) && $entertainment->trailer_url_type == 'Local'
    ? setBaseUrlWithFileName($entertainment->trailer_url)
    : ($entertainment->trailer_url ?? null),
            'plan_id' => $entertainment->plan_id ?? null,
            'language' => $entertainment->language ?? null,
            'imdb_rating' => $entertainment->IMDb_rating ?? null,
            'content_rating' => $entertainment->content_rating ?? null,
            'duration' => $entertainment->duration ?? null,
            'release_date' => $entertainment->release_date ?? null,
            'is_restricted' => $entertainment->is_restricted ?? null,
            'video_upload_type' => $entertainment->video_upload_type ?? null,
            'video_url_input' => isset($entertainment) && $entertainment->video_upload_type == 'Local'  ? setBaseUrlWithFileName($entertainment->video_url_input) : ($entertainment->video_url_input ?? null),
            'download_status' => $entertainment->download_status ?? null,
            'enable_quality' => $entertainment->enable_quality ?? null,
            'download_url' => $entertainment->download_url ?? null,
            'poster_image' => !empty($entertainment->tmdb_id) ? $entertainment->poster_url : setBaseUrlWithFileName($entertainment->poster_url ?? null ),
            'thumbnail_image' => !empty($entertainment->tmdb_id) ? $entertainment->thumbnail_url : setBaseUrlWithFileName($entertainment->thumbnail_url ?? null),
            'plans' => $plans ? PlanResource::collection($plans) : [],
            'status' => $entertainment->status ?? null,
        ];
    }
}
