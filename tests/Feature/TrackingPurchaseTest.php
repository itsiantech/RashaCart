<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\TrackingEventRule;
use App\Models\TrackingLog;
use App\Models\TrackingPlatform;
use App\Services\Tracking\TrackingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TrackingPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Http::fake(['graph.facebook.com/*' => Http::response(['events_received' => 1], 200)]);
    }

    protected function makePlatform(): TrackingPlatform
    {
        $platform = TrackingPlatform::create([
            'platform' => 'meta',
            'name' => 'Test Pixel',
            'pixel_id' => '1234567890',
            'access_token' => 'secret_token',
            'api_version' => 'v19.0',
            'is_active' => true,
        ]);
        TrackingEventRule::ensureDefaults($platform->id);

        return $platform;
    }

    protected function makeOrder(): Order
    {
        return Order::create([
            'invoice_no' => 'INV-TEST-001',
            'name' => 'Test Customer',
            'phone' => '01712345678',
            'delivery_zone' => 'Dhaka',
            'delivery_charge' => 60,
            'payment_method' => 'cod',
            'shipping_address' => 'House 1, Dhaka',
            'total_amount' => 1060,
            'status' => 'pending',
            'fbp' => 'fb.1.1.u',
            'fbc' => 'fb.1.1.c',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test-agent',
        ]);
    }

    public function test_token_is_encrypted_at_rest(): void
    {
        $platform = $this->makePlatform();

        $raw = \Illuminate\Support\Facades\DB::table('pixel_settings')
            ->where('id', $platform->id)->value('access_token');

        $this->assertNotSame('secret_token', $raw);
        $this->assertSame('secret_token', $platform->fresh()->access_token);
    }

    public function test_purchase_sent_exactly_once_per_order(): void
    {
        $this->makePlatform();
        $order = $this->makeOrder();

        $manager = app(TrackingManager::class);
        $manager->trackPurchase($order);

        // QUEUE_CONNECTION=sync in phpunit, so jobs run inline
        $this->assertNotNull($order->fresh()->purchase_sent_at);
        $this->assertSame(1, TrackingLog::forEvent('Purchase')->where('status', 'success')->count());

        // Simulate thank-you refresh + status change — must not resend
        $manager->trackPurchase($order->fresh());
        $order->fresh()->update(['status' => 'confirmed']);
        $manager->trackEvent('Purchase', [], $order->fresh());

        $this->assertSame(1, TrackingLog::forEvent('Purchase')->where('status', 'success')->count());
    }

    public function test_settings_save_uses_new_pixel_immediately(): void
    {
        $platform = $this->makePlatform();

        $this->assertSame('1234567890', TrackingPlatform::getActivePlatforms()->first()->pixel_id);

        $platform->update(['pixel_id' => '999999']);
        app(TrackingManager::class)->clearCache();

        $this->assertSame('999999', TrackingPlatform::getActivePlatforms()->first()->pixel_id);
    }

    public function test_guest_cannot_view_tracking_settings(): void
    {
        $this->get(route('admin.server-side-tracking.index'))->assertRedirect(route('login'));
        $this->get(route('admin.server-side-tracking.logs'))->assertRedirect(route('login'));
    }
}
