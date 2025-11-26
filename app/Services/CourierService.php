<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CourierService
{
    protected $baseUrl;
    protected $apiKey;
    protected $secretKey;
    protected $dryRun = true;

    public function __construct($dryRun = true)
    {
        $this->baseUrl = setting('steedfast_api_base_url');
        $this->apiKey = setting('steedfast_api_key');   
        $this->secretKey = setting('steedfast_api_secret');
        $this->dryRun = $dryRun;
    }

    public function canSend(): bool
    {
        return $this->baseUrl && $this->apiKey && $this->secretKey;
    }

    public function createOrder(array $data)
    {
        if (!$this->canSend()) {
            return [
                'status' => false,
                'message' => 'Courier API credentials not set.'
            ];
        }

        if ($this->dryRun) {
            
            return [
                'status' => 'dry_run',
                'message' => 'This is a test payload, not sent to courier.',
                'payload' => $data
            ];
        }

        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/create_order', $data);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'status' => false,
            'message' => $response->body()
        ];
    }
}
