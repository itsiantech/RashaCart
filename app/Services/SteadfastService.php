<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SteadfastService
{
    private ?string $baseUrl = null;

    private ?string $apiKey = null;

    private ?string $apiSecret = null;

    public function __construct()
    {
        $this->baseUrl = setting('steedfast_api_base_url', 'https://portal.steadfast.com.bd/api/v1');
        $this->apiKey = setting('steedfast_api_key');
        $this->apiSecret = setting('steedfast_api_secret');
    }

    private function headers(): array
    {
        return [
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->apiSecret,
            'Content-Type' => 'application/json',
        ];
    }

    public function createOrder(array $data): array
    {
        return Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/create_order", [
                'invoice' => $data['invoice'],
                'recipient_name' => $data['name'],
                'recipient_phone' => $data['phone'],
                'recipient_address' => $data['address'],
                'cod_amount' => $data['cod_amount'],
                'note' => $data['note'] ?? '',
            ])->json();
    }

    public function trackOrder(string $trackingCode): array
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/status_by_trackingcode/{$trackingCode}")
            ->json();
    }

    public function getBalance(): array
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/get_balance")
            ->json();
    }
}
