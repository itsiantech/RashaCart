<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PathaoService
{
    private string $baseUrl = 'https://hermes.pathao.com/api/v1';

    private function getToken(): string
    {
        return Cache::remember('pathao_access_token', now()->addHours(23), function () {
            $response = Http::post("{$this->baseUrl}/issue-token", [
                'client_id'     => setting('pathao_client_id'),
                'client_secret' => setting('pathao_client_secret'),
                'username'      => setting('pathao_username'),
                'password'      => setting('pathao_password'),
                'grant_type'    => 'password',
            ]);

            throw_unless($response->successful(), \Exception::class, 'Pathao auth failed');

            return $response->json('access_token');
        });
    }

    private function http()
    {
        return Http::withToken($this->getToken())
                   ->baseUrl($this->baseUrl);
    }

    public function createOrder(array $data): array
    {
        return $this->http()->post('/orders', [
            'store_id'            => setting('pathao_store_id'),
            'merchant_order_id'   => $data['invoice'],
            'recipient_name'      => $data['name'],
            'recipient_phone'     => $data['phone'],
            'recipient_address'   => $data['address'],
            'recipient_city'      => $data['city_id'],
            'recipient_zone'      => $data['zone_id'],
            'recipient_area'      => $data['area_id'] ?? null,
            'delivery_type'       => $data['delivery_type'] ?? 48,
            'item_type'           => $data['item_type'] ?? 2,
            'special_instruction' => $data['note'] ?? '',
            'item_quantity'       => $data['quantity'] ?? 1,
            'item_weight'         => $data['weight'] ?? 0.5,
            'amount_to_collect'   => $data['cod_amount'],
            'item_description'    => $data['description'] ?? '',
        ])->json();
    }

    public function getCities(): array
    {
        return $this->http()->get('/countries/1/city-list')->json('data.data', []);
    }

    public function getZones(int $cityId): array
    {
        return $this->http()->get("/cities/{$cityId}/zone-list")->json('data.data', []);
    }

    public function getAreas(int $zoneId): array
    {
        return $this->http()->get("/zones/{$zoneId}/area-list")->json('data.data', []);
    }

    public function trackOrder(string $consignmentId): array
    {
        return $this->http()->get("/orders/{$consignmentId}")->json();
    }
}