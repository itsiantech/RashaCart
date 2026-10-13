<?php

namespace Database\Factories;

use App\Models\TrackingLog;
use App\Models\TrackingPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackingLogFactory extends Factory
{
    protected $model = TrackingLog::class;

    public function definition(): array
    {
        $statuses = ['pending', 'success', 'failed', 'skipped', 'duplicate'];
        $eventNames = ['PageView', 'ViewContent', 'AddToCart', 'InitiateCheckout', 'Purchase'];

        return [
            'platform_id' => TrackingPlatform::factory(),
            'event_name' => fake()->randomElement($eventNames),
            'event_id' => fake()->uuid,
            'order_id' => null,
            'payload' => [
                'event_name' => fake()->randomElement($eventNames),
                'event_time' => time(),
                'user_data' => [
                    'em' => fake()->email,
                    'ph' => fake()->phoneNumber,
                ],
                'custom_data' => [
                    'value' => fake()->randomFloat(2, 100, 1000),
                    'currency' => 'BDT',
                ],
            ],
            'response' => [
                'success' => true,
                'events_received' => 1,
            ],
            'http_status' => 200,
            'status' => fake()->randomElement($statuses),
            'attempts' => fake()->numberBetween(0, 5),
            'error_message' => null,
            'sent_at' => now(),
        ];
    }

    public function success(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'http_status' => 200,
            'sent_at' => now(),
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'http_status' => fake()->randomElement([400, 401, 403, 404, 500, 502, 503]),
            'error_message' => fake()->sentence(),
            'attempts' => fake()->numberBetween(1, 5),
        ]);
    }

    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'http_status' => null,
            'sent_at' => null,
            'attempts' => 0,
        ]);
    }

    public function purchase(): self
    {
        return $this->state(fn (array $attributes) => [
            'event_name' => 'Purchase',
            'payload' => [
                'event_name' => 'Purchase',
                'event_time' => time(),
                'custom_data' => [
                    'value' => fake()->randomFloat(2, 500, 5000),
                    'currency' => 'BDT',
                    'contents' => [
                        [
                            'id' => fake()->randomNumber(6),
                            'quantity' => fake()->numberBetween(1, 5),
                            'item_price' => fake()->randomFloat(2, 100, 1000),
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function withOrder(int $orderId): self
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $orderId,
        ]);
    }
}
