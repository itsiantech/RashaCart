<?php

namespace Tests\Feature;

use App\Livewire\Admin\Products\ProductCreate;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Settings;
use App\Models\VariantAttributeValue;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VariantSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function seedAttributes(): array
    {
        $color = Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'color_swatch']);
        $size = Attribute::create(['name' => 'Size', 'slug' => 'size', 'type' => 'button']);
        $red = AttributeValue::create(['attribute_id' => $color->id, 'value' => 'Red', 'color_hex' => '#FF0000']);
        $blue = AttributeValue::create(['attribute_id' => $color->id, 'value' => 'Blue', 'color_hex' => '#0000FF']);
        $m = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'M']);
        $l = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'L']);

        return [$color, $size, $red, $blue, $m, $l];
    }

    public function test_section_hidden_when_off_and_visible_when_on(): void
    {
        $this->seedAttributes();
        Settings::updateOrCreate(['key' => 'attribute_system'], ['value' => 0]);
        Settings::clearCache();

        Livewire::test(ProductCreate::class)->assertDontSee('Product Variations');

        Settings::updateOrCreate(['key' => 'attribute_system'], ['value' => 1]);
        Settings::clearCache();

        Livewire::test(ProductCreate::class)->assertSee('Product Variations');
    }

    public function test_generate_variants_without_duplicates(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();

        $component = Livewire::test(ProductCreate::class);
        $component->set('sku', 'TSHIRT');
        $component->set('sale_price', 500);
        $component->call('toggleGenAttr', $color->id);
        $component->call('toggleGenValue', $color->id, $red->id);
        $component->call('toggleGenValue', $color->id, $blue->id);
        $component->call('toggleGenAttr', $size->id);
        $component->call('toggleGenValue', $size->id, $m->id);
        $component->call('toggleGenValue', $size->id, $l->id);
        $component->call('generateVariants');

        $variants = $component->get('variants');
        $this->assertCount(4, $variants);
        $skus = array_column($variants, 'sku');
        $this->assertCount(4, array_unique($skus));
        $this->assertStringStartsWith('TSHIRT-', $skus[0]);

        // Regenerate → no duplicates added
        $component->call('generateVariants');
        $this->assertCount(4, $component->get('variants'));
    }

    public function test_order_rejects_foreign_variant_and_ignores_tampered_price(): void
    {
        $this->seedAttributes();

        $a = Product::factory()->create(['sale_price' => 500, 'price' => 600]);
        $b = Product::factory()->create(['sale_price' => 100, 'price' => 100]);
        $foreign = ProductVariant::create([
            'product_id' => $b->id, 'sku' => 'B-1', 'variant_price' => 100, 'stock' => 5,
        ]);

        $service = app(OrderService::class);
        $base = [
            'invoice_no' => 'INV-T1', 'total_amount' => 1, 'status' => 'pending',
            'payment_method' => 'cod', 'payment_status' => 'unpaid',
            'name' => 'T', 'phone' => '01700000000', 'shipping_address' => 'Dhaka',
            'delivery_zone' => 'inside', 'delivery_charge' => 0,
        ];

        // Tampered: variant belongs to another product
        try {
            $service->createOrder($base, [['id' => $a->id, 'variant_id' => $foreign->id, 'qty' => 1, 'price' => 1]]);
            $this->fail('Foreign variant was accepted.');
        } catch (\Exception $e) {
            $this->assertStringContainsString('ভুল variant', $e->getMessage());
        }

        // Tampered price is ignored: charged price comes from DB.
        // Variant snapshot (sku/name/image) is stored at purchase.
        $variant = ProductVariant::create([
            'product_id' => $a->id, 'sku' => 'A-RED-M', 'variant_price' => 550,
            'stock' => 10, 'image' => 'products/variants/red.jpg',
        ]);
        VariantAttributeValue::create([
            'product_variant_id' => $variant->id,
            'attribute_id' => Attribute::where('slug', 'color')->first()->id,
            'attribute_value_id' => AttributeValue::where('value', 'Red')->first()->id,
        ]);

        $order = $service->createOrder(
            array_merge($base, ['invoice_no' => 'INV-T2']),
            [['id' => $a->id, 'variant_id' => $variant->id, 'qty' => 2, 'price' => 1]]
        );

        $item = $order->fresh()->items->first();
        $this->assertEquals(1100, $order->fresh()->total_amount); // 550 x 2, not 1 x 2
        $this->assertEquals(550, $item->unit_price);
        $this->assertEquals('A-RED-M', $item->variant_sku);
        $this->assertEquals('Color: Red', $item->variant_name);
        $this->assertEquals('products/variants/red.jpg', $item->variant_image);
    }

    public function test_used_attribute_value_cannot_be_deleted(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();
        $product = Product::factory()->create();
        $variant = ProductVariant::create(['product_id' => $product->id, 'sku' => 'X-1', 'variant_price' => 10]);
        VariantAttributeValue::create([
            'product_variant_id' => $variant->id, 'attribute_id' => $color->id, 'attribute_value_id' => $red->id,
        ]);

        $this->assertTrue($red->fresh()->isUsed());
        $this->assertFalse($blue->fresh()->isUsed());
        $this->assertTrue($color->fresh()->isUsed());
    }

    public function test_wholesale_slab_price_applies_by_quantity(): void
    {
        $product = Product::factory()->create(['sale_price' => 500, 'price' => 600]);
        $variant = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'W-1', 'variant_price' => 500, 'stock' => 100,
        ]);
        \App\Models\VariantPriceSlab::create(['product_variant_id' => $variant->id, 'min_qty' => 10, 'price' => 480]);
        \App\Models\VariantPriceSlab::create(['product_variant_id' => $variant->id, 'min_qty' => 50, 'price' => 450]);

        $this->assertEquals(500, \App\Services\PriceResolver::resolve($product, $variant->fresh(), 1));
        $this->assertEquals(480, \App\Services\PriceResolver::resolve($product, $variant->fresh(), 10));
        $this->assertEquals(480, \App\Services\PriceResolver::resolve($product, $variant->fresh(), 49));
        $this->assertEquals(450, \App\Services\PriceResolver::resolve($product, $variant->fresh(), 50));
    }

    public function test_quick_add_value_from_product_page(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();

        $component = Livewire::test(ProductCreate::class);
        $component->call('openQuickAdd', $color->id);
        $component->set('quickValue', 'Yellow');
        $component->set('quickColor', '#FFFF00');
        $component->call('quickAddValue');

        $yellow = AttributeValue::where('value', 'Yellow')->first();
        $this->assertNotNull($yellow);
        $this->assertEquals('#FFFF00', $yellow->color_hex);
        $this->assertContains($yellow->id, $component->get('genValues')[$color->id]);
    }

    public function test_listing_filters_by_attribute_values(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();

        $p1 = Product::factory()->create(['name' => 'Red Shirt', 'status' => true]);
        $v1 = ProductVariant::create(['product_id' => $p1->id, 'sku' => 'RS-R', 'variant_price' => 100]);
        VariantAttributeValue::create([
            'product_variant_id' => $v1->id, 'attribute_id' => $color->id, 'attribute_value_id' => $red->id,
        ]);

        $p2 = Product::factory()->create(['name' => 'Blue Shirt', 'status' => true]);
        $v2 = ProductVariant::create(['product_id' => $p2->id, 'sku' => 'BS-B', 'variant_price' => 100]);
        VariantAttributeValue::create([
            'product_variant_id' => $v2->id, 'attribute_id' => $color->id, 'attribute_value_id' => $blue->id,
        ]);

        $component = Livewire::test(\App\Livewire\Frontend\Category\CategoryProduct::class);
        $component->set('selectedAttrs', [(string) $color->id => [$red->id]]);

        $names = $component->get('products')->pluck('name')->all();
        $this->assertContains('Red Shirt', $names);
        $this->assertNotContains('Blue Shirt', $names);
    }

    public function test_feeds_list_one_row_per_variant(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();
        $product = Product::factory()->create(['name' => 'Feed Shirt', 'status' => true]);
        $variant = ProductVariant::create(['product_id' => $product->id, 'sku' => 'FEED-R', 'variant_price' => 100]);
        VariantAttributeValue::create([
            'product_variant_id' => $variant->id, 'attribute_id' => $color->id, 'attribute_value_id' => $red->id,
        ]);

        $this->get(route('feed.google'))->assertOk()->assertSee('FEED-R', false);
        $this->get(route('feed.meta'))->assertOk()->assertSee('FEED-R', false);
    }

    public function test_generate_variants_works_on_edit_page(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();
        $product = Product::factory()->create(['sku' => 'EDITBASE', 'status' => true]);

        $component = Livewire::test(\App\Livewire\Admin\Products\ProductEdit::class, ['id' => $product->id]);
        $component->call('toggleGenAttr', $color->id);
        $component->call('toggleGenValue', $color->id, $red->id);
        $component->call('toggleGenValue', $color->id, $blue->id);
        $component->call('generateVariants');

        $variants = $component->get('variants');
        $this->assertCount(2, $variants);
        $this->assertStringStartsWith('EDITBASE-', $variants[0]['sku']);
    }

    public function test_reselecting_same_swatch_keeps_selection_and_orders(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();
        $product = Product::factory()->create(['sale_price' => 500, 'price' => 600, 'status' => true]);
        $variant = ProductVariant::create(['product_id' => $product->id, 'sku' => 'ST-R', 'variant_price' => 500, 'stock' => 5]);
        VariantAttributeValue::create([
            'product_variant_id' => $variant->id, 'attribute_id' => $color->id, 'attribute_value_id' => $red->id,
        ]);
        \App\Models\Inventory::create([
            'product_id' => $product->id, 'variant_id' => $variant->id,
            'type' => 'in', 'quantity' => 5, 'current_stock' => 5,
        ]);

        // Double-click / thumbnail-click after chip-click must NOT deselect
        $component = Livewire::test(\App\Livewire\Frontend\Product\SingleView::class, ['slug' => $product->slug]);
        $component->call('selectColor', 'Red');
        $component->call('selectColor', 'Red');
        $this->assertEquals('Red', $component->get('selectedColor'));

        $component->call('addToCart', $product->id);
        $cart = session('cart');
        $this->assertNotEmpty($cart);
        $this->assertEquals($variant->id, collect($cart)->first()['variant_id']);
    }

    public function test_swatch_gallery_images_survive_selection_roundtrip(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();
        $product = Product::factory()->create(['status' => true, 'thumbnail' => 'a.jpg']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'sku' => 'ST-R', 'variant_price' => 100, 'stock' => 5]);
        VariantAttributeValue::create([
            'product_variant_id' => $variant->id, 'attribute_id' => $color->id, 'attribute_value_id' => $red->id,
        ]);
        \App\Models\Inventory::create([
            'product_id' => $product->id, 'variant_id' => $variant->id,
            'type' => 'in', 'quantity' => 5, 'current_stock' => 5,
        ]);
        \App\Models\ProductImage::create(['product_id' => $product->id, 'path' => 'sw/red1.jpg', 'sort_order' => 0, 'attribute_value_id' => $red->id]);
        \App\Models\ProductImage::create(['product_id' => $product->id, 'path' => 'sw/common1.jpg', 'sort_order' => 1, 'attribute_value_id' => null]);

        $component = Livewire::test(\App\Livewire\Frontend\Product\SingleView::class, ['slug' => $product->slug]);
        $component->call('selectColor', 'Red');
        $html = $component->html();

        $this->assertStringContainsString('red1', $html);
        $this->assertStringContainsString('common1', $html);
    }

    protected function seedOptionProduct(): array
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();

        $product = Product::factory()->create([
            'sale_price' => 500, 'price' => 600, 'status' => true,
            'has_attributes' => true, 'has_image_options' => true, 'require_image_option' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'GOWN-RED-M', 'variant_price' => 500, 'stock' => 10,
        ]);
        VariantAttributeValue::create([
            'product_variant_id' => $variant->id, 'attribute_id' => $color->id, 'attribute_value_id' => $red->id,
        ]);
        VariantAttributeValue::create([
            'product_variant_id' => $variant->id, 'attribute_id' => $size->id, 'attribute_value_id' => $m->id,
        ]);
        $product->productAttributes()->sync([
            $color->id => ['is_required' => true],
            $size->id => ['is_required' => true],
        ]);
        \App\Models\Inventory::create([
            'product_id' => $product->id, 'variant_id' => $variant->id,
            'type' => 'in', 'quantity' => 10, 'current_stock' => 10,
        ]);

        $option = \App\Models\ImageOption::create([
            'product_id' => $product->id, 'label' => 'Red Front',
            'image' => 'opt/red.jpg', 'price' => 450, 'sort_order' => 0,
        ]);
        \App\Services\ImageOptionStock::setInitial($option, 3);

        $plain = \App\Models\ImageOption::create([
            'product_id' => $product->id, 'label' => 'Red Back',
            'image' => 'opt/red-back.jpg', 'sort_order' => 1,
        ]);

        return [$product, $variant, $option, $plain, $red, $m];
    }

    protected function orderBase(string $invoice): array
    {
        return [
            'invoice_no' => $invoice, 'total_amount' => 1, 'status' => 'pending',
            'payment_method' => 'cod', 'payment_status' => 'unpaid',
            'name' => 'T', 'phone' => '01700000000', 'shipping_address' => 'Dhaka',
            'delivery_zone' => 'inside', 'delivery_charge' => 0,
        ];
    }

    public function test_option_price_wins_and_stock_is_min(): void
    {
        [$product, $variant, $option] = $this->seedOptionProduct();

        // option.price (450) beats variant price (500); available = min(10, 3) = 3
        $combo = \App\Services\CombinationResolver::resolve($product, $variant->id, $option->id, 2);
        $this->assertEquals(450, $combo->unitPrice);
        $this->assertEquals(3, $combo->available);
        $this->assertEquals($product->sku.'-GOWN-RED-M-opt'.$option->id, $combo->contentId);

        // option without own price falls back to variant price
        $combo2 = \App\Services\CombinationResolver::resolve(
            $product, $variant->id,
            \App\Models\ImageOption::where('label', 'Red Back')->first()->id, 1
        );
        $this->assertEquals(500, $combo2->unitPrice);
        $this->assertEquals(10, $combo2->available); // no option ledger → variant stock
    }

    public function test_required_layers_reject_missing_selection(): void
    {
        [$product, $variant, $option] = $this->seedOptionProduct();

        try {
            \App\Services\CombinationResolver::resolve($product, null, $option->id, 1);
            $this->fail('Missing variant was accepted.');
        } catch (\App\Services\CombinationException $e) {
            $this->assertStringContainsString('সিলেক্ট করুন', $e->getMessage());
        }

        try {
            \App\Services\CombinationResolver::resolve($product, $variant->id, null, 1);
            $this->fail('Missing option was accepted.');
        } catch (\App\Services\CombinationException $e) {
            $this->assertStringContainsString('image option', $e->getMessage());
        }
    }

    public function test_foreign_option_is_rejected(): void
    {
        [$product, $variant, $option] = $this->seedOptionProduct();
        $other = Product::factory()->create();

        try {
            \App\Services\CombinationResolver::resolve($product, $variant->id, $other->id + 9999, 1);
            $this->fail('Foreign option was accepted.');
        } catch (\App\Services\CombinationException $e) {
            $this->assertStringContainsString('ভুল image option', $e->getMessage());
        }

        $foreignOption = \App\Models\ImageOption::create([
            'product_id' => $other->id, 'label' => 'X', 'image' => 'x.jpg',
        ]);
        try {
            \App\Services\CombinationResolver::resolve($product, $variant->id, $foreignOption->id, 1);
            $this->fail('Cross-product option was accepted.');
        } catch (\App\Services\CombinationException $e) {
            $this->assertStringContainsString('ভুল image option', $e->getMessage());
        }
    }

    public function test_order_deducts_both_ledgers_and_restores_on_cancel(): void
    {
        [$product, $variant, $option] = $this->seedOptionProduct();
        $service = app(\App\Services\OrderService::class);

        $order = $service->createOrder(
            $this->orderBase('INV-OPT-1'),
            [['id' => $product->id, 'variant_id' => $variant->id, 'image_option_id' => $option->id, 'qty' => 2, 'price' => 1]]
        );

        $this->assertEquals(900, $order->fresh()->total_amount); // 450 x 2 (option price, tampered price ignored)
        $item = $order->fresh()->items->first();
        $this->assertEquals($option->id, $item->image_option_id);
        $this->assertEquals('Red Front', $item->option_label);
        $this->assertEquals('opt/red.jpg', $item->option_image);

        // Double deduction: variant ledger 10 → 8, option ledger 3 → 1
        $this->assertEquals(8, \App\Services\InventoryService::currentStock($product->id, $variant->id));
        $this->assertEquals(1, \App\Services\ImageOptionStock::currentStock($option->id));

        // Re-order same combination twice more must not double-deduct (idempotent OUT)
        \App\Services\ImageOptionStock::ensureOrderItemOut($item->fresh());
        $this->assertEquals(1, \App\Services\ImageOptionStock::currentStock($option->id));

        // Cancel → both restored
        $order->update(['status' => 'cancelled']);
        $this->assertEquals(10, \App\Services\InventoryService::currentStock($product->id, $variant->id));
        $this->assertEquals(3, \App\Services\ImageOptionStock::currentStock($option->id));

        // Second cancel must not double-restore (idempotent RETURN)
        \App\Services\ImageOptionStock::ensureOrderItemReturn($item->fresh());
        $this->assertEquals(3, \App\Services\ImageOptionStock::currentStock($option->id));
    }

    public function test_oversell_is_blocked(): void
    {
        [$product, $variant, $option] = $this->seedOptionProduct();
        $service = app(\App\Services\OrderService::class);

        try {
            $service->createOrder(
                $this->orderBase('INV-OPT-2'),
                [['id' => $product->id, 'variant_id' => $variant->id, 'image_option_id' => $option->id, 'qty' => 5, 'price' => 1]]
            );
            $this->fail('Oversell was accepted.');
        } catch (\Exception $e) {
            $this->assertStringContainsString('স্টক', $e->getMessage());
        }

        // Nothing was deducted
        $this->assertEquals(10, \App\Services\InventoryService::currentStock($product->id, $variant->id));
        $this->assertEquals(3, \App\Services\ImageOptionStock::currentStock($option->id));
    }

    public function test_single_layer_and_no_layer_products_still_work(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();
        $service = app(\App\Services\OrderService::class);

        // Variant-only (no options)
        $simple1 = Product::factory()->create(['sale_price' => 200, 'price' => 200, 'has_image_options' => false]);
        $order1 = $service->createOrder(
            $this->orderBase('INV-S1'),
            [['id' => $simple1->id, 'qty' => 1, 'price' => 1]]
        );
        $this->assertEquals(200, $order1->fresh()->total_amount);

        // Plain product, no layers at all
        $plain = Product::factory()->create([
            'sale_price' => 100, 'price' => 100,
            'has_attributes' => false, 'has_image_options' => false,
        ]);
        $combo = \App\Services\CombinationResolver::resolve($plain, null, null, 1);
        $this->assertEquals(100, $combo->unitPrice);
    }

    public function test_option_only_product_orders_without_variant(): void
    {
        $product = Product::factory()->create([
            'sale_price' => 300, 'price' => 300,
            'has_attributes' => false, 'has_image_options' => true, 'require_image_option' => true,
        ]);
        $option = \App\Models\ImageOption::create([
            'product_id' => $product->id, 'label' => 'Mini',
            'image' => 'opt/mini.jpg', 'price' => 250,
        ]);
        \App\Services\ImageOptionStock::setInitial($option, 4);

        $combo = \App\Services\CombinationResolver::resolve($product, null, $option->id, 2);
        $this->assertEquals(250, $combo->unitPrice);
        $this->assertEquals(4, $combo->available);
        $this->assertEquals($product->sku.'-opt'.$option->id, $combo->contentId);

        $order = app(\App\Services\OrderService::class)->createOrder(
            $this->orderBase('INV-OPT-3'),
            [['id' => $product->id, 'image_option_id' => $option->id, 'qty' => 2, 'price' => 1]]
        );
        $this->assertEquals(500, $order->fresh()->total_amount);
        $this->assertEquals(2, \App\Services\ImageOptionStock::currentStock($option->id));
    }

    public function test_layer_flags_persist_through_admin_save(): void
    {
        $cat = \App\Models\Category::create(['name' => 'Cat', 'slug' => 'cat-flag', 'status' => 1]);
        $product = Product::factory()->create(['status' => true, 'category_id' => $cat->id]);

        // Edit page: tick both layers + one option → flags must survive save + reload
        $t = Livewire::test(\App\Livewire\Admin\Products\ProductEdit::class, ['id' => $product->id]);
        $t->set('has_attributes', false);
        $t->set('has_image_options', true);
        $t->set('require_image_option', true);
        $t->set('imageOptions', [[
            'key' => 'new_0', 'id' => null, 'label' => 'Yellow', 'image' => 'x.jpg',
            'preview' => null, 'price' => 500, 'sale_price' => 600, 'stock' => 100,
            'has_ledger' => false, 'sort_order' => 0, 'is_active' => true,
        ]]);
        $t->call('update');

        $fresh = $product->fresh();
        $this->assertFalse((bool) $fresh->has_attributes);
        $this->assertTrue((bool) $fresh->has_image_options);
        $this->assertTrue((bool) $fresh->require_image_option);
        $this->assertEquals(1, \App\Models\ImageOption::where('product_id', $product->id)->count());

        // Reload (mount) must show the ticks
        $t2 = Livewire::test(\App\Livewire\Admin\Products\ProductEdit::class, ['id' => $product->id]);
        $this->assertFalse((bool) $t2->get('has_attributes'));
        $this->assertTrue((bool) $t2->get('has_image_options'));
    }

    public function test_regenerate_skips_saved_variants_and_blocks_db_duplicates(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();
        $cat = \App\Models\Category::create(['name' => 'Cat2', 'slug' => 'cat-2', 'status' => 1]);
        $product = Product::factory()->create(['sku' => 'DUPBASE', 'status' => true, 'category_id' => $cat->id]);

        // Saved variant S (as if generated + saved earlier)
        $saved = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'DUPBASE-S', 'variant_price' => 100, 'stock' => 5,
        ]);
        VariantAttributeValue::create([
            'product_variant_id' => $saved->id, 'attribute_id' => $size->id, 'attribute_value_id' => $m->id,
        ]);

        $t = Livewire::test(\App\Livewire\Admin\Products\ProductEdit::class, ['id' => $product->id]);
        $t->call('toggleGenAttr', $size->id);
        $t->call('toggleGenValue', $size->id, $m->id);
        $t->call('toggleGenValue', $size->id, $l->id);
        $t->call('generateVariants');

        $variants = $t->get('variants');
        // M skipped (already saved), only L added
        $this->assertCount(2, $variants);
        $skus = array_column($variants, 'sku');
        $this->assertContains('DUPBASE-L', $skus);
        $this->assertNotContains('DUPBASE-S', array_column(array_filter($variants, fn ($r) => ! is_numeric($r['id'] ?? null)), 'sku'));

        // Forced duplicate row (same SKU as saved variant) → Bangla error, no SQL crash
        $rows = $t->get('variants');
        $rows[] = [
            'id' => 'new_dup', 'sku' => 'DUPBASE-S', 'variant_price' => 100, 'sale_price' => 100,
            'stock' => 0, 'low_stock_threshold' => 5, 'is_active' => true, 'is_default' => false,
            'image' => null,
            'values' => [['attribute_id' => $size->id, 'attribute_value_id' => $l->id]],
        ];
        $t->set('variants', $rows);
        $t->call('update');

        $this->assertEquals(1, ProductVariant::where('product_id', $product->id)->where('sku', 'DUPBASE-S')->count());
    }

    public function test_empty_variant_price_falls_back_to_product_price(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();
        $product = Product::factory()->create(['sale_price' => 600, 'price' => 700, 'status' => true]);
        // NULL price (auto) + explicit 0 price side by side — 0 stays 0 (strict)
        $auto = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'AUTO-1', 'variant_price' => null, 'stock' => 5,
        ]);
        $zero = ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'ZERO-1', 'variant_price' => 0, 'stock' => 5,
        ]);

        $this->assertEquals(600, \App\Services\PriceResolver::resolve($product->fresh(), $auto->fresh(), 1));
        $this->assertEquals(0, \App\Services\PriceResolver::resolve($product->fresh(), $zero->fresh(), 1));

        // Generate leaves prices empty (auto)
        $t = Livewire::test(ProductCreate::class);
        $t->set('sku', 'AB');
        $t->call('toggleGenAttr', $color->id);
        $t->call('toggleGenValue', $color->id, $red->id);
        $t->call('generateVariants');
        $rows = $t->get('variants');
        $this->assertNull($rows[0]['variant_price']);
        $this->assertNull($rows[0]['sale_price']);

        // Bulk helpers
        $t->call('bulkSetVariantPrice', 550);
        $this->assertEquals(550, $t->get('variants')[0]['variant_price']);
        $t->call('autoVariantPrices');
        $this->assertNull($t->get('variants')[0]['variant_price']);
        $t->set('stock', 42);
        $t->call('copyProductStockToVariants');
        $this->assertEquals(42, $t->get('variants')[0]['stock']);
    }

    public function test_empty_option_stock_follows_variant_and_zero_means_sold_out(): void
    {
        [$color, $size, $red, $blue, $m, $l] = $this->seedAttributes();
        $product = Product::factory()->create([
            'sale_price' => 500, 'price' => 600, 'status' => true,
            'has_attributes' => false, 'has_image_options' => true, 'require_image_option' => false,
        ]);
        \App\Models\Inventory::create([
            'product_id' => $product->id, 'variant_id' => null,
            'type' => 'in', 'quantity' => 7, 'current_stock' => 7,
        ]);

        $follow = \App\Models\ImageOption::create([
            'product_id' => $product->id, 'label' => 'Follow', 'image' => 'f.jpg',
        ]); // no ledger → follows product stock (7)
        $soldout = \App\Models\ImageOption::create([
            'product_id' => $product->id, 'label' => 'Gone', 'image' => 'g.jpg',
        ]);
        \App\Services\ImageOptionStock::setInitial($soldout, 0); // explicit 0 → sold out

        $c1 = \App\Services\CombinationResolver::resolve($product, null, $follow->id, 1);
        $this->assertEquals(500, $c1->unitPrice); // no own price → product price
        $this->assertEquals(7, $c1->available);

        try {
            \App\Services\CombinationResolver::resolve($product, null, $soldout->id, 1);
            $this->fail('Zero-stock option was accepted.');
        } catch (\App\Services\CombinationException $e) {
            $this->assertStringContainsString('স্টক', $e->getMessage());
        }
    }

    public function test_clearing_option_stock_drops_ledger_unless_open_orders(): void
    {
        $product = Product::factory()->create(['status' => true, 'has_image_options' => true]);
        $option = \App\Models\ImageOption::create([
            'product_id' => $product->id, 'label' => 'X', 'image' => 'x.jpg',
        ]);
        \App\Services\ImageOptionStock::setInitial($option, 10);
        $this->assertTrue(\App\Services\ImageOptionStock::hasOwnStock($option->id));

        // Simulate admin clearing the stock field on Edit page
        $cat = \App\Models\Category::create(['name' => 'Cat3', 'slug' => 'cat-3', 'status' => 1]);
        $product->update(['category_id' => $cat->id]);
        $t = Livewire::test(\App\Livewire\Admin\Products\ProductEdit::class, ['id' => $product->id]);
        $rows = $t->get('imageOptions');
        $this->assertEquals(10, $rows[0]['stock']);
        $rows[0]['stock'] = '';
        $t->set('imageOptions', $rows);
        $t->call('update');

        $this->assertFalse(\App\Services\ImageOptionStock::hasOwnStock($option->id));
        // ...and the option now follows product stock instead of 0
        $this->assertEquals('', $t->get('imageOptions')[0]['stock'] ?? null);
    }
}
