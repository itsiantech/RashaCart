<?php

namespace App\Services\Couriers;

interface CourierDriverInterface
{
    public function canSend(): bool;
    public function createOrder(array $data): array;
    public function trackOrder(string $trackingCode): array;
}