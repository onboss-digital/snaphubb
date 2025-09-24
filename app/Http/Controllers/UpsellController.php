<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Modules\Subscriptions\Models\Plan;

class UpsellController extends Controller
{
    private string $apiKey;
    private string $baseUrl;
    private Client $client;

    public function __construct()
    {
        $this->apiKey = env('STRIPE_API_SECRET_KEY');
        $this->baseUrl = config('STRIPE_API_URL', 'https://api.stripe.com/v1');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'verify' => !env('APP_DEBUG'),
        ]);
    }

    private function request(string $method, string $endpoint, array $params = [])
    {
        try {
            $options = [];

            if (in_array(strtolower($method), ['post', 'put', 'patch'])) {
                $options['form_params'] = $params; // Stripe espera x-www-form-urlencoded
            } elseif (!empty($params)) {
                $options['query'] = $params; // GET com query string
            }

            $response = $this->client->request(strtoupper($method), $this->baseUrl . $endpoint, $options);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $body = $e->getResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
            $errorMessage = $body['error']['message'] ?? $e->getMessage();

            throw new \Exception($errorMessage);
        }
    }

    public function getPaymentMethodIdByCustomer(string $customerId): ?string
    {
        // 🔹 Busca payment_methods do tipo "card"
        $response = $this->request('get', '/payment_methods', [
            'customer' => $customerId,
            'type'     => 'card'
        ]);

        if (!empty($response['data']) && count($response['data']) > 0) {
            // retorna o primeiro payment_method
            return $response['data'][0]['id'];
        }

        return null; // nenhum payment_method encontrado
    }

    public function accept(Request $request)
    {
        $customerId = $request->input('customerId');
        $productId  = $request->input('productId');       // produto que será pago
        $upsell_productId  = $request->input('upsell_productId'); // só para achar o plano
        $currency = $request->input('currency');
        $fullUrl = $request->input('origin');

        // Pega payment method do cliente
        $paymentMethodId = $this->getPaymentMethodIdByCustomer($customerId);
        if (!$paymentMethodId) {
            return Response::json([
                'status' => 'payment_fail',
                'message' => 'Nenhum método de pagamento encontrado para o cliente.'
            ], 400);
        }

        // Busca o plano pelo upsell_productId
        $plan = Plan::where('pages_product_external_id', $upsell_productId)->first();
        if (!$plan) {
            return Response::json([
                'status' => 'payment_fail',
                'message' => 'Plano não encontrado.'
            ], 404);
        }
        $prices = [];
        try {
            // 🔹 Busca o preço do produto real que vai ser pago
            $res = $this->request('get', '/prices', [
                'product' => $productId,
                'limit'   => 100,
            ]);
            if (!empty($res['data'])) {
                foreach ($res['data'] as $price) {
                    $prices[] = [
                        'id'          => $price['id'],
                        'currency'    => strtoupper($price['currency']), // ex: BRL, USD, EUR
                        'unit_amount' => $price['unit_amount'],          // valor em centavos
                    ];
                }
            }

            $filteredPrices = array_filter($prices, fn($p) => strtolower($p['currency']) === strtolower($currency));
            $selectedPrice = reset($filteredPrices);

            $paymentIntent = $this->request('post', '/payment_intents', [
                'customer' => $customerId,
                'payment_method' => $paymentMethodId,
                'amount' => $selectedPrice['unit_amount'],
                'currency' => $selectedPrice['currency'],
                'confirm' => 'true',
                'off_session' => 'true',
                'metadata' => [
                    'product_id' => $productId,
                    'plan_id' => $plan->id
                ]
            ]);

            // 🔹 Verifica se o pagamento foi concluído
            if (!empty($paymentIntent['status']) && $paymentIntent['status'] === 'succeeded') {
                return Response::json([
                    'status' => 'success',
                    'url_redirect' => $fullUrl === $plan['pages_upsell_succes_url'] || $fullUrl === $plan['pages_downsell_url'] || $fullUrl === $plan['pages_upsell_fail_url']
                        ? null
                        : $plan['pages_upsell_succes_url']
                ]);
            } else {
                return Response::json([
                    'status' => 'payment_fail',
                    'url_redirect' => $fullUrl === $plan['pages_upsell_fail_url']
                        ? null
                        : $plan['pages_upsell_fail_url']
                ]);
            }
        } catch (\Exception $e) {
            return Response::json([
                'status' => 'payment_fail',
                'message' => $e->getMessage(),
                'url_redirect' => $plan['pages_upsell_fail_url']
            ], 500);
        }
    }

    public function price(Request $request)
    {
        $productId  = $request->input('productId');
        $prices = [];
        try {
            // 🔹 Busca o preço do produto real que vai ser pago
            $product = $this->request('get', "/products/{$productId}");
            $res = $this->request('get', '/prices', [
                'product' => $productId,
                'limit'   => 100,
            ]);
            if (!empty($res['data'])) {
                foreach ($res['data'] as $price) {
                    $prices[] = [
                        'id'          => $price['id'],
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'currency'    => strtoupper($price['currency']), // ex: BRL, USD, EUR
                        'unit_amount' => $price['unit_amount'],          // valor em centavos
                    ];
                }
            }
            return Response::json([
                'status' => 'success',
                'prices' => $prices
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'status' => 'payment_fail',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request)
    {
        $upsell_productId  = $request->input('upsell_productId');

        $plan = Plan::where('pages_product_external_id', $upsell_productId)->first();
        $fullUrl = $request->input('origin');

        return Response::json([
            'status' => 'rejected',
            'url_redirect' => $fullUrl === $plan['pages_downsell_url'] ? null : $plan['pages_downsell_url'] // ex: recusado
        ]);
    }
}
