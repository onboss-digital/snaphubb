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



        $payload = [
            'amount' => $request->input('final-price'),
            'offer_hash' => $request->input('offer_hash'),
            'payment_method' => $request->input('payment_method'),
            'card' => [
                'number' => $request->input('card_number'),
                'holder_name' => $request->input('card_name'),
                'exp_month' => date('m', strtotime($request->input('card_expiry'))),
                'exp_year' => date('Y', strtotime($request->input('card_expiry'))),
                'cvv' => $request->input('card_cvv')
            ],
            'customer' => [
                'name' => $request->input('customer_name') ?? 'Customer name',
                'email' => $request->input('customer_email') ?? 'teste@test.com',
                'phone_number' => $request->input('customer_phone_number')?? '21975784612' ,
            ],
        ];

        $payment = $this->triboPayService->createPayment($payload);
        dd($payment);

    }
}
