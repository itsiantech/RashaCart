<?php

namespace App\Livewire\Frontend\Product;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Traits\HandlesCartActions;
use Illuminate\Support\Facades\DB;
use App\Services\CouponService;
use App\Services\OrderService;

class Checkout extends Component
{
    use HandlesCartActions;

    public $name, $phone, $shipping_address;
    public $delivery_zone = 'inside';
    public $delivery_charge = 0;

    public $note;
    public $payment_method = 'cod';
    public $coupon_code, $coupon_discount_amount;

    public $cartItems = [];
    public $subtotal = 0;
    public $discount = 0;

    public $total = 0;

    public $loading = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'required|numeric|digits:11',
        'shipping_address' => 'required|string|max:500',

        'delivery_zone' => 'required|in:inside,outside',
        'payment_method' => 'required|in:cod',
    ];

    protected $listeners = ['cartUpdated' => 'refreshCart'];

    public function mount()
    {        
        if (auth()->check()) {
            $user = auth()->user();

            $this->name = $user->name;
            $this->phone = $user->phone;
            $this->shipping_address = $user->address ?? '';
        }

        $this->loadCart();
        $this->calculateTotals();
    }


    public function loadCart()
    {
        $this->cartItems = session('cart', []);
    }

    public function refreshCart()
    {
        $this->loadCart();
        $this->calculateTotals();
    }

    public function updatedDeliveryZone()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['qty']);
        $this->delivery_charge = $this->delivery_zone === 'inside' ? setting('dc_inside') : setting('dc_outside');
        $this->total = $this->subtotal - $this->discount + $this->delivery_charge;
    }
   


    public function applyCoupon(CouponService $couponService)
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

    public function confirmOrder(OrderService $orderService)
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id() ?? null,
            'invoice_no' => $orderService->generateInvoice(),
            'total_amount' => $this->total,
            'status' => 'pending',
            'payment_method' => $this->payment_method,

            'name' => $this->name,
            'phone' => $this->phone,
            'shipping_address' => $this->shipping_address,

            'delivery_zone' => $this->delivery_zone,
            'delivery_charge' => $this->delivery_charge,
            'coupon_code' => $this->coupon_code,
            'coupon_discount_amount' => $this->discount,
            
            'note' => $this->note,
        ];

        $order = $orderService->createOrder($data, $this->cartItems);

        session()->forget('cart');

        return $this->redirectRoute('frontend.order.success', ['order' => $order->id], navigate: true);
    }

    public function render()
    {
        return view('livewire.frontend.product.checkout')->layout('livewire.frontend.layouts.app');
    }
}
