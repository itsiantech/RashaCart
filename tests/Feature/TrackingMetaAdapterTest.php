<?php

namespace Tests\Feature;

use App\Models\TrackingPlatform;
use App\Services\Tracking\Adapters\MetaAdapter;
use App\Services\Tracking\HashingService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TrackingMetaAdapterTest extends TestCase
{
    protected function adapter(): MetaAdapter
    {
        return new MetaAdapter(new HashingService());
    }

    protected function platform(array $overrides = []): TrackingPlatform
    {
        return new TrackingPlatform(array_merge([
            'id' => 1,
            'platform' => 'meta',
            'name' => 'Test Pixel',
            'pixel_id' => '1234567890',
            'access_token' => 'test_token_abc',
            'api_version' => 'v19.0',
        ], $overrides));
    }

    protected function eventData(): array
    {
        return [
            'event_name' => 'Purchase',
            'event_id' => 'purchase_7',
            'event_time' => time(),
            'action_source' => 'website',
            'event_source_url' => 'https://example.com/checkout',
            'user_data' => ['em' => hash('sha256', 'a@b.com')],
            'custom_data' => ['value' => 1500, 'currency' => 'BDT'],
            'fbp' => 'fb.1.123.user',
            'fbc' => 'fb.1.123.cli',
        ];
    }

    public function test_send_posts_to_conversions_api_with_test_event_code(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['events_received' => 1], 200)]);

        $platform = $this->platform(['test_event_code' => 'TEST123']);
        $result = $this->adapter()->send($this->eventData(), $platform);

        $this->assertTrue($result->success);

        Http::assertSent(function ($request) {
            $payload = $request->data()['data'][0];

            return $payload['event_id'] === 'purchase_7'
                && $payload['action_source'] === 'website'
                && $payload['test_event_code'] === 'TEST123'
                && $payload['user_data']['fbp'] === 'fb.1.123.user'
                && $payload['user_data']['fbc'] === 'fb.1.123.cli'
                && str_contains($request->url(), '/v19.0/1234567890/events');
        });
    }

    public function test_missing_token_is_permanent_failure_without_http(): void
    {
        $result = $this->adapter()->send($this->eventData(), $this->platform(['access_token' => null]));

        $this->assertFalse($result->success);
        $this->assertFalse($result->isRetryable());
        Http::assertNothingSent();
    }

    public function test_server_error_is_retryable_but_bad_request_is_not(): void
    {
        Http::fakeSequence()
            ->push(['error' => ['message' => 'Oops', 'code' => 1]], 500)
            ->push(['error' => ['message' => 'Bad pixel', 'code' => 100]], 400);

        $retryable = $this->adapter()->send($this->eventData(), $this->platform());
        $this->assertFalse($retryable->success);
        $this->assertTrue($retryable->isRetryable());

        $permanent = $this->adapter()->send($this->eventData(), $this->platform());
        $this->assertFalse($permanent->success);
        $this->assertFalse($permanent->isRetryable());
    }
}
