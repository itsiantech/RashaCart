<?php

namespace Database\Factories;

use App\Models\TrackingEventRule;
use App\Models\TrackingPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackingEventRuleFactory extends Factory
{
    protected $model = TrackingEventRule::class;

    public function definition(): array
    {
        $eventNames = ['PageView', 'ViewContent', 'Search', 'AddToCart', 'InitiateCheckout', 'AddPaymentInfo', 'Lead', 'Purchase'];
        $triggerTypes = ['frontend', 'order_placed', 'order_status'];

        return [
            'platform_id' => TrackingPlatform::factory(),
            'event_name' => fake()->randomElement($eventNames),
            'is_enabled' => true,
            'trigger_type' => fake()->randomElement($triggerTypes),
            'trigger_status' => fake()->randomElement(['confirmed', 'delivered', 'shipped', null]),
            'value_mode' => fake()->randomElement(['full', 'exclude_shipping', 'custom']),
            'send_browser' => true,
            'send_server' => true,
            'delay_minutes' => fake()->randomElement([null, 0, 5, 10, 30]),
        ];
    }

    public function purchase(): self
    {
        return $this->state(fn (array $attributes) => [
            'event_name' => 'Purchase',
            'trigger_type' => 'order_status',
            'trigger_status' => 'confirmed',
        ]);
    }

    public function frontend(): self
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => 'frontend',
            'trigger_status' => null,
        ]);
    }

    public function serverOnly(): self
    {
        return $this->state(fn (array $attributes) => [
            'send_browser' => false,
            'send_server' => true,
        ]);
    }

    public function browserOnly(): self
    {
        return $this->state(fn (array $attributes) => [
            'send_browser' => true,
            'send_server' => false,
        ]);
    }

    public function withDelay(int $minutes = 5): self
    {
        return $this->state(fn (array $attributes) => [
            'delay_minutes' => $minutes,
        ]);
    }

    public function disabled(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_enabled' => false,
        ]);
    }
}
