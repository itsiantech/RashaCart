<?php

namespace App\Livewire\Frontend\LandingPage;

use App\Models\LandingPage;
use App\Models\Product;
use App\Services\OrderService;
use Livewire\Component;

class PublicLandingPage extends Component
{
    public $slug;
    public $landingPage;
    public $products;
    public $quantities = [];

    // Customer info
    public $name;
    public $country = 'Bangladesh';
    public $shipping_address;
    public $phone;

    // Delivery
    public $delivery_zone = 'inside';
    public $delivery_charge = 60;

    // Payment
    public $payment_method = 'cod';

    protected $rules = [
        'name' => 'required|string|max:255',
        'country' => 'required|string|max:100',
        'shipping_address' => 'required|string|max:500',
        'phone' => 'required|numeric|digits_between:10,15',
        'delivery_zone' => 'required|in:inside,outside',
        'payment_method' => 'required|in:cod,bkash,nagad,rocket',
    ];

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->landingPage = LandingPage::active()->where('slug', $slug)->firstOrFail();

        if (empty($this->landingPage->product_ids)) {
            abort(404, 'No products associated with this landing page');
        }

        $this->products = Product::whereIn('id', $this->landingPage->product_ids)
            ->where('status', 1)
            ->get();

        if ($this->products->isEmpty()) {
            abort(404, 'No active products found');
        }

        // Initialize quantities
        foreach ($this->products as $product) {
            $this->quantities[$product->id] = 1;
        }

        // Pre-fill user info if logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->name = $user->name;
            $this->phone = $user->phone;
            $this->shipping_address = $user->address ?? '';
        }
    }

    public function updatedDeliveryZone()
    {
        $this->delivery_charge = $this->delivery_zone === 'inside'
            ? setting('dc_inside', 60)
            : setting('dc_outside', 120);
    }

    public function incrementQuantity($productId)
    {
        if (isset($this->quantities[$productId])) {
            $this->quantities[$productId]++;
        }
    }

    public function decrementQuantity($productId)
    {
        if (isset($this->quantities[$productId]) && $this->quantities[$productId] > 1) {
            $this->quantities[$productId]--;
        }
    }

    public function getSubtotalProperty()
    {
        $subtotal = 0;
        foreach ($this->products as $product) {
            $qty = $this->quantities[$product->id] ?? 1;
            $price = $this->landingPage->discount_price ?? $product->price;
            $subtotal += $price * $qty;
        }
        return $subtotal;
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->delivery_charge;
    }

    public function placeOrder(OrderService $orderService)
    {
        $this->validate();

        // Prepare cart items
        $cartItems = [];
        foreach ($this->products as $product) {
            $qty = $this->quantities[$product->id] ?? 1;
            $price = $this->landingPage->discount_price ?? $product->price;

            $cartItems[] = [
                'product_id' => $product->id,
                'vendor_id' => $product->user_id,
                'price' => $price,
                'qty' => $qty,
                'name' => $product->name,
                'thumbnail' => $product->thumbnail,
                'category_id' => $product->category_id,
            ];
        }

        $order = $orderService->createOrder([
            'user_id' => auth()->id() ?? null,
            'invoice_no' => $orderService->generateInvoice(),
            'total_amount' => $this->total,
            'status' => 'pending',
            'payment_method' => $this->payment_method,
            'payment_status' => in_array($this->payment_method, ['bkash', 'nagad', 'rocket']) ? 'pending_verification' : 'unpaid',
            'name' => $this->name,
            'phone' => $this->phone,
            'shipping_address' => $this->shipping_address,
            'delivery_zone' => $this->delivery_zone,
            'delivery_charge' => $this->delivery_charge,
            'note' => 'Order from Landing Page: ' . $this->landingPage->title,
        ], $cartItems);

        // Reset quantities
        foreach ($this->products as $product) {
            $this->quantities[$product->id] = 1;
        }

        return $this->redirectRoute('frontend.order.success', ['order' => $order->id], navigate: true);
    }

    public function render()
    {
        return view('livewire.frontend.landing-page.public-landing-page')->layout('livewire.frontend.layouts.app');
    }
}
