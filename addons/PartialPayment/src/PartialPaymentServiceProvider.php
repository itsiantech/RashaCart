<?php

namespace Addons\PartialPayment;

use Addons\PartialPayment\Console\ExpireAdvanceOrders;
use Addons\PartialPayment\Livewire\Admin\PaymentsPanel;
use Addons\PartialPayment\Livewire\Admin\ProductRuleForm;
use Addons\PartialPayment\Livewire\Admin\RefundsIndex;
use Addons\PartialPayment\Livewire\Admin\SettingsPanel;
use Addons\PartialPayment\Models\OrderPlan;
use Addons\PartialPayment\Models\Payment;
use Addons\PartialPayment\Services\AdvanceService;
use Addons\PartialPayment\Services\PlanCalculator;
use Addons\PartialPayment\Support\Options;
use Addons\PartialPayment\Support\PopupContext;
use App\Addons\AddonManager;
use App\Models\Order;
use App\Support\Hooks;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class PartialPaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AdvanceService::class);

        $this->mergeConfigFrom($this->basePath('config.php'), 'partial-payment');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom($this->basePath('database/migrations'));
        $this->loadViewsFrom($this->basePath('resources/views'), 'partial-payment');
        $this->loadTranslationsFrom($this->basePath('lang'), 'partial-payment');
        $this->loadRoutesFrom($this->basePath('routes/admin.php'));

        Livewire::component('pp-product-rule', ProductRuleForm::class);
        Livewire::component('pp-settings-panel', SettingsPanel::class);
        Livewire::component('pp-payments-panel', PaymentsPanel::class);
        Livewire::component('pp-refunds', RefundsIndex::class);

        $this->registerOrderHooks();
        $this->registerPopupHooks();
        $this->registerAdminHooks();
        $this->guardDeliveries();

        $this->commands(ExpireAdvanceOrders::class);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('partial-payment:expire')->hourly()->withoutOverlapping();
        });
    }

    /** Runs once, right after the add-on is installed. */
    public function onInstall(): void
    {
        foreach (Options::defaults() as $key => $value) {
            if (setting($key, null) === null) {
                \App\Models\Settings::set($key, $value);
            }
        }
    }

    /* ---------------------------------------------------------------------
     |  Order + money
     | ------------------------------------------------------------------- */

    protected function registerOrderHooks(): void
    {
        // The plan the customer sees before ordering is recomputed from the
        // order as it landed in the database.
        Hooks::addFilter('order.payment_plan', function (array $plan, Order $order) {
            $mine = app(AdvanceService::class)->tablesReady()
                ? PlanCalculator::fromOrder($order)
                : null;

            if ($mine === null) {
                return $plan;
            }

            return ['pay_now' => $mine['advance'], 'pay_later' => $mine['due']];
        });

        Hooks::addAction('order.created', function (Order $order, array $plan = [], array $input = []) {
            app(AdvanceService::class)->registerOrder($order, $input);
        });

        // A verified-but-unpaid advance must not be delivered by the courier.
        Hooks::addFilter('order.courier_cod_amount', function ($amount, Order $order) {
            $plan = $this->planFor($order);

            return $plan === null ? $amount : (float) $plan->due_amount;
        });
    }

    /* ---------------------------------------------------------------------
     |  Order popup
     | ------------------------------------------------------------------- */

    protected function registerPopupHooks(): void
    {
        Hooks::addFilter('popup.steps', function (array $steps, $popup) {
            if (! PopupContext::requiresAdvance($popup)) {
                return $steps;
            }

            $steps[] = ['key' => 'payment', 'label' => __('partial-payment::messages.step_label'), 'slot' => 'popup.step.partial_payment'];

            return $steps;
        });

        Hooks::addFilter('popup.submit_gate', function ($gate, $popup) {
            return PopupContext::requiresAdvance($popup) ? 'payment' : $gate;
        });

        Hooks::addFilter('popup.validation_rules', function (array $rules, $popup) {
            if (! PopupContext::requiresAdvance($popup)) {
                return $rules;
            }

            $rules['addon.method'] = ['required', 'in:'.implode(',', array_keys(Options::METHODS))];
            $rules['addon.sender_number'] = ['required', 'string', 'min:11', 'max:14'];
            $rules['addon.trx_id'] = ['required', 'string', 'min:6', 'max:20', 'regex:/^[A-Za-z0-9]+$/', function ($attribute, $value, $fail) use ($popup) {
                // A TrxID already on the books must stop the order before it lands.
                $method = array_key_exists($popup->addon['method'] ?? '', Options::METHODS)
                    ? (string) $popup->addon['method']
                    : null;

                if ($method !== null && Payment::where('method', $method)->where('trx_id', trim((string) $value))->exists()) {
                    $fail(__('partial-payment::messages.err_trx_used'));
                }
            }];

            return $rules;
        });

        Hooks::addFilter('popup.validation_messages', function (array $messages, $popup) {
            if (! PopupContext::requiresAdvance($popup)) {
                return $messages;
            }

            return array_merge($messages, [
                'addon.method.required' => __('partial-payment::messages.err_method'),
                'addon.method.in' => __('partial-payment::messages.err_method'),
                'addon.sender_number.required' => __('partial-payment::messages.err_sender'),
                'addon.sender_number.min' => __('partial-payment::messages.err_sender'),
                'addon.trx_id.required' => __('partial-payment::messages.err_trx'),
                'addon.trx_id.min' => __('partial-payment::messages.err_trx'),
                'addon.trx_id.max' => __('partial-payment::messages.err_trx'),
                'addon.trx_id.regex' => __('partial-payment::messages.err_trx'),
            ]);
        });

        Hooks::addFilter('popup.primary_button_label', function (string $label, $popup) {
            if (! PopupContext::requiresAdvance($popup)) {
                return $label;
            }

            return $popup->step === 'payment'
                ? __('partial-payment::messages.gate_pay')
                : __('partial-payment::messages.gate_details');
        }, 20);

        Hooks::addFilter('popup.whatsapp_text', function (string $text, Order $order) {
            $plan = OrderPlan::forOrder((int) $order->id);

            if ($plan === null) {
                return $text;
            }

            return $text."\n".
                __('partial-payment::messages.wa_advance').': '.$this->money($plan->advance_amount, __('partial-payment::messages.wa_waiting'))."\n".
                __('partial-payment::messages.wa_cod').': '.$this->money($plan->due_amount, __('partial-payment::messages.wa_delivery_time'));
        });

        Hooks::addSlot('popup.summary.extra', 'partial-payment::popup.summary-rows');
        Hooks::addSlot('popup.step.partial_payment', 'partial-payment::popup.step');
        Hooks::addSlot('popup.success.extra', 'partial-payment::popup.success');
    }

    /* ---------------------------------------------------------------------
     |  Admin
     | ------------------------------------------------------------------- */

    protected function registerAdminHooks(): void
    {
        Hooks::addSlot('admin.menu', 'partial-payment::admin.menu');

        Hooks::addFilter('admin.orders.columns', function (array $columns) {
            $columns[] = ['key' => 'partial_payment', 'label' => 'Payment'];

            return $columns;
        });

        Hooks::addSlot('admin.orders.cell.partial_payment', 'partial-payment::admin.column');

        Hooks::addSlot('admin.orders.filters', 'partial-payment::admin.filters');

        Hooks::addFilter('admin.orders.query', function ($query, array $filters) {
            $value = (string) ($filters['payment_status'] ?? 'all');

            if (! in_array($value, ['awaiting_verify', 'advance_pending', 'advance_paid', 'paid', 'refund_due'], true)) {
                return $query;
            }

            if ($value === 'awaiting_verify') {
                return $query->whereIn('orders.id', Payment::query()
                    ->select('order_id')
                    ->where('type', 'advance')
                    ->where('status', 'pending'));
            }

            return $query->whereIn('orders.id', OrderPlan::query()
                ->select('order_id')
                ->where('payment_status', $value));
        });

        Hooks::addFilter('order.statuses', function (array $statuses) {
            array_unshift($statuses, 'awaiting_advance');

            return $statuses;
        });

        Hooks::addFilter('order.status_counts', function (array $counts) {
            if (! app(AdvanceService::class)->tablesReady()) {
                return $counts;
            }

            $counts['awaiting_advance'] = OrderPlan::query()
                ->where('payment_status', OrderPlan::PENDING)
                ->whereHas('order', fn ($q) => $q->where('status', 'pending'))
                ->count();

            return $counts;
        });

        // "awaiting_advance" is not a column value: the orders enum cannot hold
        // it, so the tab filters on this add-on's own plan table instead.
        Hooks::addFilter('order.status_query', function ($query, string $status) {
            if ($status !== 'awaiting_advance') {
                return $query->where('status', $status);
            }

            return $query->where('status', 'pending')->whereIn(
                'orders.id',
                OrderPlan::query()->select('order_id')->where('payment_status', OrderPlan::PENDING)
            );
        });

        Hooks::addFilter('admin.orders.export_headers', function (array $headers) {
            $headers[] = 'Advance';
            $headers[] = 'Advance Status';
            $headers[] = 'COD Due';

            return $headers;
        });

        Hooks::addFilter('admin.orders.export_row', function (array $row, Order $order) {
            $plan = $this->planFor($order);

            $row[] = $plan ? number_format((float) $plan->advance_amount, 2) : '';
            $row[] = $plan ? str_replace('_', ' ', $plan->payment_status) : '';
            $row[] = $plan ? number_format((float) $plan->due_amount, 2) : '';

            return $row;
        });

        Hooks::addSlot('admin.order.details.panels', 'partial-payment::admin.order-panel');
        Hooks::addSlot('admin.product.form.sections', 'partial-payment::admin.product-section');

        Hooks::addSlot('admin.settings.tabs', 'partial-payment::admin.settings-tab');
        Hooks::addSlot('admin.settings.panes', 'partial-payment::admin.settings-pane');

        Hooks::addSlot('admin.dashboard.cards', 'partial-payment::admin.dashboard-cards');
    }

    /* --------------------------------------------------------------------- */

    protected function guardDeliveries(): void
    {
        Order::updating(function (Order $order) {
            if (! $this->handlesOrders()) {
                return;
            }

            if (! $order->isDirty('status') || $order->status !== 'delivered') {
                return;
            }

            $plan = $this->planFor($order);

            if ($plan === null) {
                return;
            }

            if ($plan->payment_status === OrderPlan::PENDING) {
                throw new \RuntimeException('অ্যাডভান্স পেমেন্ট এখনো ভেরিফাই হয়নি — ডেলিভারড করা যাবে না।');
            }

            if ($plan->hasOutstandingDue()) {
                throw new \RuntimeException('বাকি টাকা আদায় করা নেই — আগে "Collect due" দিন, অথবা override করুন।');
            }
        });

        Order::updated(function (Order $order) {
            if (! $this->handlesOrders()) {
                return;
            }

            if ($order->wasChanged('status') && $order->status === 'cancelled') {
                app(AdvanceService::class)->markRefundDue($order);
            }
        });
    }

    /**
     * Eloquent listeners outlive a same-process activate/deactivate, so the
     * guard re-reads the add-on's own state instead of trusting boot order.
     */
    protected function handlesOrders(): bool
    {
        return app(AddonManager::class)->isActive('PartialPayment');
    }

    protected function planFor(Order $order): ?OrderPlan
    {
        if (! app(AdvanceService::class)->tablesReady()) {
            return null;
        }

        return OrderPlan::forOrder((int) $order->id);
    }

    protected function money($amount, ?string $suffix = null): string
    {
        $text = setting('currency').' '.number_format((float) $amount, 2);

        return $suffix === null ? $text : $text.' ('.$suffix.')';
    }

    protected function basePath(string $path): string
    {
        return __DIR__.'/../'.$path;
    }
}
