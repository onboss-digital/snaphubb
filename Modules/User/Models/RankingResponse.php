<?php


namespace Modules\User\Models;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class RankingResponse extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'ranking_id',
        'response_date',
        'sugestion_name',
        'sugestion_link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
