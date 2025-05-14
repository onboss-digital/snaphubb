<?php

namespace App\Services\Custom;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class TriboPayService
{
    protected $baseUrl;
    protected $apiToken;
    protected $client;

    public function __construct()
    {
        $this->baseUrl = env('TRIBOPAY_API_URL', 'https://api.tribopay.com/api/api');
        $this->apiToken = env('TRIBOPAY_API_TOKEN');
        $this->client = new Client([
            'verify' => false,
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Make an API request to TriboPay using Guzzle
     */
    protected function request($method, $endpoint, $data = [], $query = [])
    {
        
    }

}