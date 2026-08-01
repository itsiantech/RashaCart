<?php

namespace App\Services\Couriers;

use Illuminate\Support\Facades\Http;

class SteadfastDriver implements CourierDriverInterface
{
    private ?string $baseUrl = null;

    private ?string $apiKey = null;

    private ?string $secretKey = null;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            setting('steedfast_api_base_url') ?: 'https://portal.packzy.com/api/v1',
            '/'
        );
        $this->apiKey = setting('steedfast_api_key');
        $this->secretKey = setting('steedfast_api_secret');
    }

    public function canSend(): bool
    {
        return (bool) ($this->baseUrl && $this->apiKey && $this->secretKey);
    }

    private function headers(): array
    {
        return [
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function createOrder(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(15)
                ->post("{$this->baseUrl}/create_order", [
                    'invoice' => $data['invoice'],
                    'recipient_name' => $data['name'],
                    'recipient_phone' => $data['phone'],
                    'recipient_address' => $data['address'],
                    'cod_amount' => $data['cod_amount'],
                    'note' => $data['note'] ?? '',
                ]);

            return $response->successful()
                ? $response->json()
                : ['status' => false, 'message' => $response->body()];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return ['status' => false, 'message' => 'Could not connect to Steadfast: '.$e->getMessage()];
        }
    }

    public function trackOrder(string $trackingCode): array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/status_by_trackingcode/{$trackingCode}");

        return $response->successful()
            ? $response->json()
            : ['status' => false, 'message' => $response->body()];
    }
}
