<?php

namespace Database\Factories;

use App\Models\TrackingPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackingPlatformFactory extends Factory
{
    protected $model = TrackingPlatform::class;

    public function definition(): array
    {
        $platforms = ['meta', 'tiktok', 'ga4', 'google_ads'];

        return [
            'platform' => fake()->randomElement($platforms),
            'name' => fake()->company() . ' ' . fake()->randomElement(['Pixel', 'Tracker', 'Analytics']),
            'pixel_id' => fake()->randomNumber(9, true),
            'access_token' => 'EAAG' . fake()->sha256(),
            'test_event_code' => 'TEST' . fake()->randomNumber(5, true),
            'is_active' => true,
            'is_test_mode' => false,
            'api_version' => 'v19.0',
            'extra' => [
                'ga4_api_secret' => fake()->uuid,
                'tiktok_app_id' => fake()->randomNumber(9, true),
            ],
            'store_id' => null,
        ];
    }

    public function meta(): self
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'meta',
            'api_version' => 'v19.0',
        ]);
    }

    public function tiktok(): self
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'tiktok',
        ]);
    }

    public function ga4(): self
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'ga4',
            'extra' => [
                'ga4_api_secret' => fake()->uuid,
            ],
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function testMode(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_test_mode' => true,
            'test_mode_enabled_at' => now(),
        ]);
    }
}
