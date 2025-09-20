<?php

namespace Modules\Subscriptions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderBump extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_bumps';

    protected $fillable = [
        'external_id',
        'title',
        'text_button',
        'description',
        'plan_id',
    ];

    public function plan()
    {
        return $this->belongsTo(\Modules\Subscriptions\Models\Plan::class, 'plan_id');
    }
}
