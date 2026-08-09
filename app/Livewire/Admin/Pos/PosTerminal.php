<?php

namespace App\Livewire\Admin\Pos;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PosTerminal extends Component
{
    // Search
    public string $search        = '';
    public string $customerSearch = '';
    public string $selectedCategory = '';
    public string $selectedBrand    = '';

    public bool $showCreateCustomerForm = false;
    public string $newCustomerName  = '';
    public string $newCustomerEmail = '';
    public string $newCustomerPhone = '';
    public string $newCustomerPassword = '';
    public bool $createCustomerLoading = false;
    public bool $isWalkIn = false;

    public string $activeMobileTab = 'products';

    // Cart
    public array $cart = [];

    // Customer
    public ?int  $selectedCustomerId = null;
    public string $walkInName        = 'Walk-in Customer';
    public bool  $showCustomerModal  = false;

    // Discount
    public string $discountType  = 'fixed';
    public $discountValue = 0;
    public string $couponCode    = '';
    public $couponDiscount = 0;
    public ?array $appliedCoupon = null;

    // Payment
    public string $paymentMethod = 'cash';
    public $cashReceived  = 0;
    public string $note          = '';

    // Receipt
    public bool   $showReceiptModal = false;
    public ?array $lastOrder        = null;

    // Totals (computed in recalculate)
    public $subtotal       = 0;
    public $discountAmount = 0;
    public $total          = 0;
    public $change         = 0;

    // ── Products ──
    public function getProductsProperty()
    {
        return Product::with(['variants' => fn($q) => $q->with(['inventories', 'variantAttributeValues.attributeValue']), 'inventories'])
            ->where('status', true)
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku',  'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))  // ← ADD
            ->when($this->selectedBrand,    fn($q) => $q->where('brand_id',    $this->selectedBrand))      // ← ADD
            ->latest()
            ->take(20)
            ->get();
    }

    public function selectWalkIn(): void
    {
        $this->selectedCustomerId     = null;
        $this->isWalkIn               = true;
        $this->walkInName             = 'Walk-in Customer';
        $this->showCustomerModal      = false;
        $this->showCreateCustomerForm = false;
        $this->customerSearch         = '';
        $this->newCustomerName        = '';
        $this->newCustomerEmail       = '';
        $this->newCustomerPhone       = '';
    }

    // Create new customer from POS
    public function createCustomer(): void
    {
        $this->validate([
            'newCustomerName'  => ['required', 'string', 'max:255'],
            'newCustomerPhone' => ['required', 'numeric', 'digits:11', 'unique:users,phone'],
            'newCustomerEmail' => ['nullable', 'email', 'max:255', 'unique:users,email'],
        ], [
            'newCustomerName.required'  => 'Customer name is required.',
            'newCustomerPhone.required' => 'Phone number is required.',
            'newCustomerPhone.digits'   => 'Phone must be 11 digits.',
            'newCustomerPhone.unique'   => 'This phone is already registered.',
            'newCustomerEmail.unique'   => 'This email is already registered.',
        ]);

        $user = User::create([
            'name'     => $this->newCustomerName,
            'phone'    => $this->newCustomerPhone,
            'email'    => $this->newCustomerEmail ?: null,
            'password' => \Illuminate\Support\Facades\Hash::make($this->newCustomerPhone), // default password = phone
        ]);

        $user->assignRole('customer');

        $this->selectedCustomerId = $user->id;
        $this->showCustomerModal  = false;
        $this->resetCreateForm();

        $this->dispatch('notify', ['type' => 'success', 'message' => "Customer '{$user->name}' created and selected!"]);
    }

    public function switchMobileTab($tab)
    {
        $this->activeMobileTab = $tab;
    }

    public function resetCreateForm(): void
    {
        $this->showCreateCustomerForm = false;
        $this->newCustomerName        = '';
        $this->newCustomerEmail       = '';
        $this->newCustomerPhone       = '';
        $this->customerSearch         = '';
    }

    // Override showCustomerModal reset
    public function openCustomerModal(): void
    {
        $this->showCustomerModal      = true;
        $this->showCreateCustomerForm = false;
        $this->customerSearch         = '';
    }

    // ── Customers ──
    public function getCustomersProperty()
    {
        if (strlen($this->customerSearch) < 2) return collect();

        return User::role('customer')
            ->where(function ($q) {
                $q->where('name',  'like', '%' . $this->customerSearch . '%')
                    ->orWhere('email', 'like', '%' . $this->customerSearch . '%')
                    ->orWhere('phone', 'like', '%' . $this->customerSearch . '%');
            })
            ->take(8)
            ->get();
    }

    public function getSelectedCustomerDataProperty(): ?User
    {
        return $this->selectedCustomerId ? User::find($this->selectedCustomerId) : null;
    }

    public function getCategoriesProperty()
    {
        return Category::where('status', true)->orderBy('name')->get();
    }

    public function getBrandsProperty()
    {
        return Brand::where('is_active', true)->orderBy('name')->get();
    }

    // ── Cart ──
    public function addToCart(int $productId, ?int $variantId = null)
    {
        $product = Product::with(['variants.inventories', 'inventories'])->find($productId);
        if (!$product) return;

        // If product has variants but none selected, show warning
        if (!$variantId && $product->variants->count() > 0) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Please select a variant first.']);
            return;
        }

        if ($variantId) {
            $variant = ProductVariant::with(['inventories', 'variantAttributeValues.attributeValue'])->find($variantId);
            $stock   = $variant?->inventories()->latest('id')->first()?->current_stock ?? 0;
            $price   = $variant->variant_price ?? $product->sale_price ?? $product->price;
            $label   = $variant->variantAttributeValues->map(fn($v) => $v->attributeValue?->value)->filter()->join(', ');
            $name    = $product->name . ($label ? " ({$label})" : '');
        } else {
            $stock = $product->inventories()->latest('id')->first()?->current_stock ?? 0;
            $price = $product->sale_price ?? $product->price;
            $name  = $product->name;
        }

        if ($stock <= 0) {
            $this->dispatch('notify', ['type' => 'error', 'message' => "{$name} is out of stock!"]);
            return;
        }

        $key = $variantId ? "v_{$variantId}" : "p_{$productId}";

        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['quantity'] >= $stock) {
                $this->dispatch('notify', ['type' => 'warning', 'message' => "Only {$stock} available!"]);
                return;
            }
            $this->cart[$key]['quantity']++;
            $this->cart[$key]['subtotal'] = $this->cart[$key]['quantity'] * $price;
        } else {
            $this->cart[$key] = [
                'key'        => $key,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name'       => $name,
                'price'      => (float)$price,
                'quantity'   => 1,
                'stock'      => $stock,
                'subtotal'   => (float)$price,
                'image'      => $product->thumbnail,
            ];
        }

        $this->recalculate();
        $this->activeMobileTab = 'cart';
    }

    public function removeFromCart(string $key)
    {
        unset($this->cart[$key]);
        $this->recalculate();
    }

    public function increaseQty(string $key)
    {
        if (!isset($this->cart[$key])) return;
        if ($this->cart[$key]['quantity'] >= $this->cart[$key]['stock']) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Max stock reached!']);
            return;
        }
        $this->cart[$key]['quantity']++;
        $this->cart[$key]['subtotal'] = $this->cart[$key]['quantity'] * $this->cart[$key]['price'];
        $this->recalculate();
    }

    public function decreaseQty(string $key)
    {
        if (!isset($this->cart[$key])) return;
        if ($this->cart[$key]['quantity'] <= 1) {
            $this->removeFromCart($key);
            return;
        }
        $this->cart[$key]['quantity']--;
        $this->cart[$key]['subtotal'] = $this->cart[$key]['quantity'] * $this->cart[$key]['price'];
        $this->recalculate();
    }

    public function clearCart()
    {
        $this->cart          = [];
        $this->couponCode    = '';
        $this->couponDiscount = 0;
        $this->appliedCoupon = null;
        $this->discountValue = 0;
        $this->cashReceived  = 0;
        $this->note          = '';
        $this->isWalkIn       = false;
        $this->recalculate();
    }

    // ── Coupon ──
    public function applyCoupon()
    {
        if (empty($this->couponCode)) return;

        $coupon = Coupon::where('code', strtoupper($this->couponCode))
            ->where('status', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$coupon) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Invalid or expired coupon.']);
            return;
        }

        $sub = collect($this->cart)->sum('subtotal');

        $this->couponDiscount = $coupon->discount_type === 'percent'
            ? $sub * ($coupon->discount_amount / 100)
            : min((float)$coupon->discount_amount, $sub);

        $this->appliedCoupon = [
            'id'       => $coupon->id,
            'code'     => $coupon->code,
            'discount' => $this->couponDiscount,
        ];

        $this->recalculate();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Coupon applied!']);
    }

    public function removeCoupon()
    {
        $this->couponCode     = '';
        $this->couponDiscount = 0;
        $this->appliedCoupon  = null;
        $this->recalculate();
    }

    // ── Recalculate ──
    public function recalculate(): void
    {
        $sub = (float) collect($this->cart)->sum('subtotal');

        $manual = $this->discountType === 'percentage'
            ? $sub * (min((float)$this->discountValue, 100) / 100)
            : min((float)$this->discountValue, $sub);

        $this->discountAmount = $manual + $this->couponDiscount;
        $this->subtotal       = $sub;
        $this->total          = max(0, $sub - $this->discountAmount);
        $this->change         = $this->paymentMethod === 'cash'
            ? max(0, (float)$this->cashReceived - $this->total)
            : 0;
    }

    public function updatedDiscountValue(): void
    {
        $this->recalculate();
    }
    public function updatedDiscountType(): void
    {
        $this->recalculate();
    }
    public function updatedPaymentMethod(): void
    {
        $this->recalculate();
    }
    public function updatedCashReceived(): void
    {
        $this->recalculate();
    }

    // ── Customer ──
    public function selectCustomer(int $id): void
    {
        $this->selectedCustomerId = $id;
        $this->showCustomerModal  = false;
        $this->customerSearch     = '';
    }

    public function removeCustomer(): void
    {
        $this->selectedCustomerId = null;
        $this->isWalkIn           = false;
    }

    public function checkout(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Cart is empty!']);
            return;
        }

        if ($this->paymentMethod === 'cash' && (float)$this->cashReceived < $this->total) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Cash received is less than total amount!']);
            return;
        }

        DB::beginTransaction();
        try {
            $customerName = $this->selectedCustomerData?->name ?? $this->walkInName;

            $order = Order::create([
                'user_id'                 => $this->selectedCustomerId,
                'vendor_id'               => null,
                'name'                    => $customerName,
                'phone'                   => $this->selectedCustomerData?->phone ?? '-',
                'shipping_address'        => $this->selectedCustomerData?->address ?? 'POS Sale',
                'delivery_zone'           => 'POS',
                'delivery_charge'         => 0,
                'payment_method'          => $this->paymentMethod,
                'pay_amt'                 => $this->paymentMethod === 'cash' ? (float)$this->cashReceived : (float)$this->total,
                'invoice_no'              => 'POS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)),
                'note'                    => $this->note ?: null,
                'status'                  => 'completed',
                'total_amount'            => $this->total,
                'coupon_discount_amount'  => $this->appliedCoupon ? $this->couponDiscount : 0,
                'coupon_code'             => $this->appliedCoupon['code'] ?? null,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $item['product_id'],
                    'vendor_id'          => null,
                    'product_variant_id' => $item['variant_id'],  // was variant_id
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $item['price'],        // was price
                    'is_preorder'        => false,
                ]);

                // Deduct stock
                if ($item['variant_id']) {
                    $inv = ProductVariant::find($item['variant_id'])?->inventories()->latest('id')->first();
                } else {
                    $inv = Product::find($item['product_id'])?->inventories()->latest('id')->first();
                }

                if ($inv) {
                    $inv->current_stock = max(0, $inv->current_stock - $item['quantity']);
                    $inv->save();
                }
            }

            DB::commit();

            $this->lastOrder = [
                'order_number'   => $order->invoice_no,
                'customer'       => $customerName,
                'items'          => array_values($this->cart),
                'subtotal'       => $this->subtotal,
                'discount'       => $this->discountAmount,
                'total'          => $this->total,
                'payment_method' => $this->paymentMethod,
                'cash_received'  => $this->cashReceived,
                'change'         => $this->change,
                'created_at'     => now()->format('d M Y, h:i A'),
                'cashier'        => Auth::user()->name,
            ];

            $this->showReceiptModal = true;
            $this->clearCart();
            $this->selectedCustomerId = null;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('POS Checkout Error: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Order failed: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.admin.pos.pos-terminal', [
            'products'  => $this->products,
            'customers' => $this->customers,
            'categories' => $this->categories,
            'brands'     => $this->brands,
            'selectedCustomerData' => $this->selectedCustomerData,
        ])->layout('livewire.admin.layouts.app');
    }
}
