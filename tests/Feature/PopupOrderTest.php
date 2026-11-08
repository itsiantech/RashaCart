<?php

namespace Tests\Feature;

use App\Livewire\Frontend\OrderPopup;
use App\Models\Order;
use App\Models\Product;
use App\Models\Settings;
use App\Services\PhoneNormalizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class PopupOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('order-popup:127.0.0.1');

        Settings::set('popup_order_enabled', '1');
        Settings::set('popup_order_whatsapp', '1');
        Settings::set('popup_order_title', 'Order Now');
        Settings::set('popup_order_default_zone', 'inside');
        Settings::set('dc_inside', '60');
        Settings::set('dc_outside', '120');
        Settings::set('currency', '৳');
        Settings::set('contact_phone', '01700000000');
    }

    protected function makeProduct(array $over = []): Product
    {
        return Product::factory()->create(array_merge([
            'status' => true,
            'price' => 500,
            'sale_price' => 500,
            'stock' => 20,
        ], $over));
    }

    protected function fillForm($component, array $over = [])
    {
        return $component
            ->set('name', $over['name'] ?? 'Rahim Uddin')
            ->set('phone', $over['phone'] ?? '01712345678')
            ->set('address', $over['address'] ?? 'House 1, Road 2, Dhanmondi, Dhaka');
    }

    public function test_phone_normalizer_accepts_bd_mobile_formats(): void
    {
        foreach (['01712345678', '+8801712345678', '8801712345678', '01712-345678', '01712 345 678', '1712345678'] as $raw) {
            $this->assertSame('01712345678', PhoneNormalizer::normalize($raw), $raw);
            $this->assertTrue(PhoneNormalizer::isValid($raw), $raw);
        }

        foreach (['0171234567', '01212345678', '017123456789', 'abc', ''] as $raw) {
            $this->assertFalse(PhoneNormalizer::isValid($raw), $raw);
        }
    }

    public function test_popup_creates_cod_order_with_server_side_price_and_shipping(): void
    {
        $product = $this->makeProduct();

        $component = Livewire::test(OrderPopup::class)
            ->call('openPopup', $product->id, null, null, 2)
            ->assertSet('open', true)
            ->assertSet('unitPrice', 500.0)
            ->assertSet('qty', 2);

        $this->fillForm($component)
            ->set('email', 'rahim@example.com')
            // Tampered browser price must never reach the order
            ->set('unitPrice', 1)
            ->call('confirmCod')
            ->assertSet('placed', true);

        $order = Order::where('channel', 'cod')->firstOrFail();

        $this->assertSame('pending', $order->status);
        $this->assertSame('cod', $order->payment_method);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('01712345678', $order->phone);
        $this->assertSame('rahim@example.com', $order->email);
        $this->assertSame('inside', $order->delivery_zone);
        $this->assertEquals(1060.0, (float) $order->total_amount);

        $item = $order->items()->firstOrFail();
        $this->assertSame($product->name, $item->product_name);
        $this->assertSame(2, (int) $item->quantity);
        $this->assertEquals(500.0, (float) $item->unit_price);
    }

    public function test_outside_zone_uses_outside_delivery_charge(): void
    {
        $product = $this->makeProduct();

        $component = $this->fillForm(Livewire::test(OrderPopup::class)->call('openPopup', $product->id));

        $component->set('zone', 'outside')
            ->call('confirmCod')
            ->assertSet('placed', true);

        $order = Order::firstOrFail();

        $this->assertSame('outside', $order->delivery_zone);
        $this->assertEquals(120.0, (float) $order->delivery_charge);
        $this->assertEquals(620.0, (float) $order->total_amount);
    }

    public function test_whatsapp_channel_builds_wa_link(): void
    {
        $product = $this->makeProduct();

        $component = $this->fillForm(Livewire::test(OrderPopup::class)->call('openPopup', $product->id));

        $component->call('confirmWhatsApp')->assertSet('placed', true);

        $order = Order::firstOrFail();

        $this->assertSame('whatsapp', $order->channel);
        $this->assertStringStartsWith('https://wa.me/8801700000000?text=', $component->get('waUrl'));
        $this->assertSame($order->invoice_no, $component->get('placedInvoice'));
    }

    public function test_invalid_phone_is_rejected_and_no_order_created(): void
    {
        $product = $this->makeProduct();

        $component = $this->fillForm(
            Livewire::test(OrderPopup::class)->call('openPopup', $product->id),
            ['phone' => '01212345678']
        );

        $component->call('confirmCod')->assertHasErrors(['phone']);

        $this->assertSame(0, Order::count());
    }

    public function test_honeypot_submission_creates_nothing(): void
    {
        $product = $this->makeProduct();

        $component = $this->fillForm(Livewire::test(OrderPopup::class)->call('openPopup', $product->id));

        $component->set('website', 'http://spam.example')->call('confirmCod');

        $this->assertSame(0, Order::count());
    }

    public function test_rate_limiter_blocks_sixth_attempt(): void
    {
        $product = $this->makeProduct();

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('order-popup:127.0.0.1', 60);
        }

        $component = $this->fillForm(Livewire::test(OrderPopup::class)->call('openPopup', $product->id));

        $component->call('confirmCod')->assertDispatched('error');

        $this->assertSame(0, Order::count());
    }

    public function test_popup_does_not_open_when_disabled(): void
    {
        Settings::set('popup_order_enabled', '0');
        $product = $this->makeProduct();

        Livewire::test(OrderPopup::class)
            ->call('openPopup', $product->id)
            ->assertSet('open', false);
    }

    public function test_buy_button_falls_back_to_checkout_when_popup_disabled(): void
    {
        $product = $this->makeProduct();

        Settings::set('popup_order_enabled', '0');
        $off = (string) $this->blade(
            '<x-order-now-button :product="$product" label="Buy Now" />',
            ['product' => $product]
        );
        $this->assertStringContainsString('wire:click="buyNow(', $off);
        $this->assertStringNotContainsString('order-popup-open', $off);

        Settings::set('popup_order_enabled', '1');
        $on = (string) $this->blade(
            '<x-order-now-button :product="$product" label="Buy Now" />',
            ['product' => $product]
        );
        $this->assertStringContainsString('order-popup-open', $on);
        $this->assertStringNotContainsString('wire:click="buyNow(', $on);
    }

    public function test_order_settings_page_is_admin_guarded(): void
    {
        $this->get('/admin/settings/order')->assertRedirect(route('login'));
    }
}
