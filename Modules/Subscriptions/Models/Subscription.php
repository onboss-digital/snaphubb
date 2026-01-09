<?php

namespace Modules\Subscriptions\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Subscriptions\Models\SubscriptionTransactions;
use Jenssegers\Agent\Agent;


class Subscription extends BaseModel
{
    use HasFactory;

    protected $fillable = ['plan_id',
        'user_id',
        'device_id',
        'start_date',
        'end_date',
        'status',
        'amount',
        'discount_percentage',
        'tax_amount',
        'total_amount',
        'name',
        'identifier',
        'type',
        'duration',
        'level',
        'plan_type',
        'payment_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subscription_transaction()
    {
        return $this->hasOne(SubscriptionTransactions::class, 'subscriptions_id', 'id');
    }

    public function plan()
    {
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }

    protected static function newFactory()
    {
        return \Modules\Subscriptions\Database\factories\SubscriptionFactory::new();
    }
    public static function checkPlanSupportDevice($user_id){
        // Removed device type restrictions - all devices are supported
        // Only check for authentication status
        return response()->json(['isDeviceSupported' => true, 'device_name' => 'any']);
    }

    public static function checkSimultaneousDeviceAccess($user_id, $current_device_id = null) {
        // Check how many devices are currently active for this user
        // Get all active sessions for this user
        $activeSessions = \DB::table('sessions')
            ->where('user_id', $user_id)
            ->where('last_activity', '>', now()->subMinutes(30)->timestamp)
            ->get();

        // If there's more than 1 active session, deny access
        if ($activeSessions->count() > 1) {
            return [
                'allowed' => false,
                'message' => 'Sua conta está sendo acessada em outro dispositivo. Faça logout em outros dispositivos para continuar.',
                'active_devices' => $activeSessions->count()
            ];
        }

        return [
            'allowed' => true,
            'message' => 'Acesso permitido',
            'active_devices' => $activeSessions->count()
        ];
    }
}
