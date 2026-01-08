<?php

namespace Modules\Voting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyRanking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'weekly_rankings';

    protected $fillable = [
        'week_id',
        'cast_crew_id',
        'rank_position',
        'total_votes',
        'percentage',
        'week_start',
        'week_end'
    ];

    /**
     * Relacionamento com CastCrew
     */
    public function castCrew()
    {
        return $this->belongsTo('Modules\CastCrew\Models\CastCrew', 'cast_crew_id');
    }

    /**
     * Scope para ranking da semana atual
     */
    public function scopeCurrentWeek($query)
    {
        return $query->where('week_id', Vote::getCurrentWeekId());
    }

    /**
     * Scope para top 3
     */
    public function scopeTop3($query)
    {
        return $query->whereIn('rank_position', [1, 2, 3])
            ->orderBy('rank_position', 'asc');
    }

    /**
     * Get week identifier in YYYY-WW format
     */
    public static function getCurrentWeekId()
    {
        return now()->format('Y-W');
    }
}
