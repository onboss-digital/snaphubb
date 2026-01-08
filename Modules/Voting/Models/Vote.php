<?php

namespace Modules\Voting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'cast_crew_id',
        'week_id',
        'vote_count'
    ];

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Relacionamento com CastCrew
     */
    public function castCrew()
    {
        return $this->belongsTo('Modules\CastCrew\Models\CastCrew', 'cast_crew_id');
    }

    /**
     * Get week identifier in YYYY-WW format
     */
    public static function getCurrentWeekId()
    {
        return now()->format('Y-W');
    }

    /**
     * Scope para votações da semana atual
     */
    public function scopeCurrentWeek($query)
    {
        return $query->where('week_id', self::getCurrentWeekId());
    }

    /**
     * Scope para votações de um usuário específico
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
