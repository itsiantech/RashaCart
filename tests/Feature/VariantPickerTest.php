<?php

namespace Tests\Feature;

use App\Livewire\Frontend\OrderPopup;
use App\Livewire\Frontend\Product\SingleView;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Settings;
use App\Models\VariantAttributeValue;
use App\Services\VariantPickerData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VariantPickerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Settings::set('popup_order_enabled', '1');
        Settings::set('popup_order_whatsapp', '0');
        Settings::set('popup_order_default_zone', 'inside');
        Settings::set('dc_inside', '60');
        Settings::set('dc_outside', '120');
        Settings::set('currency', '৳');
        Settings::set('add_to_cart_enabled', '1');
        Settings::set('buy_now_enabled', '1');
        Settings::set('attribute_system', '1');
    }

    /**
     * Product with Color × Size variants; Blue/L has no ledger stock.
     *
     * @return array{0: Product, 1: array<string, ProductVariant>}
     */
    protected function makeVariantProduct(): array
    {
        $color = Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'color_swatch']);
        $size = Attribute::create(['name' => 'Size', 'slug' => 'size', 'type' => 'button']);

        $red = AttributeValue::create(['attribute_id' => $color->id, 'value' => 'Red', 'color_hex' => '#FF0000']);
        $blue = AttributeValue::create(['attribute_id' => $color->id, 'value' => 'Blue', 'color_hex' => '#0000FF']);
        $m = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'M']);
        $l = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'L']);

        $product = Product::factory()->create([
            'status' => true,
            'price' => 600,
            'sale_price' => 500,
            'stock' => 20,
            'has_attributes' => true,
        ]);

        $variants = [];
        foreach ([['Red', 'M', $red, $m, 5], ['Red', 'L', $red, $l, 3], ['Blue', 'M', $blue, $m, 4], ['Blue', 'L', $blue, $l, 0]] as [$colorValue, $sizeValue, $colorRow, $sizeRow, $stock]) {
            $key = $colorValue.'-'.$sizeValue;
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => 'VP-'.$key,
                'variant_price' => 500,
                'stock' => $stock,
            ]);

            VariantAttributeValue::create([
                'product_variant_id' => $variant->id,
                'attribute_id' => $color->id,
                'attribute_value_id' => $colorRow->id,
            ]);
            VariantAttributeValue::create([
                'product_variant_id' => $variant->id,
                'attribute_id' => $size->id,
                'attribute_value_id' => $sizeRow->id,
            ]);

            Inventory::create([
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'type' => 'in',
                'quantity' => max(1, $stock),
                'current_stock' => $stock,
            ]);

            $variants[$key] = $variant;
        }

        // Blue/L is actually sold out: reverse the fake "in" row.
        Inventory::create([
            'product_id' => $product->id,
            'variant_id' => $variants['Blue-L']->id,
            'type' => 'out',
            'quantity' => 1,
            'current_stock' => 0,
        ]);

        return [$product, $variants];
    }

    protected function makeSimpleProduct(): Product
    {
        return Product::factory()->create([
            'status' => true,
            'price' => 500,
            'sale_price' => 500,
            'stock' => 10,
        ]);
    }

    public function test_add_to_cart_opens_picker_for_variant_product(): void
    {
        [$product] = $this->makeVariantProduct();

        $component = Livewire::test(SingleView::class, ['slug' => $product->slug]);

        $component->assertSee('openCartPopup')
            ->call('openCartPopup')
            ->assertSet('cartPopupOpen', true)
            ->assertSee('আপনার পছন্দ বেছে নিন')
            ->assertSee('Color')
            ->assertSee('Size');
    }

    public function test_simple_product_uses_direct_add_to_cart(): void
    {
        $product = $this->makeSimpleProduct();

        $component = Livewire::test(SingleView::class, ['slug' => $product->slug]);

        $this->assertFalse($component->instance()->pickerRequired());
        $this->assertStringNotContainsString('wire:click="openCartPopup"', $component->html());
    }

    public function test_order_settings_admin_can_turn_variant_popup_off(): void
    {
        Livewire::test(\App\Livewire\Admin\Settings\OrderSettings::class)
            ->assertSee('Variant picker popup')
            ->set('variant_popup_enabled', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('0', (string) setting('variant_popup_enabled'));

        Livewire::test(\App\Livewire\Admin\Settings\OrderSettings::class)
            ->set('variant_popup_enabled', true)
            ->call('save');

        $this->assertSame('1', (string) setting('variant_popup_enabled'));
    }

    public function test_order_settings_toggle_disables_picker_on_product_page(): void
    {
        Settings::set('variant_popup_enabled', '0');
        [$product] = $this->makeVariantProduct();

        $component = Livewire::test(SingleView::class, ['slug' => $product->slug]);

        $this->assertFalse($component->instance()->pickerRequired());
        $this->assertStringNotContainsString('wire:click="openCartPopup"', $component->html());
        $this->assertStringContainsString('wire:click="addToCart('.$product->id.')"', $component->html());
    }

    public function test_order_settings_toggle_disables_picker_in_order_popup(): void
    {
        Settings::set('variant_popup_enabled', '0');
        [$product, $variants] = $this->makeVariantProduct();

        Livewire::test(OrderPopup::class)
            ->call('openPopup', $product->id, $variants['Red-M']->id)
            ->assertSet('showPicker', false)
            ->assertSet('variantId', $variants['Red-M']->id)
            ->assertSee('ক্যাশ অন ডেলিভারিতে অর্ডার করুন');
    }

    public function test_picker_prefills_existing_page_selection(): void
    {
        [$product] = $this->makeVariantProduct();

        $component = Livewire::test(SingleView::class, ['slug' => $product->slug]);
        $component->call('selectColor', 'Red');
        $component->call('openCartPopup');

        $this->assertTrue($component->get('cartPopupOpen'));
        $this->assertSame('Red', $component->get('cartPicks')['Color']);
        $this->assertArrayNotHasKey('Size', $component->get('cartPicks'));
    }

    public function test_add_to_cart_skips_picker_when_page_selection_is_complete(): void
    {
        [$product, $variants] = $this->makeVariantProduct();

        $component = Livewire::test(SingleView::class, ['slug' => $product->slug]);
        $component->call('selectColor', 'Red');
        $component->call('selectSize', 'M');
        $component->call('openCartPopup');

        $this->assertFalse($component->get('cartPopupOpen'));

        $item = collect(session('cart'))->first();

        $this->assertSame($variants['Red-M']->id, $item['variant_id']);
    }

    public function test_card_add_to_cart_opens_quick_add_popup(): void
    {
        [$product] = $this->makeVariantProduct();
        $product->load('variants.variantAttributeValues.attribute', 'variants.variantAttributeValues.attributeValue');

        $html = view('livewire.frontend.layouts.partial.product-card', ['product' => $product])->render();

        $this->assertStringContainsString('quick-add-open', $html);
        $this->assertStringNotContainsString('wire:click="addToCart('.$product->id.')"', $html);
    }

    public function test_card_falls_back_to_product_link_when_variant_popup_is_off(): void
    {
        Settings::set('variant_popup_enabled', '0');
        [$product] = $this->makeVariantProduct();
        $product->load('variants.variantAttributeValues.attribute', 'variants.variantAttributeValues.attributeValue');

        $html = view('livewire.frontend.layouts.partial.product-card', ['product' => $product])->render();

        $this->assertStringNotContainsString('quick-add-open', $html);
        $this->assertStringContainsString(route('frontend.product.single.view', $product->slug), $html);
    }

    public function test_quick_add_popup_adds_picked_variant_from_card(): void
    {
        [$product, $variants] = $this->makeVariantProduct();

        Livewire::test(\App\Livewire\Frontend\QuickAddPopup::class)
            ->call('openQuickAdd', $product->id)
            ->assertSet('open', true)
            ->call('selectPick', 'Color', 'Blue')
            ->call('selectPick', 'Size', 'M')
            ->call('confirmQuickAdd')
            ->assertSet('open', false);

        $item = collect(session('cart'))->first();

        $this->assertSame($variants['Blue-M']->id, $item['variant_id']);
    }

    public function test_quick_add_popup_blocks_sold_out_combination(): void
    {
        [$product] = $this->makeVariantProduct();

        $component = Livewire::test(\App\Livewire\Frontend\QuickAddPopup::class)
            ->call('openQuickAdd', $product->id)
            ->call('selectPick', 'Color', 'Blue')
            ->call('selectPick', 'Size', 'L')
            ->call('confirmQuickAdd');

        $this->assertTrue($component->get('open'));
        $this->assertNotSame('', $component->get('pickerError'));
        $this->assertEmpty(session('cart'));
    }

    public function test_quick_add_popup_ignores_event_when_disabled(): void
    {
        Settings::set('variant_popup_enabled', '0');
        [$product] = $this->makeVariantProduct();

        Livewire::test(\App\Livewire\Frontend\QuickAddPopup::class)
            ->call('openQuickAdd', $product->id)
            ->assertSet('open', false);
    }

    public function test_picking_in_popup_adds_the_exact_variant_to_cart(): void
    {
        [$product, $variants] = $this->makeVariantProduct();

        $component = Livewire::test(SingleView::class, ['slug' => $product->slug]);
        $component->call('openCartPopup');
        $component->call('selectPickerValue', 'Color', 'Red');
        $component->call('selectPickerValue', 'Size', 'L');

        $component->assertSet('selectedVariant', $variants['Red-L']->id);

        $component->call('confirmCartPopup')->assertSet('cartPopupOpen', false);

        $item = collect(session('cart'))->first();

        $this->assertSame($variants['Red-L']->id, $item['variant_id']);
        $this->assertEquals(500, $item['price']);
        $this->assertSame('Red / L', $item['variant_label']);
    }

    public function test_out_of_stock_value_is_disabled_in_picker(): void
    {
        [$product] = $this->makeVariantProduct();

        $component = Livewire::test(SingleView::class, ['slug' => $product->slug]);
        $component->call('openCartPopup');

        $data = $component->get('cartPickerData');

        // Nothing picked yet: every value is reachable through some live variant.
        $this->assertTrue(VariantPickerData::valueAvailable($data, [], 'Color', 'Blue'));
        $this->assertTrue(VariantPickerData::valueAvailable($data, [], 'Size', 'L'));

        // Once Blue is picked, L is dead (Blue/L has no stock).
        $this->assertFalse(VariantPickerData::valueAvailable($data, ['Color' => 'Blue'], 'Size', 'L'));
        $this->assertTrue(VariantPickerData::valueAvailable($data, ['Color' => 'Blue'], 'Size', 'M'));

        // Once L is picked, Blue is dead.
        $this->assertFalse(VariantPickerData::valueAvailable($data, ['Size' => 'L'], 'Color', 'Blue'));
        $this->assertTrue(VariantPickerData::valueAvailable($data, ['Size' => 'L'], 'Color', 'Red'));
    }

    public function test_order_popup_asks_for_variant_first(): void
    {
        [$product] = $this->makeVariantProduct();

        $component = Livewire::test(OrderPopup::class)
            ->call('openPopup', $product->id)
            ->assertSet('open', true)
            ->assertSet('showPicker', true)
            ->assertSet('variantId', null)
            ->assertSee('চালিয়ে যান');

        // Cannot submit while the picker is still open.
        $component->call('confirmCod')->assertSet('placed', false);
        $this->assertSame(0, Order::count());
    }

    public function test_order_popup_picked_variant_lands_in_the_order(): void
    {
        [$product, $variants] = $this->makeVariantProduct();

        $component = Livewire::test(OrderPopup::class)
            ->call('openPopup', $product->id)
            ->call('selectPick', 'Color', 'Blue')
            ->call('selectPick', 'Size', 'M')
            ->assertSet('variantId', $variants['Blue-M']->id)
            ->assertSet('unitPrice', 500.0)
            ->call('continueToForm')
            ->assertSet('showPicker', false);

        $component
            ->set('name', 'Rahim Uddin')
            ->set('phone', '01712345678')
            ->set('address', 'House 1, Road 2, Dhanmondi, Dhaka')
            ->call('confirmCod')
            ->assertSet('placed', true);

        $order = Order::firstOrFail();
        $item = $order->items->first();

        $this->assertSame($variants['Blue-M']->id, (int) $item->product_variant_id);
        $this->assertEquals(500, $item->unit_price);
        $this->assertEquals(560, (float) $order->total_amount); // 500 + 60 inside
    }

    public function test_order_popup_skips_picker_when_variant_already_chosen(): void
    {
        [$product, $variants] = $this->makeVariantProduct();

        Livewire::test(OrderPopup::class)
            ->call('openPopup', $product->id, $variants['Red-M']->id)
            ->assertSet('showPicker', false)
            ->assertSet('variantId', $variants['Red-M']->id)
            ->assertSee('ক্যাশ অন ডেলিভারিতে অর্ডার করুন');
    }

    public function test_order_popup_shows_reason_for_unavailable_combination(): void
    {
        [$product] = $this->makeVariantProduct();

        $component = Livewire::test(OrderPopup::class)
            ->call('openPopup', $product->id)
            ->call('selectPick', 'Color', 'Blue')
            ->call('selectPick', 'Size', 'L')
            ->call('continueToForm')
            ->assertSet('showPicker', true);

        $this->assertNotSame('', $component->get('pickerError'));
    }
}
