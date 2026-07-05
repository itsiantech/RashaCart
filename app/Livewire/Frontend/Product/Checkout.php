<?php

namespace App\Livewire\Frontend\Product;

use App\Models\FreeShipping;
use App\Models\ManualPaymentSetting;
use App\Services\CouponService;
use App\Services\OrderService;
use App\Traits\HandlesCartActions;
use Livewire\Component;

class Checkout extends Component
{
    use HandlesCartActions;

    // Customer info
    public $name, $phone, $shipping_address;
    public $delivery_zone = 'inside';
    public $delivery_charge = 0;
    public $note;

    // Payment
    public string $payment_method = 'cod';
    public string $transaction_id = '';

    // Coupon
    public $coupon_code, $coupon_discount_amount;

    // Cart
    public $cartItems = [];
    public $subtotal = 0;
    public $discount = 0;
    public $total = 0;

    // Available payment methods from DB
    public array $manualMethods = [];
    public bool $sslEnabled = false;

    // UI state — which manual method panel is expanded
    public ?string $activeManualMethod = null;

    protected $listeners = ['cartUpdated' => 'refreshCart'];

    protected function rules(): array
    {
        $manualMethodKeys = collect($this->manualMethods)->pluck('key')->toArray();
        $allowedMethods   = array_merge(['cod', 'sslcommerz'], $manualMethodKeys);

        $rules = [
            'name'             => 'required|string|max:255',
            'phone'            => 'required|numeric|digits:11',
            'shipping_address' => 'required|string|max:500',
            'delivery_zone'    => 'required|in:inside,outside',
            'payment_method'   => 'required|in:' . implode(',', $allowedMethods),
        ];

        // Transaction ID required for manual payments
        if (in_array($this->payment_method, $manualMethodKeys)) {
            $rules['transaction_id'] = 'required|string|min:6|max:50';
        }

        return $rules;
    }

    protected array $messages = [
        'transaction_id.required' => 'Transaction ID is required for this payment method.',
        'transaction_id.min'      => 'Transaction ID must be at least 6 characters.',
        'phone.digits'            => 'Phone number must be exactly 11 digits.',
    ];

    public function mount(): void
    {
        if (auth()->check()) {
            $user                   = auth()->user();
            $this->name             = $user->name;
            $this->phone            = $user->phone;
            $this->shipping_address = $user->address ?? '';
        }

        $this->loadPaymentMethods();
        $this->loadCart();
        $this->calculateTotals();
    }

    // ── Payment method discovery ────────────────────────────────────────────

    public function loadPaymentMethods(): void
    {
        $setting = ManualPaymentSetting::first();

        $this->manualMethods = [];

        if ($setting) {
            if ($setting->bkash_enabled && $setting->bkash_number) {
                $this->manualMethods[] = [
                    'key'          => 'bkash',
                    'label'        => 'bKash',
                    'number'       => $setting->bkash_number,
                    'account_type' => $setting->bkash_account_type ?? 'personal',
                    'color'        => '#e2136e',
                    'img'          => 'img/bkash.png',
                ];
            }

            if ($setting->nagad_enabled && $setting->nagad_number) {
                $this->manualMethods[] = [
                    'key'          => 'nagad',
                    'label'        => 'Nagad',
                    'number'       => $setting->nagad_number,
                    'account_type' => $setting->nagad_account_type ?? 'personal',
                    'color'        => '#f05829',
                    'img'          => 'img/nagad.png',
                ];
            }

            if ($setting->rocket_enabled && $setting->rocket_number) {
                $this->manualMethods[] = [
                    'key'   => 'rocket',
                    'label' => 'Rocket',
                    'number' => $setting->rocket_number,
                    'color' => '#8b1fa9',
                    'img'   => 'img/rocket.png',
                ];
            }

            if ($setting->bank_enabled && $setting->bank_account_number) {
                $this->manualMethods[] = [
                    'key'            => 'bank',
                    'label'          => 'Bank Transfer',
                    'bank_name'      => $setting->bank_name,
                    'account_name'   => $setting->bank_account_name,
                    'account_number' => $setting->bank_account_number,
                    'routing'        => $setting->bank_routing_number ?? null,
                    'branch'         => $setting->bank_branch ?? null,
                    'color'          => '#0d6efd',
                    'img'            => 'img/bank.png',
                ];
            }
        }

        // SSL Commerz
        // Uncomment when ApiPaymentSetting is ready:
        // $apiSetting       = ApiPaymentSetting::first();
        // $this->sslEnabled = $apiSetting?->sslcommerz_enabled ?? false;
        $this->sslEnabled = false;
    }

    // ── Payment method selection ────────────────────────────────────────────

    public function selectPaymentMethod(string $method): void
    {
        $this->payment_method = $method;
        $this->transaction_id = '';
        $this->resetErrorBag('transaction_id');

        $manualKeys = collect($this->manualMethods)->pluck('key')->toArray();
        $this->activeManualMethod = in_array($method, $manualKeys) ? $method : null;
    }

    // ── Cart ────────────────────────────────────────────────────────────────

    public function loadCart(): void
    {
        $this->cartItems = session('cart', []);
    }

    public function refreshCart(): void
    {
        $this->loadCart();
        $this->calculateTotals();
    }

    public function updatedDeliveryZone(): void
    {
        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['qty']);

        $this->delivery_charge = $this->checkFreeShippingEligibility()
            ? 0
            : ($this->delivery_zone === 'inside' ? setting('dc_inside', 60) : setting('dc_outside', 120));

        $this->total = $this->subtotal - $this->discount + $this->delivery_charge;
    }

    public function checkFreeShippingEligibility(): bool
    {
        try {
            $rules = FreeShipping::where('is_active', true)->get();
            if ($rules->isEmpty()) return false;

            $subtotal = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['qty']);

            foreach ($rules as $rule) {
                if ($rule->type === 'minimum_amount' && $rule->minimum_amount > 0 && $subtotal >= $rule->minimum_amount) {
                    return true;
                }
            }

            $qualifying = 0;
            $total      = 0;

            foreach ($this->cartItems as $item) {
                $amount  = $item['price'] * $item['qty'];
                $total  += $amount;
                $isMatch = false;

                foreach ($rules as $rule) {
                    if ($rule->type === 'category' && $rule->category_id && isset($item['category_id']) && (int) $item['category_id'] === (int) $rule->category_id) {
                        $isMatch = true; break;
                    }
                    if ($rule->type === 'product' && $rule->product_id && isset($item['product_id']) && (int) $item['product_id'] === (int) $rule->product_id) {
                        $isMatch = true; break;
                    }
                }

                if ($isMatch) $qualifying += $amount;
            }

            return $qualifying > 0 && $qualifying >= $total;
        } catch (\Exception) {
            return false;
        }
    }

    // ── Coupon ──────────────────────────────────────────────────────────────

    public function applyCoupon(CouponService $couponService): void
    {
        $response = $couponService->validateAndApply($this->coupon_code, $this->cartItems);

        if (isset($response['error'])) {
            $this->dispatch('error', ['message' => $response['error']]);
            return;
        }

        $this->discount = $response['discount'];
        $this->calculateTotals();
        $this->dispatch('success', ['message' => 'Coupon applied successfully!']);
    }

    // ── Order submission ────────────────────────────────────────────────────

    public function confirmOrder(OrderService $orderService): mixed
    {
        $this->validate();

        // SSL Commerz: redirect to gateway instead of creating order directly
        if ($this->payment_method === 'sslcommerz') {
            return $this->initiateSslPayment($orderService);
        }

        $order = $orderService->createOrder([
            'user_id'               => auth()->id() ?? null,
            'invoice_no'            => $orderService->generateInvoice(),
            'total_amount'          => $this->total,
            'status'                => 'pending',
            'payment_method'        => $this->payment_method,
            'payment_status'        => in_array($this->payment_method, collect($this->manualMethods)->pluck('key')->toArray())
                                            ? 'pending_verification'
                                            : 'unpaid',
            'transaction_id'        => $this->transaction_id ?: null,
            'name'                  => $this->name,
            'phone'                 => $this->phone,
            'shipping_address'      => $this->shipping_address,
            'delivery_zone'         => $this->delivery_zone,
            'delivery_charge'       => $this->delivery_charge,
            'coupon_code'           => $this->coupon_code,
            'coupon_discount_amount'=> $this->discount,
            'note'                  => $this->note,
        ], $this->cartItems);

        session()->forget('cart');

        return $this->redirectRoute('frontend.order.success', ['order' => $order->id], navigate: true);
    }

    private function initiateSslPayment(OrderService $orderService): void
    {
        // Store pending order in session, redirect to SSL Commerz
        // session(['pending_order' => [...]])
        // return redirect()->route('payment.sslcommerz.init');
    }

    // ── Helpers for view ────────────────────────────────────────────────────

    public function getActiveMethodDetails(): ?array
    {
        return collect($this->manualMethods)->firstWhere('key', $this->activeManualMethod);
    }

    public function render()
    {
        return view('livewire.frontend.product.checkout', [
            'activeMethodDetails' => $this->getActiveMethodDetails(),
        ])->layout('livewire.frontend.layouts.app');
    }
}
