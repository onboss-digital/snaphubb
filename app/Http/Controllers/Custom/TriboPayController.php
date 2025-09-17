<?php


namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Custom\TriboPayService;
use Kreait\Firebase\Value\Uid;

class TriboPayController extends Controller
{
    protected $triboPayService;

    public function __construct()
    {
        $this->triboPayService = new TriboPayService();
    }

    public function sendCheckout(Request $request)
    {
        $request->validate([
            'currency' => 'required|string',
            'payment_method' => 'required|string',
            'final-price' => 'required|string',
            'card_number' => 'required_if:payment_method,card',
            'card_expiry' => 'required_if:payment_method,card',
            'card_cvv' => 'required_if:payment_method,card',
            'card_name' => 'required_if:payment_method,card',
            'order_bump' => 'nullable|boolean'
        ]);

        // Format the amount (remove currency symbol and convert to cents)
        $amountStr = $request->input('final-price');
        $amount = (int) (preg_replace('/[^0-9,.]/', '', $amountStr) * 100);

        // Convert plan and currency to offer_hash if needed
        $offer_hash = $request->input('plan', 'monthly') . '-' . $request->input('currency', 'BRL');

        $payload = [
            'amount' => $amount,
            'offer_hash' => $offer_hash,
            'payment_method' => $request->input('payment_method'),
            'customer' => [
                'name' => $request->input('customer_name') ?? 'Customer name',
                'email' => $request->input('customer_email') ?? 'teste@test.com',
                'phone_number' => $request->input('customer_phone_number') ?? '21975784612',
            ],
            'installments' => 1,
            'expire_in_days' => 1,
            'postback_url' => env('TRIBOPAY_POSTBACK_URL', 'https://enf8p6q9i44zv.x.pipedream.net/'),
        ];

        // Add card data if payment method is card
        $payload['card'] = [
            'number' => preg_replace('/\D/', '', $request->input('card_number')),
            'holder_name' => $request->input('card_name'),
            'exp_month' => (int) date('m', strtotime($request->input('card_expiry'))),
            'exp_year' => (int) date('Y', strtotime($request->input('card_expiry'))),
            'cvv' => $request->input('card_cvv')
        ];

        $payload['cart'] = [
            [
                'product_hash' => $offer_hash,
                'title' => 'Assinatura Premium',
                'cover' => null,
                'price' => $amount,
                'quantity' => 1,
                'operation_type' => 1
            ]
        ];


        $body = json_decode('{
   "amount": 800,
   "offer_hash": "velit nostrud dolor in deserunt",
   "payment_method": "credit_card",
   "card": {
      "number": "2",
      "holder_name": "Betty Morissette",
      "exp_month": 47372423,
      "exp_year": 2025,
      "cvv": "277"
   },
   "customer": {
      "name": "Rafael Crooks",
      "email": "Eldora6@yahoo.com",
      "phone_number": "(983) 955-1031"
   },
   "cart": [
      {
         "product_hash": "xwe2w2p4ce",
         "title": "Product test",
         "price": 800,
         "quantity": 1,
         "operation_type": 1
      }
   ],
   "installments": 93439145,
   "expire_in_days": -11455546,
   "postback_url": "https://old-eyeliner.info/"
}');

        $payment = $this->triboPayService->createPayment($payload);
        // Log the response
        Log::info('TriboPayService payment response', ['response' => $payment]);

        dd($payment );
        // Here you would typically redirect to a success page or handle the response
        return response()->json($payment);
        try {
        } catch (\Exception $e) {
            Log::error('TriboPayService payment error', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
