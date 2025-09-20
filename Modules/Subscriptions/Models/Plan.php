<?php

namespace Modules\Subscriptions\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends BaseModel
{
    use SoftDeletes;

    protected $table = 'plan';

    protected $fillable = [
        'name',
        'identifier',
        'android_identifier',
        'apple_identifier',
        'level',
        'duration',
        'duration_value',
        'price',
        'description',
        'status',
        'discount',
        'discount_percentage',
        'total_price',
        'currency',
        'language',
        'custom_gateway',
        'external_product_id',
        'external_url',
        'pages_product_external_id',
        'pages_upsell_url',
        'pages_upsell_succes_url',
        'pages_downsell_url',
        'pages_upsell_fail_url',
    ];

    const CUSTOM_FIELD_MODEL = 'Modules\Subscriptions\Models\Plan';

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total_price' => 'decimal:2',
        'status' => 'boolean',
        'discount' => 'boolean',
    ];

    public function planLimitation()
    {
        return $this->hasMany(PlanLimitationMapping::class, 'plan_id', 'id')->with('limitation_data');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function orderBumps()
    {
        return $this->hasMany(OrderBump::class, 'plan_id');
    }
}
