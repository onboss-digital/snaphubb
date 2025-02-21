<?php

namespace Modules\User\Models;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Entertainment\Models\EntertainmentGenerMapping;
use Modules\Subscriptions\Models\Plan;

class Ranking extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'rankings';

    protected $fillable = [
        'name',
        'slug',
        'file_url',
        'description',
        'status',
        'contents',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'contents' => 'array',
    ];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = slug_format(trim($value));

        if (empty($value)) {
            $this->attributes['slug'] = slug_format(trim($this->attributes['name']));
        }
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'ranking_plan', 'ranking_id', 'plan_id');
    }

    protected static function boot()
    {
        parent::boot();

    }

    
}