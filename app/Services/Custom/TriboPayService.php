<?php

namespace App\Services\Custom;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class TriboPayService
{
    protected $baseUrl;
    protected $apiToken;
    protected $client;

    public function __construct()
    {
        $this->baseUrl = env('TRIBOPAY_API_URL', 'https://api.tribopay.com.br/api/public/v1/');
        $this->apiToken = env('TRIBOPAY_API_TOKEN');
        $this->client = new Client([
            'verify' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Make an API request to TriboPay using Guzzle
     */
    protected function request($method, $endpoint, $data = [], $query = [])
    {
        try {
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];

            // Merge query params into endpoint if provided
            if (!empty($query)) {
                $endpoint .= (strpos($endpoint, '?') === false ? '?' : '&') . http_build_query($query);
            }

            $body = json_encode($data);
            $request = new Request('POST', "{$this->baseUrl}{$endpoint}?api_token=" . $this->apiToken, $headers, $body);
            $response = $this->client->sendAsync($request)->wait();

            dd($response->getStatusCode(), $response->getBody()->getContents());
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('TriboPayService API Error: ' . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            }
            throw $e;
        }
    }

    /**
     * Create a payment transaction
     * 
     * @param array $data
     * @return array
     */
    public function createPayment(array $data)
    {
        // Ensure all required fields are present according to API spec
        $requiredFields = [
            'amount',
            'payment_method',
            'customer',
            'installments',
            'expire_in_days',
            'postback_url'
        ];

        // Adding defaults to ensure API compliance
        $defaults = [
            'installments' => 1,
            'expire_in_days' => 1,
            'postback_url' => env('TRIBOPAY_POSTBACK_URL', 'https://enf8p6q9i44zv.x.pipedream.net/'),
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($data[$key])) {
                $data[$key] = $value;
            }
        }

        // Process customer data
        if (!array_key_exists('document', $data['customer'])) {
            $data['customer']['document'] = env('TRIBOPAY_DEFAULT_DOCUMENT', '09115751031');
        }

        // Ensure address fields are present
        $addressDefaults = [
            'street_name' => 'Nome da Rua',
            'number' => 'sn',
            'complement' => 'Complemento',
            'neighborhood' => 'Centro',
            'city' => 'Itaguaí',
            'state' => 'RJ',
            'zip_code' => '23822180'
        ];

        foreach ($addressDefaults as $key => $value) {
            if (!isset($data['customer'][$key])) {
                $data['customer'][$key] = $value;
            }
        }

        // Add cart if not present
        if (!isset($data['cart'])) {
            $data['cart'] = [
                [
                    'product_hash' => $data['offer_hash'] ?? '45ziutvowl',
                    'title' => 'Assinatura Premium',
                    'cover' => null,
                    'price' => $data['amount'],
                    'quantity' => 1,
                    'operation_type' => 1,
                    'tangible' => false
                ]
            ];
        }
        // $this->request('POST', 'transactions', $data);
        return $this->request('POST',  'transactions', $data);
    }
}
