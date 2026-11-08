<?php

namespace Tests\Feature\Addons;

use Addons\PartialPayment\Console\ExpireAdvanceOrders;
use Addons\PartialPayment\Livewire\Admin\PaymentsPanel;
use Addons\PartialPayment\Livewire\Admin\ProductRuleForm;
use Addons\PartialPayment\Livewire\Admin\RefundsIndex;
use Addons\PartialPayment\Models\OrderPlan;
use Addons\PartialPayment\Models\Payment;
use Addons\PartialPayment\Models\ProductRule;
use Addons\PartialPayment\Services\PlanCalculator;
use App\Addons\AddonManager;
use App\Livewire\Admin\Order\OrderIndex;
use App\Livewire\Frontend\OrderPopup;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Settings;
use App\Models\User;
use App\Support\Hooks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

class PartialPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Hooks::flush();
        OrderPlan::flushMemo();
        RateLimiter::clear('order-popup:127.0.0.1');

        Settings::set('popup_order_enabled', '1');
        Settings::set('popup_order_whatsapp', '1');
        Settings::set('dc_inside', '60');
        Settings::set('dc_outside', '120');
        Settings::set('currency', '৳');
        Settings::set('contact_phone', '01700000000');
    }

    protected function tearDown(): void
    {
        OrderPlan::flushMemo();
        Hooks::flush();

        parent::tearDown();
    }

    protected function bootAddon(): AddonManager
    {
        config(['addons.path' => base_path('addons')]);
        $this->app->forgetInstance(AddonManager::class);

        $manager = $this->app->make(AddonManager::class);
        $manager->registerAutoloader();
        $manager->install('PartialPayment');
        $manager->activate('PartialPayment');
        $manager->boot();

        return $manager;
    }

    protected function makeProduct(array $over = []): Product
    {
        $product = Product::factory()->create(array_merge([
            'status' => true,
            'price' => 1000,
            'sale_price' => 1000,
        ], $over));

        // Sellable stock lives in the inventory ledger: Product::$attributes has no stock column.
        $stock = (int) ($over['stock'] ?? 30);

        Inventory::create([
            'product_id' => $product->id,
            'variant_id' => null,
            'type' => 'in',
            'quantity' => $stock,
            'current_stock' => $stock,
        ]);

        return $product;
    }

    protected function adminUser(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    /** Open the popup for a product. The customer's own fields come second: openPopup() wipes them. */
    protected function openFor(Product $product, int $qty = 1, bool $fill = true): Testable
    {
        $component = Livewire::test(OrderPopup::class)
            ->call('openPopup', $product->id, null, null, $qty);

        return $fill
            ? $component
                ->set('name', 'Rahim Uddin')
                ->set('phone', '01712345678')
                ->set('address', 'House 1, Road 2, Dhanmondi, Dhaka')
            : $component;
    }

    protected function payAdvance(Testable $component, string $trx = 'BK9A8S7D6F'): Testable
    {
        return $component->set('addon', [
            'method' => 'bkash',
            'sender_number' => '01712345678',
            'trx_id' => $trx,
        ]);
    }

    /** Fill the form, walk the gate, hand over the TrxID, submit. */
    protected function placeAdvanceOrder(Product $product, string $trx, int $qty = 1): Testable
    {
        return $this->payAdvance($this->openFor($product, $qty)->call('confirmCod'), $trx)
            ->call('confirmCod');
    }

    protected function addonNumber(): void
    {
        Settings::set('pp_bkash_number', '01700000001');
        Settings::clearCache();
    }

    // ─── install / schema ────────────────────────────────────────────────────

    public function test_install_creates_only_its_own_tables_and_seeds_defaults(): void
    {
        $this->bootAddon();

        foreach (['pp_product_rules', 'pp_order_plans', 'pp_payments', 'pp_activities'] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table.' should exist');
        }

        // The core orders table keeps its shape: nothing added, no new status column.
        $columns = Schema::getColumnListing('orders');
        $this->assertFalse(collect($columns)->contains(fn ($c) => str_starts_with($c, 'pp_')));
        $this->assertNotContains('awaiting_advance', $columns);

        if (DB::connection()->getDriverName() === 'mysql') {
            $status = collect(Schema::getColumns('orders'))->firstWhere('name', 'status');
            $this->assertStringContainsString('enum', strtolower((string) $status['type']));
            $this->assertStringNotContainsString('awaiting_advance', (string) $status['type']);
        }

        $this->assertSame('total', setting('pp_base'));
        $this->assertSame('24', (string) setting('pp_expiry_hours'));
        $this->assertNotSame('', (string) setting('pp_instructions'));
    }

    // ─── calculation ─────────────────────────────────────────────────────────

    public function test_percentage_advance_rounds_up_to_whole_taka(): void
    {
        $rule = new ProductRule(['advance_type' => 'percent', 'advance_value' => 30]);

        // 30% of 1,960 = 588 exactly.
        $this->assertSame(588.0, PlanCalculator::advanceAmount(1960, $rule));
        // 33.3% of 1,001 = 333.33 -> rounded up, never down.
        $this->assertSame(334.0, PlanCalculator::advanceAmount(1001, new ProductRule(['advance_type' => 'percent', 'advance_value' => 33.3])));
        // An advance is always at least Tk 1.
        $this->assertSame(1.0, PlanCalculator::advanceAmount(10, new ProductRule(['advance_type' => 'percent', 'advance_value' => 0.5])));
        // ... and never more than the amount it is taken from.
        $this->assertSame(5.0, PlanCalculator::advanceAmount(5, new ProductRule(['advance_type' => 'percent', 'advance_value' => 99])));
    }

    public function test_fixed_claim_never_exceeds_the_amount_it_is_taken_from(): void
    {
        $rule = new ProductRule(['advance_type' => 'fixed', 'advance_value' => 5000]);

        // Whole-price demands are refused: the order stays plain cash on delivery.
        $this->assertSame([], PlanCalculator::build($rule, 1000, 60));
        // Same fixed value against the subtotal base still leaves the delivery due.
        Settings::set('pp_base', 'subtotal');
        $plan = PlanCalculator::build($rule, 1000, 60);
        $this->assertSame(1000.0, $plan['advance']);
        $this->assertSame(60.0, $plan['due']);
    }

    public function test_base_option_changes_the_percentage_target(): void
    {
        $rule = new ProductRule(['advance_type' => 'percent', 'advance_value' => 10]);

        Settings::set('pp_base', 'total');
        $this->assertSame(106.0, PlanCalculator::build($rule, 1000, 60)['advance']);

        Settings::set('pp_base', 'subtotal');
        $this->assertSame(100.0, PlanCalculator::build($rule, 1000, 60)['advance']);
    }

    // ─── popup ───────────────────────────────────────────────────────────────

    public function test_popup_shows_advance_rows_and_a_payment_step(): void
    {
        $this->bootAddon();
        $this->addonNumber();

        $product = $this->makeProduct(['price' => 1000, 'sale_price' => 1000]);
        ProductRule::create(['product_id' => $product->id, 'advance_type' => 'percent', 'advance_value' => 30]);

        $component = $this->openFor($product);

        // 30% of (1,000 + 60) = 318, due 742.
        $component->assertSee(__('partial-payment::messages.pay_now'), false);
        $component->assertSee('318', false);
        $component->assertSee('742', false);

        $component->call('confirmCod')
            ->assertSet('step', 'payment')
            ->assertSee(__('partial-payment::messages.trx'), false)
            ->assertSee('01700000001', false);

        $this->assertSame(0, Order::count(), 'The gate must not create an order while the advance is unpaid');
    }

    public function test_product_without_a_rule_keeps_the_plain_cod_popup(): void
    {
        $this->bootAddon();

        $product = $this->makeProduct();

        Livewire::test(OrderPopup::class)
            ->call('openPopup', $product->id, null, null, 1)
            ->assertDontSee(__('partial-payment::messages.pay_now'), false)
            ->set('name', 'Rahim Uddin')
            ->set('phone', '01712345678')
            ->set('address', 'House 1, Road 2, Dhanmondi, Dhaka')
            ->call('confirmCod')
            ->assertSet('step', 'details')
            ->assertSet('placed', true);

        $this->assertSame(1, Order::count());
        $this->assertSame(0, OrderPlan::count());
        $this->assertSame(1060.0, (float) Hooks::filter('order.courier_cod_amount', 1060, Order::first()));
    }

    public function test_payment_is_recorded_from_server_prices_not_from_the_browser(): void
    {
        $this->bootAddon();
        $this->addonNumber();

        $product = $this->makeProduct(['price' => 1000, 'sale_price' => 1000]);
        ProductRule::create(['product_id' => $product->id, 'advance_type' => 'percent', 'advance_value' => 30]);

        $component = $this->openFor($product, 2);

        // 30% of (2,000 + 60) = 618, due 1,442.
        $component->call('confirmCod')->assertSet('step', 'payment');

        // A tampered claim on the same payload must be ignored.
        $component->set('addon', [
            'method' => 'bkash',
            'sender_number' => '01712345678',
            'trx_id' => 'BK1111111111',
            'amount' => 1,
        ])->call('confirmCod')->assertSet('placed', true);

        $order = Order::first();
        $plan = OrderPlan::forOrder((int) $order->id);

        $this->assertNotNull($plan);
        $this->assertSame(618.0, (float) $plan->advance_amount);
        $this->assertSame(1442.0, (float) $plan->due_amount);
        $this->assertSame(OrderPlan::PENDING, $plan->payment_status);

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertSame('advance', $payment->type);
        $this->assertSame('pending', $payment->status);
        $this->assertSame(618.0, (float) $payment->amount, 'The browser claim must never reach the ledger');
        $this->assertSame('BK1111111111', $payment->trx_id);

        // The success panel tells the customer what was paid and what is left.
        $component->assertSee(__('partial-payment::messages.success_advance', ['amount' => '৳ 618']), false);
        $component->assertSee(__('partial-payment::messages.success_due', ['amount' => '৳ 1,442']), false);
    }

    public function test_sender_number_and_trxid_are_validated_and_trxid_is_unique(): void
    {
        $this->bootAddon();
        $this->addonNumber();

        $product = $this->makeProduct();
        ProductRule::create(['product_id' => $product->id, 'advance_type' => 'fixed', 'advance_value' => 200]);

        $component = $this->openFor($product)->call('confirmCod');
        $component->assertSet('step', 'payment');

        // A short sender number and a symbol-laden TrxID.
        $component->set('addon', ['method' => 'bkash', 'sender_number' => '0171', 'trx_id' => 'AB-12'])
            ->call('confirmCod')
            ->assertHasErrors(['addon.sender_number', 'addon.trx_id']);

        $this->assertSame(0, Order::count());

        $component->set('addon', ['method' => 'bkash', 'sender_number' => '01712345678', 'trx_id' => 'ABC123456'])
            ->call('confirmCod')
            ->assertHasNoErrors()
            ->assertSet('placed', true);

        $this->assertSame(1, Order::count());

        // The same TrxID on a second order must be refused before the order lands.
        $second = $this->openFor($product)
            ->call('confirmCod')
            ->set('addon', ['method' => 'bkash', 'sender_number' => '01799999999', 'trx_id' => 'ABC123456'])
            ->call('confirmCod');

        $second->assertHasErrors(['addon.trx_id']);
        $this->assertSame(1, Order::count(), 'A replayed TrxID should not open a second order');
        $this->assertSame(1, OrderPlan::count());
    }

    // ─── admin workflow ──────────────────────────────────────────────────────

    /** @return array{0: Order, 1: OrderPlan} */
    protected function orderWithAdvance(string $trx): array
    {
        $product = $this->makeProduct(['price' => 1000, 'sale_price' => 1000]);
        ProductRule::create(['product_id' => $product->id, 'advance_type' => 'percent', 'advance_value' => 30]);

        // 30% of (1,000 + 60) = 318 advance, 742 due.
        $this->placeAdvanceOrder($product, $trx)->assertSet('placed', true);

        $order = Order::query()->orderByDesc('id')->first();

        return [$order, OrderPlan::forOrder((int) $order->id)];
    }

    public function test_verify_reject_collect_and_refund_transitions(): void
    {
        $this->bootAddon();
        $this->addonNumber();
        $this->actingAs($this->adminUser());

        // A reject needs a reason, and puts the plan back to awaiting an advance.
        [$order] = $this->orderWithAdvance('BK1000000001');
        $payment = Payment::where('order_id', $order->id)->first();

        Livewire::test(PaymentsPanel::class, ['orderId' => $order->id])
            ->call('startReject', $payment->id)
            ->call('confirmReject')
            ->assertHasErrors(['rejectNote']);

        $this->assertSame('pending', $payment->fresh()->status);

        Livewire::test(PaymentsPanel::class, ['orderId' => $order->id])
            ->call('startReject', $payment->id)
            ->set('rejectNote', 'name mismatch')
            ->call('confirmReject');

        $this->assertSame('rejected', $payment->fresh()->status);
        $this->assertSame(OrderPlan::PENDING, OrderPlan::forOrder((int) $order->id)?->payment_status);
        $this->assertDatabaseHas('pp_activities', ['order_id' => $order->id, 'action' => 'advance_rejected']);

        // Verify -> collect the COD balance -> fully paid.
        [$order2] = $this->orderWithAdvance('BK1000000002');
        $payment2 = Payment::where('order_id', $order2->id)->first();

        Livewire::test(PaymentsPanel::class, ['orderId' => $order2->id])->call('verify', $payment2->id);

        $this->assertSame('verified', $payment2->fresh()->status);
        $this->assertSame(OrderPlan::ADVANCE_PAID, OrderPlan::forOrder((int) $order2->id)->payment_status);
        $this->assertDatabaseHas('pp_activities', ['order_id' => $order2->id, 'action' => 'advance_verified']);
        $this->assertNotNull(Payment::where('order_id', $order2->id)->where('status', 'verified')->first()->verified_by);

        Livewire::test(PaymentsPanel::class, ['orderId' => $order2->id])->call('collectDue');

        $plan2 = OrderPlan::forOrder((int) $order2->id);
        $this->assertSame(OrderPlan::PAID, $plan2->payment_status);
        $this->assertSame(0.0, (float) $plan2->due_amount);
        $this->assertDatabaseHas('pp_payments', ['order_id' => $order2->id, 'type' => 'balance', 'status' => 'verified']);

        // Cancelling an order with a verified advance opens a refund.
        [$order3] = $this->orderWithAdvance('BK1000000003');
        $payment3 = Payment::where('order_id', $order3->id)->first();
        Livewire::test(PaymentsPanel::class, ['orderId' => $order3->id])->call('verify', $payment3->id);

        $order3->update(['status' => 'cancelled']);
        $this->assertSame(OrderPlan::REFUND_DUE, OrderPlan::forOrder((int) $order3->id)->payment_status);

        Livewire::test(RefundsIndex::class)
            ->call('markRefunded', OrderPlan::forOrder((int) $order3->id)->id);

        $this->assertSame(OrderPlan::REFUNDED, OrderPlan::forOrder((int) $order3->id)->payment_status);
        $this->assertDatabaseHas('pp_activities', ['order_id' => $order3->id, 'action' => 'refunded']);
    }

    public function test_delivery_is_blocked_until_the_due_is_collected_or_released(): void
    {
        $this->bootAddon();
        $this->addonNumber();
        $this->actingAs($this->adminUser());

        [$order] = $this->orderWithAdvance('BK2000000001');

        try {
            $order->update(['status' => 'delivered']);
            $this->fail('Delivering before the advance is verified should be blocked.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('ভেরিফাই', $e->getMessage());
        }

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_delivery_is_allowed_after_the_balance_is_collected(): void
    {
        $this->bootAddon();
        $this->addonNumber();
        $this->actingAs($this->adminUser());

        [$order] = $this->orderWithAdvance('BK3000000001');
        $payment = Payment::where('order_id', $order->id)->first();

        Livewire::test(PaymentsPanel::class, ['orderId' => $order->id])->call('verify', $payment->id);

        // The courier is told the remaining cash, not the whole order value.
        $this->assertSame(742.0, (float) Hooks::filter('order.courier_cod_amount', 1060, $order));

        // Verified advance but the due is still open: blocked.
        try {
            $order->update(['status' => 'delivered']);
            $this->fail('Delivering with an open due should be blocked.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('বাকি', $e->getMessage());
        }

        Livewire::test(PaymentsPanel::class, ['orderId' => $order->id])->call('collectDue');

        $order->update(['status' => 'delivered']);
        $this->assertSame('delivered', $order->fresh()->status);
        $this->assertSame(0.0, (float) Hooks::filter('order.courier_cod_amount', 1060, $order));
    }

    public function test_admin_override_releases_the_due(): void
    {
        $this->bootAddon();
        $this->addonNumber();
        $this->actingAs($this->adminUser());

        [$order] = $this->orderWithAdvance('BK4000000001');
        $payment = Payment::where('order_id', $order->id)->first();
        Livewire::test(PaymentsPanel::class, ['orderId' => $order->id])->call('verify', $payment->id);

        Livewire::test(PaymentsPanel::class, ['orderId' => $order->id])
            ->call('releaseDue')
            ->assertHasErrors(['releaseNote']);

        Livewire::test(PaymentsPanel::class, ['orderId' => $order->id])
            ->set('releaseNote', 'loyal customer, waived')
            ->call('releaseDue');

        $order->update(['status' => 'delivered']);
        $this->assertSame('delivered', $order->fresh()->status);
        $this->assertDatabaseHas('pp_activities', ['order_id' => $order->id, 'action' => 'due_released']);
        $this->assertSame(0.0, (float) Hooks::filter('order.courier_cod_amount', 1060, $order));
    }

    // ─── product rule + listing ──────────────────────────────────────────────

    public function test_product_rule_form_saves_and_clears_the_rule(): void
    {
        $this->bootAddon();
        $product = $this->makeProduct();

        Livewire::test(ProductRuleForm::class, ['productId' => $product->id])
            ->set('mode', 'percent')
            ->set('value', '25')
            ->call('save');

        $rule = ProductRule::forProduct((int) $product->id);
        $this->assertSame('percent', $rule->advance_type);
        $this->assertSame(25.0, (float) $rule->advance_value);

        // 25% of (1,000 + 60) = 265 advance, 795 due.
        $preview = PlanCalculator::preview((int) $product->id, 1000, 1, 60);
        $this->assertSame(265.0, $preview['advance']);
        $this->assertSame(795.0, $preview['due']);

        Livewire::test(ProductRuleForm::class, ['productId' => $product->id])
            ->set('mode', 'none')
            ->call('save');

        $this->assertNull(ProductRule::forProduct((int) $product->id));
    }

    public function test_orders_list_offers_the_awaiting_advance_tab_and_payment_export(): void
    {
        $this->bootAddon();
        $this->addonNumber();
        $this->actingAs($this->adminUser());

        [$order] = $this->orderWithAdvance('BK5000000001');

        $component = Livewire::test(OrderIndex::class)
            ->call('filterByStatus', 'awaiting_advance')
            ->assertSee('Awaiting advance', false)
            ->assertSee('এডভান্স', false)
            ->assertSee($order->invoice_no, false)
            ->assertSee('Advance — awaiting verification', false);

        // The add-on's own payment filter narrows the same list.
        $component->set('af.payment_status', 'awaiting_verify')
            ->assertSee($order->invoice_no, false);

        $component->set('af.payment_status', 'advance_paid')
            ->assertDontSee($order->invoice_no, false);

        // The core list still filters the real statuses the same way.
        Livewire::test(OrderIndex::class)
            ->call('filterByStatus', 'delivered')
            ->assertDontSee($order->invoice_no, false);

        $headers = (array) Hooks::filter('admin.orders.export_headers', ['INV', 'Total']);
        $this->assertContains('Advance', $headers);
        $this->assertContains('COD Due', $headers);

        $row = (array) Hooks::filter('admin.orders.export_row', ['INV', '1060.00'], $order);
        $this->assertContains('318.00', $row);
        $this->assertContains('742.00', $row);
    }

    // ─── expiry ──────────────────────────────────────────────────────────────

    public function test_expiry_command_cancels_stale_orders_only(): void
    {
        $this->bootAddon();
        $this->addonNumber();

        [$stale, $stalePlan] = $this->orderWithAdvance('BK6000000001');
        [$fresh] = $this->orderWithAdvance('BK6000000002');

        $stalePlan->forceFill(['expires_at' => now()->subHour()])->save();
        OrderPlan::flushMemo();

        // The provider registers this as `partial-payment:expire`; the test runs
        // the same class because the console kernel was built before boot().
        $command = $this->app->make(ExpireAdvanceOrders::class);
        $command->setLaravel($this->app);
        $this->assertSame('partial-payment:expire', $command->getName());

        $output = new BufferedOutput;
        $this->assertSame(0, $command->run(new ArrayInput([]), $output));
        $this->assertStringContainsString('1 order(s) cancelled', $output->fetch());

        $this->assertSame('cancelled', $stale->fresh()->status);
        $this->assertSame('pending', $fresh->fresh()->status);
        $this->assertDatabaseHas('pp_activities', ['order_id' => $stale->id, 'action' => 'expired']);
    }

    // ─── the guarantee: inactive = plain cash on delivery ────────────────────

    public function test_deactivated_addon_shows_no_advance_ui_and_orders_are_plain_cod(): void
    {
        $manager = $this->bootAddon();
        $this->addonNumber();

        $product = $this->makeProduct();
        ProductRule::create(['product_id' => $product->id, 'advance_type' => 'percent', 'advance_value' => 30]);

        $this->openFor($product)
            ->call('confirmCod')
            ->assertSet('step', 'payment');

        $this->assertSame(0, Order::count(), 'While active the advance must be taken first');

        $manager->deactivate('PartialPayment');
        Hooks::flush();
        OrderPlan::flushMemo();
        app(AddonManager::class)->forgetActiveCache();
        $manager->boot();

        $this->openFor($product)
            ->assertDontSee(__('partial-payment::messages.pay_now'), false)
            ->assertDontSee('pp-step-bar', false)
            ->assertSee('অর্ডার কনফার্ম করুন', false)
            ->call('confirmCod')
            ->assertSet('step', 'details')
            ->assertSet('placed', true);

        $this->assertSame(1, Order::count());
        $this->assertSame(0, OrderPlan::count(), 'No plan while the add-on is off');

        $plain = Order::first();
        $this->assertSame(1060.0, (float) $plain->total_amount);
        $this->assertSame('pending', $plain->status);
        $this->assertSame(1060.0, (float) Hooks::filter('order.courier_cod_amount', 1060, $plain));

        // An inactive add-on never reaches the admin either.
        $this->assertSame([], Hooks::filter('admin.orders.columns', []));
        $this->assertNull(Hooks::filter('popup.submit_gate', null, Livewire::test(OrderPopup::class)->instance()));
    }

    public function test_reactivating_restores_the_rules_and_the_old_data(): void
    {
        $manager = $this->bootAddon();
        $this->addonNumber();

        $product = $this->makeProduct();
        ProductRule::create(['product_id' => $product->id, 'advance_type' => 'percent', 'advance_value' => 30]);

        // Walk the gate: the popup stops on the payment step until an advance is handed over.
        $this->openFor($product)
            ->call('confirmCod')
            ->assertSet('step', 'payment');

        $this->assertSame(0, Order::count());

        $this->placeAdvanceOrder($product, 'BK8000000001')->assertSet('placed', true);

        $order = Order::query()->orderByDesc('id')->first();
        $this->assertSame(318.0, (float) OrderPlan::forOrder((int) $order->id)->advance_amount);

        $manager->deactivate('PartialPayment');
        Hooks::flush();
        OrderPlan::flushMemo();
        $manager->forgetActiveCache();
        $manager->boot();

        // Off: plain COD again, and no new plan for the same product.
        $this->openFor($product)
            ->assertDontSee(__('partial-payment::messages.pay_now'), false)
            ->call('confirmCod')
            ->assertSet('placed', true);

        $this->assertSame(2, Order::count());
        $this->assertSame(1, OrderPlan::count(), 'No plan is written while the add-on is off');

        $manager->activate('PartialPayment');
        Hooks::flush();
        OrderPlan::flushMemo();
        $manager->forgetActiveCache();
        $manager->boot();

        $plan = OrderPlan::forOrder((int) $order->id);
        $this->assertNotNull($plan, 'Old plans must still be readable');
        $this->assertSame(318.0, (float) $plan->advance_amount);
        $this->assertSame(742.0, (float) $plan->due_amount);
        $this->assertSame(OrderPlan::PENDING, $plan->payment_status);
        $this->assertDatabaseHas('pp_payments', ['order_id' => $order->id, 'trx_id' => 'BK8000000001']);
        $this->assertDatabaseHas('pp_product_rules', ['product_id' => $product->id]);

        // And the advance UI is back.
        $this->openFor($product)
            ->assertSee(__('partial-payment::messages.pay_now'), false)
            ->call('confirmCod')
            ->assertSet('step', 'payment');
    }
}
