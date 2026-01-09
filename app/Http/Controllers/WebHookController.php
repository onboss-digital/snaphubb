<?php

namespace App\Http\Controllers;

use App\Jobs\BulkNotification;
use App\Mail\ExpiringSubscriptionEmail;
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
use Illuminate\Support\Facades\DB;


class WebHookController extends Controller
{
    public function genericWebhookHandler($type)
    {
        $type = strtolower($type);
        $type = preg_replace('/[^a-z0-9_\-]/', '', $type);
        try {
            $data = request()->all();
            $this->logData($data, $type);
        } catch (\Exception $e) {
            Log::error('Error processing generic webhook: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['status' => 'success']);
    }

    private function logData($data, $type = 'cartpanda')
    {
        try {
            $logDir = storage_path("logs/{$type}");
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . "/{$type}" . date('Ymd_His') . '.log';
            $log = json_encode($data) . PHP_EOL;
            file_put_contents($logFile, $log);
            return $logFile;
        } catch (\Exception $e) {
        }
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


    protected function handleSubscrible($plan_id, $amount, $payment_type, $transaction_id, $user, $bumps = [])
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

        // NOVO: Registrar bumps adquiridos
        if (!empty($bumps)) {
            foreach ($bumps as $bump_id) {
                // Armazenar qual bump o usuário comprou
                DB::table('user_purchased_bumps')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'bump_id' => $bump_id,
                    ],
                    [
                        'plan_id' => $plan_id,
                        'purchased_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

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
        $logFile = $logFile == false ? $this->logData($data, 'tribopay') : $logFile;


        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $logData = json_decode(trim($logContent), true);
        }

        if (!isset($logData) || $logData == null) {
            $exploded = explode(' {', trim($logContent));
            if (strtotime($exploded[0]) !== false) {
                unset($exploded[0]);
                $logContent = '{' . implode(' {', $exploded);
            }
            $logContent = str_replace('}{', '},{', $logContent);
            $logData = json_decode(trim($logContent), true);
        }

        if (!isset($logData) || $logData == null) {
            $logData = $data;
        }


        app()->setLocale(env('APP_LOCALE', 'br'));


        try {
            // Novo formato TriboPay: event = 'transaction', status = 'paid'
            if (($logData['event'] ?? null) === 'transaction' && ($logData['status'] ?? null) === 'paid') {
                $customer = $logData['customer'] ?? [];
                $user = User::firstOrCreate([
                    'email' => $customer['email'] ?? null
                ], [
                    'first_name' => $customer['name'] ?? null,
                    'last_name' => null,
                    'email' => $customer['email'] ?? null,
                    'password' => bcrypt('P@55w0rd'),
                    'user_type' => 'user',
                ]);

                // Buscar plano pelo hash do offer, se existir, senão pelo menor preço
                $offer = $logData['offer'] ?? [];
                $product_hash = $offer['hash'] ?? null;
                $plan = null;

                if ($product_hash) {
                    $plan = Plan::where('custom_gateway', '=', 'TriboPay')
                        ->where('external_product_id', '=', $product_hash)
                        ->first();
                }

                if (!$plan) {
                    return response()->json(['status' => 'error', 'message' => 'Plan not found']);
                }

                $amount = $plan->total_price;
                $transaction_id = $logData['transaction']['id'] ?? null;

                $this->handleSubscrible($plan->id, $amount, 'tribopay', $transaction_id, $user);

                $user->password_decrypted = 'P@55w0rd';

                event(new Registered($user));
            } else if (($logData['event'] ?? null) === 'transaction' && ($logData['status'] ?? null) === 'failed') {
                // Lógica para transação falhada
            } else if (($logData['event'] ?? null) === 'transaction' && ($logData['status'] ?? null) === 'cancelled') {
                // Lógica para transação cancelada
            } else {
                return response()->json(['status' => 'error']);
            }

            if (file_exists($logFile)) {
                $successDir = storage_path('logs/tribopay/success');
                if (!is_dir($successDir)) {
                    mkdir($successDir, 0755, true);
                }
                rename($logFile, $successDir . '/' . basename($logFile));
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            $adminEmail = Config::get('mail.admin_email');
            Mail::raw('An error occurred: ' . $e->getMessage(), function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('Error Notification');
            });

            if (file_exists($logFile)) {
                $failDir = storage_path('logs/tribopay/fail');
                if (!is_dir($failDir)) {
                    mkdir($failDir, 0755, true);
                }
                rename($logFile, $failDir . '/' . basename($logFile));
            }
        }

        return response()->json(['status' => 'success']);
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
            }

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
                $logFile =  $data;
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

                    $plan = Plan::where('custom_gateway', 'CartPanda')
                        ->where('external_product_id', $logData['order']['line_items'][0]['product_id'])
                        ->first();

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

    public function stripe(Request $request, $logFile = false)
    {
        $data = $request->all();
        $logFile = $logFile == false ? $this->logData($data, 'stripe') : $logFile;


        try {
            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                $logData = json_decode(trim($logContent), true);
                $adminnEmail = Config::get('mail.admin_email');


                // Mail::raw($logContent, function ($message) use ($adminnEmail) {
                //     $message->to('teste@exemplo.com')->subject('Email de Teste');
                // });
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            $adminEmail = Config::get('mail.admin_email');
            Mail::raw('An error occurred: ' . $e->getMessage(), function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('Error Notification');
            });

            if (file_exists($logFile)) {
                $failDir = storage_path('logs/stripe/fail');
                if (!is_dir($failDir)) {
                    mkdir($failDir, 0755, true);
                }
                rename($logFile, $failDir . '/' . basename($logFile));
            }
        }

        if (file_exists($logFile)) {
            $successDir = storage_path('logs/stripe/success');
            if (!is_dir($successDir)) {
                mkdir($successDir, 0755, true);
            }
            rename($logFile, $successDir . '/' . basename($logFile));
        }

        return response()->json(['status' => 'success']);
    }

    public function stripepages(Request $request, $logFile = false)
    {
        $data = $request->all();
        $logFile = $logFile == false ? $this->logData($data, 'tribopay') : $logFile;
        $logData = [];

        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $logData = json_decode(trim($logContent), true);
        }

        if (!isset($logData) || $logData == null) {
            $exploded = explode(' {', trim($logContent));
            if (strtotime($exploded[0]) !== false) {
                unset($exploded[0]);
                $logContent = '{' . implode(' {', $exploded);
            }
            $logContent = str_replace('}{', '},{', $logContent);
            $logData = json_decode(trim($logContent), true);
        }

        if (!isset($logData) || $logData == null) {
            $logData = $data;
        }

        app()->setLocale(env('APP_LOCALE', 'br'));

        //                     return response()->json([
        //     'received_data' => $data,
        //     'raw_content' => $request->getContent(),
        //     'content_type' => $request->header('Content-Type'),
        //     'log_file' => $logFile,
        //     'request' => $request,
        //      'logData' => $logData
        // ]);
        
        try {

            if (($logData['object'] ?? null) === 'event' && ($logData['type'] ?? null) === 'invoice.payment_succeeded') {
                if (($logData['data']['object']['status'] ?? null) === 'paid') {

                    $productId = $logData['data']['object']['lines']['data'][0]['metadata']['product_id'] ?? null;
                    $email = $logData['data']['object']['customer_email'] ?? null;
                    $name = $logData['data']['object']['customer_name'] ?? null;
                    $plan = [];

                    if ($productId) {
                        $plan = Plan::where('pages_product_external_id', '=', $productId)
                            ->firstOrFail();
                    }
                    
                    if (!$plan) {
                        return response()->json(['status' => 'error', 'message' => 'Plan not found']);
                    }
                    
                    $user = User::firstOrCreate([
                        'email' => $email ?? null
                    ], [
                        'first_name' => $name ?? null,
                        'last_name' => null,
                        'email' => $email ?? null,
                        'password' => bcrypt('P@55w0rd'),
                        'user_type' => 'user',
                    ]);

                    $amount = $plan->total_price;
                    $transaction_id = $logData['data']['object']['parent']['subscription_details']['subscription'] ?? null;
                    
                    if ($user->wasRecentlyCreated) {
                        // Usuário FOI CRIADO AGORA (não existia)
                    } else {
                        // ja existia
                        $upsell = new UpsellController();
                        $customer = $upsell->request('get', '/customers' . '/' . $logData['data']['object']['customer']);                        
                        $userupdate = User::where('email', $customer['email'])->first();       
                        $planupdate = Plan::where('pages_product_external_id', $logData['data']['object']['items']['data'][0]['plan']['product'] ??  $logData['data']['object']['parent']['subscription_details']['metadata']['product_id'])->first();
                        $transaction = Subscription::where('user_id', $userupdate['id'])->where('plan_id', $planupdate['id'])->first();
                        $subtransaction = SubscriptionTransactions::where('transaction_id', $transaction_id)->first();;
                        if (!$transaction) {
                            $this->handleSubscrible($plan->id, $amount, 'stripepages', $transaction_id, $user);

                            $user->password_decrypted = 'P@55w0rd';

                            event(new Registered($user));
                        } else {
                            if ($subtransaction) {
                                $transaction->update([
                                    'status' => 'active',
                                ]);
                                $subtransaction->update([
                                    'payment_status' => $logData['data']['object']['status'],
                                ]);
                            }
                        }
                    }

                    $this->handleSubscrible($plan->id, $amount, 'stripepages', $transaction_id, $user);

                    $user->password_decrypted = 'P@55w0rd';

                    event(new Registered($user));
                }
            } else if (($logData['object'] ?? null) === 'event' && ($logData['type'] ?? null) === 'customer.subscription.deleted') {
                // Lógica para assinatura cancelada
                $upsell = new UpsellController();
                $customer = $upsell->request('get', '/customers' . '/' . $logData['data']['object']['customer']);
                $userupdate = User::where('email', $customer['email'])->first();
                $planupdate = Plan::where('pages_product_external_id', $logData['data']['object']['items']['data'][0]['plan']['product'])->first();
                $transaction = Subscription::where('user_id', $userupdate['id'])->where('plan_id', $planupdate['id'])->first();
                $subtransaction = SubscriptionTransactions::where('transaction_id', $logData['data']['object']['id']);

                if ($subtransaction) {
                    // Atualiza os campos desejados
                    $transaction->update([
                        'status' => 'inactive',
                    ]);
                    $subtransaction->update([
                        'payment_status' => $logData['data']['object']['status'],
                    ]);
                }
                try {
                    if (function_exists('isSmtpConfigured') && isSmtpConfigured()) {
                        $sendLocale = $userupdate->locale ?? config('app.locale');
                        Mail::to($userupdate->email)
                            ->locale($sendLocale)
                            ->queue(new ExpiringSubscriptionEmail($userupdate));
                    } else {
                        Log::info('webhook: SMTP not configured, skipping expiring subscription email', ['user' => $userupdate->id ?? null]);
                    }
                } catch (\Exception $e) {
                    Log::error('webhook: failed to queue expiring subscription email - ' . $e->getMessage());
                }
            } else if (($logData['object'] ?? null) === 'event' && ($logData['type'] ?? null) === 'invoice.payment_failed') {
                // Lógica para pagamento falhou
            } else if (($logData['object'] ?? null) === 'event' && ($logData['type'] ?? null) === 'customer.subscription.updated') {
                if (($logData['object']['status'] ?? null) === 'unpaid') {
                    // Lógica para assinatura expirada Ocorre após múltiplas tentativas de pagamento falharem 
                    //Após 7 dias no status unpaid, a assinatura é automaticamente cancelada
                }
                if (($logData['object']['status'] ?? null) === 'active') {
                    $upsell = new UpsellController();
                    $customer = $upsell->request('get', '/customers' . '/' . $logData['data']['object']['customer']);
                    $userupdate = User::where('email', $customer['email'])->first();
                    $planupdate = Plan::where('pages_product_external_id', $logData['data']['object']['items']['data'][0]['plan']['product'])->first();
                    $transaction = Subscription::where('user_id', $userupdate['id'])->where('plan_id', $planupdate['id'])->first();
                    $end_date = $this->get_plan_expiration_date(now(), $planupdate->duration, $planupdate->duration_value);
                    if ($transaction) {
                        // Atualiza os campos desejados
                        $transaction->update([
                            'status' => 'active',
                            'start_date' => now(),
                            'end_date' => $end_date
                        ]);
                    }
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
                $failDir = storage_path('logs/stripe/fail');
                if (!is_dir($failDir)) {
                    mkdir($failDir, 0755, true);
                }
                rename($logFile, $failDir . '/' . basename($logFile));
            }
        }

        if (file_exists($logFile)) {
            $successDir = storage_path('logs/stripe/success');
            if (!is_dir($successDir)) {
                mkdir($successDir, 0755, true);
            }
            rename($logFile, $successDir . '/' . basename($logFile));
        }

        return response()->json(['status' => 'success']);
    }
}
