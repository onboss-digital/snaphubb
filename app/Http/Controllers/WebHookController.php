<?php

namespace App\Http\Controllers;

use App\Jobs\BulkNotification;
use App\Mail\SubscriptionDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Models\User;
use Modules\Frontend\Http\Controllers\PaymentController;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\Subscription;
use Modules\Subscriptions\Models\SubscriptionTransactions;
use Modules\Subscriptions\Transformers\PlanlimitationMappingResource;
use Modules\Subscriptions\Transformers\SubscriptionResource;
use Modules\Tax\Models\Tax;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;


class WebHookController extends Controller
{
    private function logData($data)
    {
        $logDir = storage_path('logs/cartpanda');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/cartpanda_' . date('Ymd_His') . '.log';
        $log = json_encode($data) . PHP_EOL;
        file_put_contents($logFile, $log);
        return $logFile;
    }
    public function get_plan_expiration_date($plan_start_date = '', $plan_type = '', $plan_duration = 1)
    {
        $start_at = new \Carbon\Carbon($plan_start_date);
        $end_date = '';

        if ($plan_type === 'month') {

            $end_date = $start_at->addMonths($plan_duration);
        }
        if ($plan_type == 'year') {
            $end_date = $start_at->addYears($plan_duration);
        }
        if ($plan_type == 'week') {
            $end_date = $start_at->addWeeks($plan_duration);
        }

        return $end_date->format('Y-m-d H:i:s');
    }


    protected function handleSubscrible($plan_id, $amount, $payment_type, $transaction_id, $user)
    {
        $plan = Plan::findOrFail($plan_id);
        $limitation_data = PlanlimitationMappingResource::collection($plan->planLimitation);

        $end_date = $this->get_plan_expiration_date(now(), $plan->duration, $plan->duration_value);
        $taxes = Tax::active()->get();
        $totalTax = 0;
        foreach ($taxes as $tax) {
            if (strtolower($tax->type) == 'fixed') {
                $totalTax += $tax->value;
            } elseif (strtolower($tax->type) == 'percentage') {
                $totalTax += ($plan->price * $tax->value) / 100;
            }
        }
        // Create the subscription
        $subscription = Subscription::create([
            'plan_id' => $plan_id,
            'user_id' => $user->id,
            'device_id' => 1,
            'start_date' => now(),
            'end_date' => $end_date,
            'status' => 'active',
            'amount' => $plan->price,
            'discount_percentage' => $plan->discount_percentage,
            'tax_amount' => $totalTax,
            'total_amount' => $amount,
            'name' => $plan->name,
            'identifier' => $plan->identifier,
            'type' => $plan->duration,
            'duration' => $plan->duration_value,
            'level' => $plan->level,
            'plan_type' => $limitation_data ? json_encode($limitation_data) : null,
            'payment_id' => null,
        ]);

        // Create a subscription transaction
        SubscriptionTransactions::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_type' => $payment_type,
            'payment_status' => 'paid',
            'tax_data' => $taxes->isEmpty() ? null : json_encode($taxes),
            'transaction_id' => $transaction_id,
            'subscriptions_id' => $subscription->id,
        ]);

        $user->update(['is_subscribe' => 1]);

        return $user;
    }

    public function cartpanda(Request $request, $logFile = false)
    {
        $data = $request->all();
        $logFile = $logFile == false ? $this->logData($data) : $logFile;

        // $logFile = storage_path('logs/cartpanda/success') . '/cartpanda_20250204_100410.log';
        try {
            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                $logData = json_decode(trim($logContent), true);

                if ($logData == null) {
                    $exploded = explode(' {', trim($logContent));
                    if (strtotime($exploded[0]) !== false) {
                        unset($exploded[0]);
                        $logContent = '{' . implode(' {', $exploded);
                    }
                    $logContent = str_replace('}{', '},{', $logContent);
                    $logData = json_decode(trim($logContent), true);
                }

                if ($logData == null) {
                    return response()->json(['status' => 'error']);
                }
                app()->setLocale(env('APP_LOCALE', 'es'));
                switch ($logData['event']) {
                    case 'order.paid':
                        $user = User::firstOrCreate(['email' => $logData['order']['customer']['email']], [
                            'first_name' => $logData['order']['customer']['first_name'],
                            'last_name' => $logData['order']['customer']['last_name'],
                            'email' => $logData['order']['customer']['email'],
                            'password' => bcrypt('P@55w0rd'),
                            'user_type' => 'user',
                        ]);

                        $plan = Plan::where('cartpanda_product_id', $logData['order']['line_items'][0]['product_id'])->first();

                        if (!$plan) {
                            $plan = Plan::orderBy('price', 'asc')->first();
                        }

                        $this->handleSubscrible($plan->id, $plan->price, 'cartpanda', $logData['order']['id'], $user);

                        $user->password_decrypted = 'P@55w0rd';

                        event(new Registered($user));
                        break;
                    case 'order.failed':
                        break;
                    case 'order.cancelled':
                        break;
                    default:
                        return response()->json(['status' => 'error']);
                }

                if (file_exists($logFile)) {
                    $successDir = storage_path('logs/cartpanda/success');
                    if (!is_dir($successDir)) {
                        mkdir($successDir, 0755, true);
                    }
                    rename($logFile, $successDir . '/' . basename($logFile));
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            $adminEmail = Config::get('mail.admin_email');
            Mail::raw('An error occurred: ' . $e->getMessage(), function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('Error Notification');
            });

            if (file_exists($logFile)) {
                $failDir = storage_path('logs/cartpanda/fail');
                if (!is_dir($failDir)) {
                    mkdir($failDir, 0755, true);
                }
                rename($logFile, $failDir . '/' . basename($logFile));
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function executeWebhookLogs($webhook, Request $request)
    {
        $logDir = storage_path("logs/{$webhook}");
        $logFiles = glob($logDir . '/*.log');

        foreach ($logFiles as $logFile) {
            $request = new Request();
            $this->cartpanda($request, $logFile);
        }

        return response()->json(['status' => 'success']);
    }

    public function triboPay(Request $request, $logFile = false)
    {
        $data = $request->all();
        $logFile = $logFile == false ? $this->logData($data) : $logFile;

        return response()->json(['status' => 'success']);
    }
}
