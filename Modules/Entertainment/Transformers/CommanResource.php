<?php

namespace Modules\Entertainment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CommanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'movie_access' => in_array($this->type, ['movie', 'tvshow']) ? $this->movie_access : $this->access,
            'plan_id' => $this->plan_id,
            'plan_level' => $this->plan->level ?? 0,
            'trailer_url_type' => $this->trailer_url_type,
            'trailer_url' => $this->trailer_url_type=='Local' ? setBaseUrlWithFileName($this->trailer_url) : $this->trailer_url,
            'video_upload_type' => $this->video_upload_type,
            'video_url_input' => $this->video_upload_type=='Local' ? setBaseUrlWithFileName($this->video_url_input) : $this->video_url_input,
            'poster_image' => setBaseUrlWithFileName($this->poster_url),
            'thumbnail_image' => setBaseUrlWithFileName($this->thumbnail_url),
        ];
    }
}
