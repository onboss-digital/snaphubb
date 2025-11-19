<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionDetail;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\Subscription;
use Modules\Subscriptions\Models\SubscriptionTransactions;
use App\Models\User;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1) Log payload for debugging/audit
        $payload = $request->all();
        Log::channel('daily')->info('stripe.webhook.received', $payload);

        // 2) Verify webhook signature (recommended for production)
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');
        
        if ($webhookSecret && $signature) {
            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_API_SECRET_KEY'));
                $event = \Stripe\Webhook::constructEvent(
                    $request->getContent(),
                    $signature,
                    $webhookSecret
                );
            } catch (\Exception $e) {
                Log::error('stripe.webhook: signature verification failed - ' . $e->getMessage());
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        } else {
            // For testing without signature verification
            $event = $payload;
        }

        // 3) Extract event type and data
        $eventType = $event['type'] ?? null;
        $eventData = $event['data']['object'] ?? null;

        if (!$eventType || !$eventData) {
            Log::warning('stripe.webhook: missing event type or data', ['payload' => $payload]);
            return response()->json(['status' => 'ignored'], 200);
        }

        Log::info('stripe.webhook: processing event', ['type' => $eventType]);

        // 4) Handle different event types
        switch ($eventType) {
            case 'checkout.session.completed':
                return $this->handleCheckoutCompleted($eventData);
            
            case 'invoice.payment_succeeded':
                return $this->handleInvoicePaymentSucceeded($eventData);
            
            case 'payment_intent.succeeded':
                return $this->handlePaymentIntentSucceeded($eventData);
            
            case 'charge.succeeded':
                return $this->handleChargeSucceeded($eventData);
            
            default:
                Log::info('stripe.webhook: unhandled event type', ['type' => $eventType]);
                return response()->json(['status' => 'ignored'], 200);
        }
    }

    /**
     * Handle Checkout Session Completed (one-time payment)
     */
    protected function handleCheckoutCompleted($session)
    {
        $sessionId = $session['id'] ?? null;
        $paymentIntentId = $session['payment_intent'] ?? null;
        $metadata = $session['metadata'] ?? [];
        $planId = $metadata['plan_id'] ?? null;
        $customerEmail = $session['customer_email'] ?? $session['customer_details']['email'] ?? null;
        $customerName = $session['customer_details']['name'] ?? null;
        $amountTotal = ($session['amount_total'] ?? 0) / 100; // Convert cents to currency

        if (!$planId) {
            Log::warning('stripe.webhook: missing plan_id in session metadata', ['session_id' => $sessionId]);
            return response()->json(['status' => 'ignored', 'reason' => 'missing_plan_id'], 200);
        }

        $plan = Plan::find($planId);
        if (!$plan) {
            Log::warning('stripe.webhook: plan not found', ['plan_id' => $planId]);
            return response()->json(['status' => 'ignored', 'reason' => 'plan_not_found'], 200);
        }

        // Check idempotency
        $existing = SubscriptionTransactions::where('transaction_id', $sessionId)->first();
        if ($existing) {
            Log::info('stripe.webhook: session already processed', ['session_id' => $sessionId]);
            return response()->json(['status' => 'already_processed'], 200);
        }

        return $this->provisionSubscription($plan, $customerEmail, $customerName, $sessionId, $amountTotal);
    }

    /**
     * Handle Invoice Payment Succeeded (subscription payment)
     */
    protected function handleInvoicePaymentSucceeded($invoice)
    {
        $invoiceId = $invoice['id'] ?? null;
        $metadata = $invoice['lines']['data'][0]['metadata'] ?? [];
        $planId = $metadata['plan_id'] ?? $metadata['product_id'] ?? null;
        $customerEmail = $invoice['customer_email'] ?? null;
        $customerName = $invoice['customer_name'] ?? null;
        $amountPaid = ($invoice['amount_paid'] ?? 0) / 100;

        if (!$planId) {
            Log::warning('stripe.webhook: missing plan_id in invoice metadata', ['invoice_id' => $invoiceId]);
            return response()->json(['status' => 'ignored', 'reason' => 'missing_plan_id'], 200);
        }

        $plan = Plan::find($planId);
        if (!$plan) {
            // Try by external_product_id
            $plan = Plan::where('external_product_id', $planId)->first();
            if (!$plan) {
                Log::warning('stripe.webhook: plan not found', ['plan_id' => $planId]);
                return response()->json(['status' => 'ignored', 'reason' => 'plan_not_found'], 200);
            }
        }

        // Check idempotency
        $existing = SubscriptionTransactions::where('transaction_id', $invoiceId)->first();
        if ($existing) {
            Log::info('stripe.webhook: invoice already processed', ['invoice_id' => $invoiceId]);
            return response()->json(['status' => 'already_processed'], 200);
        }

        return $this->provisionSubscription($plan, $customerEmail, $customerName, $invoiceId, $amountPaid);
    }

    /**
     * Handle Payment Intent Succeeded
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        $paymentIntentId = $paymentIntent['id'] ?? null;
        $metadata = $paymentIntent['metadata'] ?? [];
        $planId = $metadata['plan_id'] ?? null;
        $amountReceived = ($paymentIntent['amount_received'] ?? 0) / 100;

        if (!$planId) {
            Log::info('stripe.webhook: payment_intent without plan_id, might be handled by checkout.session', ['payment_intent_id' => $paymentIntentId]);
            return response()->json(['status' => 'ignored'], 200);
        }

        // This will be handled by checkout.session.completed in most cases
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Handle Charge Succeeded (fallback)
     */
    protected function handleChargeSucceeded($charge)
    {
        $chargeId = $charge['id'] ?? null;
        Log::info('stripe.webhook: charge succeeded', ['charge_id' => $chargeId]);
        
        // Usually handled by other events
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Centralized provisioning logic with DB transaction
     */
    protected function provisionSubscription($plan, $email, $name, $transactionId, $amount)
    {
        try {
            DB::transaction(function () use ($plan, $email, $name, $transactionId, $amount) {
                // Find or create user
                $user = User::firstOrCreate([
                    'email' => $email
                ], [
                    'first_name' => $name ?? explode('@', $email)[0],
                    'last_name' => null,
                    'email' => $email,
                    'password' => bcrypt('P@55w0rd'),
                    'user_type' => 'user'
                ]);

                // Create subscription
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
                    'total_amount' => $amount ?: $plan->price,
                    'name' => $plan->name,
                    'identifier' => $plan->identifier ?? null,
                    'type' => $plan->duration ?? 'month',
                    'duration' => $plan->duration_value ?? 1,
                    'level' => $plan->level ?? null,
                    'plan_type' => null,
                    'payment_id' => null,
                ]);

                // Create transaction
                SubscriptionTransactions::create([
                    'user_id' => $user->id,
                    'amount' => $subscription->total_amount,
                    'payment_type' => 'stripe',
                    'payment_status' => 'paid',
                    'tax_data' => null,
                    'transaction_id' => $transactionId,
                    'subscriptions_id' => $subscription->id,
                ]);

                // Update user subscription status
                $user->update(['is_subscribe' => 1]);

                // Send email
                try {
                    if (function_exists('isSmtpConfigured') && isSmtpConfigured()) {
                        $sendLocale = $user->locale ?? config('app.locale');
                        Mail::to($user->email)
                            ->locale($sendLocale)
                            ->queue(new SubscriptionDetail($subscription));
                    } else {
                        Log::info('stripe.webhook: SMTP not configured, skipping email');
                    }
                } catch (\Exception $e) {
                    Log::error('stripe.webhook: failed to queue email - ' . $e->getMessage());
                }

                Log::info('stripe.webhook: provisioned subscription', [
                    'transaction_id' => $transactionId,
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id
                ]);
            });

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('stripe.webhook: provisioning error - ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }
}
