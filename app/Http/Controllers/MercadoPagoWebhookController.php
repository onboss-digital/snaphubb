<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http as HttpClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionDetail;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\Subscription;
use Modules\Subscriptions\Models\SubscriptionTransactions;
use App\Models\User;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1) Log payload for debugging/audit
        $payload = $request->all();
        Log::channel('daily')->info('mercadopago.webhook.received', $payload);

        // 2) try to extract payment id from common locations
        $paymentId = $request->input('data.id') ?? $request->input('resource.id') ?? $request->input('id') ?? null;
        if (!$paymentId) {
            Log::warning('mercadopago.webhook: missing payment id', ['payload' => $payload]);
            return response()->json(['status' => 'ignored'], 200);
        }

        // 3) allow tests to supply a full `payment` object in the webhook body so
        // we can process locally without calling Mercado Pago API (useful for TEST tokens)
        $mp = $request->input('payment');
        if ($mp && is_array($mp)) {
            // ensure payment id is available
            $paymentId = $mp['id'] ?? $paymentId;
            Log::info('mercadopago.webhook: processing embedded payment object', ['payment_id' => $paymentId]);
        } else {
            // fetch payment details from Mercado Pago to validate status
            $accessToken = env('MERCADOPAGO_ACCESS_TOKEN') ?? env('MERCADOPAGO_TOKEN');
            if (!$accessToken) {
                Log::error('mercadopago.webhook: no access token configured');
                return response()->json(['status' => 'error', 'message' => 'no_token'], 200);
            }

            try {
                $resp = HttpClient::withToken($accessToken)
                    ->acceptJson()
                    ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

                if (!$resp->successful()) {
                    Log::warning('mercadopago.webhook: failed to fetch payment', ['payment_id' => $paymentId, 'status' => $resp->status(), 'body' => $resp->body()]);
                    return response()->json(['status' => 'error'], 200);
                }

                $mp = $resp->json();
            } catch (\Exception $e) {
                Log::error('mercadopago.webhook: exception fetching payment - ' . $e->getMessage());
                return response()->json(['status' => 'error'], 200);
            }
        }

        $status = strtolower($mp['status'] ?? $mp['payment_status'] ?? '');

        // Only process approved/paid payments
        if (!in_array($status, ['approved', 'paid'])) {
            Log::info('mercadopago.webhook: payment not approved yet', ['payment_id' => $paymentId, 'status' => $status]);
            return response()->json(['status' => 'ignored'], 200);
        }

        // 4) determine external reference / plan
        $external = $mp['external_reference'] ?? $mp['metadata']['external_reference'] ?? $mp['metadata']['plan_id'] ?? null;

        $plan = null;
        $planId = null;
        if ($external) {
            // If we used 'plan:{id}' convention
            if (preg_match('/plan:(\d+)/', $external, $m)) {
                $planId = (int)$m[1];
                $plan = Plan::find($planId);
            }

            if (!$plan) {
                // try match by external_product_id
                $plan = Plan::where('external_product_id', $external)->first();
                if ($plan) $planId = $plan->id;
            }
        }

        if (!$plan) {
            Log::warning('mercadopago.webhook: could not resolve plan for payment', ['payment_id' => $paymentId, 'external' => $external, 'mp' => $mp]);
            return response()->json(['status' => 'ignored', 'reason' => 'plan_not_found'], 200);
        }

        // 5) idempotency: check if transaction already processed
        $existing = SubscriptionTransactions::where('transaction_id', $paymentId)->first();
        if ($existing) {
            Log::info('mercadopago.webhook: payment already processed', ['payment_id' => $paymentId]);
            return response()->json(['status' => 'already_processed'], 200);
        }

        // 6) find or create user by payer email
        $payerEmail = $mp['payer']['email'] ?? null;
        $payerName = $mp['payer']['first_name'] ?? ($mp['payer']['name'] ?? null);

        $user = User::firstOrCreate([
            'email' => $payerEmail
        ], [
            'first_name' => $payerName ?? null,
            'last_name' => null,
            'email' => $payerEmail,
            'password' => bcrypt('P@55w0rd'),
            'user_type' => 'user'
        ]);

        // 7) Provision: create subscription and transaction (mirrors existing implementation)
        try {
            $taxes = [];
            // compute taxes similar to other flows if needed - keep minimal here

            $start = now();
            $end = now()->addMonths($plan->duration_value ?? 1);

            $subscription = Subscription::create([
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'device_id' => 1,
                'start_date' => $start,
                'end_date' => $end,
                'status' => 'active',
                'amount' => $plan->price,
                'discount_percentage' => $plan->discount_percentage ?? 0,
                'tax_amount' => 0,
                'total_amount' => $mp['transaction_amount'] ?? $mp['transaction_details']['total_paid_amount'] ?? $plan->price,
                'name' => $plan->name,
                'identifier' => $plan->identifier ?? null,
                'type' => $plan->duration ?? 'month',
                'duration' => $plan->duration_value ?? 1,
                'level' => $plan->level ?? null,
                'plan_type' => null,
                'payment_id' => null,
            ]);

            SubscriptionTransactions::create([
                'user_id' => $user->id,
                'amount' => $subscription->total_amount,
                'payment_type' => 'mercadopago',
                'payment_status' => 'paid',
                'tax_data' => null,
                'transaction_id' => $paymentId,
                'subscriptions_id' => $subscription->id,
            ]);

            $user->update(['is_subscribe' => 1]);

            // 8) send email (queued if possible)
            try {
                if (function_exists('isSmtpConfigured') && isSmtpConfigured()) {
                    // Build a minimal payload for the mailable - SubscriptionDetail expects a resource object in other code paths.
                    // We'll queue the mail to avoid blocking webhook processing.
                    // Send the mailable in the user's preferred locale (if set)
                    $sendLocale = $user->locale ?? config('app.locale');
                    Mail::to($user->email)
                        ->locale($sendLocale)
                        ->queue(new SubscriptionDetail($subscription));
                } else {
                    Log::info('mercadopago.webhook: SMTP not configured, skipping email');
                }
            } catch (\Exception $e) {
                Log::error('mercadopago.webhook: failed to queue/send email - ' . $e->getMessage());
            }

            Log::info('mercadopago.webhook: provisioned subscription', ['payment_id' => $paymentId, 'subscription_id' => $subscription->id]);
        } catch (\Exception $e) {
            Log::error('mercadopago.webhook: provisioning error - ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error'], 200);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
