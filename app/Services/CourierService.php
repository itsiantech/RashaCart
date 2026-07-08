<?php

namespace App\Services;

use App\Services\Couriers\CourierDriverInterface;
use App\Services\Couriers\PathaoDriver;
use App\Services\Couriers\SteadfastDriver;

class CourierService
{
    protected CourierDriverInterface $driver;

    protected bool $dryRun;

    public function __construct($dryRun = null)
    {
        if ($dryRun === null) {
            // Set dryRun to true if not in production, false in production
            $this->dryRun = app()->environment() !== 'production';
        } else {
            $this->dryRun = $dryRun;
        }
        $this->driver = $this->resolveDriver();
    }

    private function resolveDriver(): CourierDriverInterface
    {
        return match (setting('selected_courier')) {
            'pathao' => new PathaoDriver,
            default => new SteadfastDriver, // keeps all existing behaviour
        };
    }

    public function canSend(): bool
    {
        return $this->driver->canSend();
    }

    public function createOrder(array $data): array
    {
        if (! $this->canSend()) {
            return [
                'status' => false,
                'message' => 'Courier API credentials not set.',
            ];
        }

        if ($this->dryRun) {
            return [
                'status' => 'dry_run',
                'message' => 'This is a test payload, not sent to courier.',
                'payload' => $data,
            ];
        }

        return $this->driver->createOrder($data);
    }

    public function trackOrder(string $trackingCode): array
    {
        if (! $this->canSend()) {
            return [
                'status' => false,
                'message' => 'Courier API credentials not set.',
            ];
        }

        return $this->driver->trackOrder($trackingCode);
    }

    // Use this when you need Pathao-specific methods like getCities()
    public function driver(): CourierDriverInterface
    {
        return $this->driver;
    }
}
