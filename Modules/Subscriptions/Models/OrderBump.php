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
        'title_en',
        'text_button_en',
        'description_en',
        'title_es',
        'text_button_es',
        'description_es',
        'plan_id',
        'original_price',
        'discount_percentage',
        'icon',
        'badge',
        'badge_color',
        'social_proof_count',
        'urgency_text',
        'recommended',
    ];

    public function plan()
    {
        return $this->belongsTo(\Modules\Subscriptions\Models\Plan::class, 'plan_id');
    }
}
