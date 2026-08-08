{{-- resources/views/livewire/admin/pos/pos-terminal.blade.php --}}
<div>

    {{-- ===== MOBILE TAB SWITCHER ===== --}}
    <div class="pos-mobile-tabs">
        <button class="pos-mobile-tab active" id="tabProducts" onclick="switchPosTab('products')">
            <i class="bx bx-store"></i>
            <span>Products</span>
        </button>
        <button class="pos-mobile-tab" id="tabCart" onclick="switchPosTab('cart')">
            <i class="bx bx-cart"></i>
            <span>Cart</span>
            @if(!empty($cart))
            <span class="pos-tab-badge">{{ count($cart) }}</span>
            @endif
        </button>
    </div>

    {{-- ===== MAIN WRAPPER ===== --}}
    <div class="pos-wrapper">

        {{-- ===== LEFT PANEL: Products ===== --}}
        <div class="pos-left" id="posProducts">

            {{-- Filters --}}
            <div class="pos-filter-bar">
                <div class="pos-filter-select-wrap">
                    <select wire:model.live="selectedCategory" class="pos-filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <i class="bx bx-chevron-down pos-filter-chevron"></i>
                </div>

                <div class="pos-filter-select-wrap">
                    <select wire:model.live="selectedBrand" class="pos-filter-select">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    <i class="bx bx-chevron-down pos-filter-chevron"></i>
                </div>

                <div class="pos-search-bar">
                    <i class="bx bx-search"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search..." class="pos-search-input">
                    @if($search)
                    <button wire:click="$set('search','')" class="pos-search-clear">
                        <i class="bx bx-x"></i>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="pos-product-grid">
                @forelse ($products as $product)
                @php
                $hasVariants = $product->variants->count() > 0;
                $stock = $hasVariants
                ? $product->variants->sum(fn($v) => $v->inventories->sortByDesc('id')->first()?->current_stock ?? 0)
                : ($product->inventories->sortByDesc('id')->first()?->current_stock ?? 0);
                $price = $product->sale_price ?? $product->price;
                @endphp

                <div class="pos-product-card {{ $stock <= 0 ? 'out-of-stock' : '' }}" x-data="{ open: false }">

                    <div class="pos-product-img">
                        <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : 'https://placehold.co/100x100' }}"
                            alt="{{ $product->name }}">
                        @if($stock <= 0)
                            <div class="pos-out-badge">Out
                    </div>
                    @endif
                </div>

                <div class="pos-product-body">
                    <div class="pos-product-name">{{ Str::limit($product->name, 28) }}</div>
                    <div class="pos-product-price">{{ setting('currency') }}{{ number_format($price, 2) }}</div>
                    <div class="pos-product-stock">Stock: {{ $stock }}</div>
                </div>

                @if($hasVariants)
                <div class="pos-variant-btn" @click="open = !open">
                    <i class="bx bx-chevron-down"></i> Variants
                </div>
                <div class="pos-variant-dropdown" x-show="open" x-transition
                    @click.outside="open=false" wire:key="product-{{ $product->id }}">
                    @foreach($product->variants as $variant)
                    @php
                    $vStock = $variant->inventories->sortByDesc('id')->first()?->current_stock ?? 0;
                    $vLabel = $variant->variantAttributeValues->map(fn($v) => $v->attributeValue?->value)->filter()->join(', ');
                    $vPrice = $variant->variant_price ?? $price;
                    @endphp
                    <button wire:click="addToCart({{ $product->id }}, {{ $variant->id }})"
                        @click="open=false; switchPosTab('cart')"
                        class="pos-variant-item {{ $vStock <= 0 ? 'disabled' : '' }}"
                        {{ $vStock <= 0 ? 'disabled' : '' }}>
                        <span>{{ $vLabel ?: 'Variant' }}</span>
                        <span>{{ setting('currency') }}{{ number_format($vPrice, 2) }}</span>
                        <span class="pos-variant-stock">{{ $vStock }}</span>
                    </button>
                    @endforeach
                </div>
                @else
                <button wire:click="addToCart({{ $product->id }})"
                    @click="switchPosTab('cart')"
                    class="pos-add-btn"
                    {{ $stock <= 0 ? 'disabled' : '' }}>
                    <i class="bx bx-plus"></i> Add
                </button>
                @endif

            </div>
            @empty
            <div class="pos-empty-products">
                <i class="bx bx-search-alt"></i>
                <p>No products found</p>
            </div>
            @endforelse
        </div>

    </div>{{-- end pos-left --}}

    {{-- ===== RIGHT PANEL: Cart + Checkout ===== --}}
    <div class="pos-right" id="posCart">

        {{-- Header --}}
        <div class="pos-right-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bx bx-receipt fs-5"></i>
                <span class="fw-bold">Current Sale</span>
                @if(!empty($cart))
                <span class="badge bg-danger rounded-pill">{{ count($cart) }}</span>
                @endif
            </div>
            @if(!empty($cart))
            <button wire:click="clearCart" wire:confirm="Clear the entire cart?" class="pos-clear-btn">
                <i class="bx bx-trash"></i> Clear
            </button>
            @endif
        </div>

        {{-- Customer --}}
        <div class="pos-customer-section">
            @if($selectedCustomerId && $selectedCustomerData)
            {{-- Registered Customer --}}
            <div class="pos-customer-selected">
                <div class="d-flex align-items-center gap-2">
                    <div class="pos-customer-avatar">
                        {{ strtoupper(substr($selectedCustomerData->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:0.85rem">{{ $selectedCustomerData->name }}</div>
                        <div class="text-muted" style="font-size:0.75rem">{{ $selectedCustomerData->phone }}</div>
                    </div>
                </div>
                <button wire:click="removeCustomer" class="pos-customer-remove">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            @elseif($isWalkIn)
            {{-- Walk-in Customer --}}
            <div class="pos-customer-selected pos-walkin-selected">
                <div class="d-flex align-items-center gap-2">
                    <div class="pos-customer-avatar pos-walkin-avatar">
                        <i class="bx bx-walk"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:0.85rem">Walk-in Customer</div>
                        <div class="text-muted" style="font-size:0.75rem">No account</div>
                    </div>
                </div>
                <button wire:click="removeCustomer" class="pos-customer-remove">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            @else
            {{-- No Customer Selected --}}
            <button wire:click="$set('showCustomerModal', true)" class="pos-customer-add-btn">
                <i class="bx bx-user-plus me-1"></i>
                Add Customer (optional)
            </button>
            @endif
        </div>

        {{-- Cart Items --}}
        <div class="pos-cart-items">
            @forelse($cart as $key => $item)
            <div class="pos-cart-item" wire:key="cart-{{ $key }}">
                <div class="pos-cart-img">
                    <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://placehold.co/40x40' }}" alt="">
                </div>
                <div class="pos-cart-info">
                    <div class="pos-cart-name">{{ Str::limit($item['name'], 26) }}</div>
                    <div class="pos-cart-price">{{ setting('currency') }}{{ number_format($item['price'], 2) }}</div>
                </div>
                <div class="pos-cart-qty">
                    <button wire:click="decreaseQty('{{ $key }}')" class="pos-qty-btn">−</button>
                    <span class="pos-qty-val">{{ $item['quantity'] }}</span>
                    <button wire:click="increaseQty('{{ $key }}')" class="pos-qty-btn">+</button>
                </div>
                <div class="pos-cart-subtotal">
                    {{ setting('currency') }}{{ number_format($item['subtotal'], 2) }}
                </div>
                <button wire:click="removeFromCart('{{ $key }}')" class="pos-cart-remove">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
            @empty
            <div class="pos-cart-empty">
                <i class="bx bx-cart"></i>
                <p>Cart is empty.<br>Click products to add.</p>
            </div>
            @endforelse
        </div>

        @if(!empty($cart))

        {{-- Discount & Coupon --}}
        <div class="pos-discount-section">
            <div class="pos-discount-row">
                <label class="pos-label">Discount</label>
                <div class="d-flex gap-2 align-items-center">
                    <select wire:model.live="discountType" class="pos-select">
                        <option value="percentage">%</option>
                        <option value="fixed">Fixed</option>
                    </select>
                    <input type="number" wire:model.live.debounce.300ms="discountValue"
                        min="0" placeholder="0" class="pos-input">
                </div>
            </div>
            <div class="pos-coupon-row">
                <label class="pos-label">Coupon</label>
                @if($appliedCoupon)
                <div class="pos-coupon-applied">
                    <span><i class="bx bxs-tag text-success me-1"></i>{{ $appliedCoupon['code'] }}</span>
                    <button wire:click="removeCoupon" class="pos-coupon-remove">Remove</button>
                </div>
                @else
                <div class="d-flex gap-2">
                    <input type="text" wire:model="couponCode"
                        placeholder="Coupon code" class="pos-input" style="text-transform:uppercase">
                    <button wire:click="applyCoupon" class="pos-coupon-apply-btn">Apply</button>
                </div>
                @endif
            </div>
        </div>

        {{-- Totals --}}
        <div class="pos-totals">
            <div class="pos-total-row">
                <span>Subtotal</span>
                <span>{{ setting('currency') }}{{ number_format($subtotal, 2) }}</span>
            </div>
            @if($discountAmount > 0)
            <div class="pos-total-row text-success">
                <span>Discount</span>
                <span>−{{ setting('currency') }}{{ number_format($discountAmount, 2) }}</span>
            </div>
            @endif
            <div class="pos-total-row pos-grand-total">
                <span>Total</span>
                <span>{{ setting('currency') }}{{ number_format($total, 2) }}</span>
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="pos-payment-section">
            <label class="pos-label">Payment Method</label>
            <div class="pos-payment-methods">
                <button wire:click="$set('paymentMethod','cash')"
                    class="pos-pay-btn {{ $paymentMethod === 'cash' ? 'active' : '' }}">
                    <i class="bx bx-money"></i> Cash
                </button>
                <button wire:click="$set('paymentMethod','card')"
                    class="pos-pay-btn {{ $paymentMethod === 'card' ? 'active' : '' }}">
                    <i class="bx bx-credit-card"></i> Card
                </button>
                <button wire:click="$set('paymentMethod','manual')"
                    class="pos-pay-btn {{ $paymentMethod === 'manual' ? 'active' : '' }}">
                    <i class="bx bx-transfer"></i> Manual
                </button>
            </div>
            @if($paymentMethod === 'cash')
            <div class="pos-cash-section mt-2">
                <input type="number" wire:model.live="cashReceived"
                    placeholder="Cash received" class="pos-input w-100" min="0" step="0.01">
                @if($cashReceived >= $total && $total > 0)
                <div class="pos-change-display">
                    Change: <strong>{{ setting('currency') }}{{ number_format($change, 2) }}</strong>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Note --}}
        <div class="pos-note-section">
            <textarea wire:model="note" placeholder="Order note (optional)..."
                class="pos-input w-100" rows="2" style="resize:none"></textarea>
        </div>

        {{-- Checkout Button --}}
        <div class="pos-checkout-wrap">
            <button wire:click="checkout" wire:loading.attr="disabled" wire:target="checkout"
                class="pos-checkout-btn">
                <span wire:loading.remove wire:target="checkout">
                    <i class="bx bx-check-circle me-1"></i>
                    Complete Sale — {{ setting('currency') }}{{ number_format($total, 2) }}
                </span>
                <span wire:loading wire:target="checkout">
                    <i class="bx bx-loader-alt bx-spin me-1"></i> Processing...
                </span>
            </button>
        </div>

        @endif

    </div>{{-- end pos-right --}}

</div>{{-- end pos-wrapper --}}


{{-- ===== CUSTOMER MODAL ===== --}}
@if($showCustomerModal)
<div class="pos-modal-overlay" wire:click.self="$set('showCustomerModal', false)">
    <div class="pos-modal pos-customer-modal">

        <div class="pos-modal-header">
            <div class="d-flex align-items-center gap-2">
                @if($showCreateCustomerForm)
                <button wire:click="$set('showCreateCustomerForm', false)" class="pos-modal-back-btn">
                    <i class="bx bx-arrow-back"></i>
                </button>
                <h6 class="mb-0">New Customer</h6>
                @else
                <h6 class="mb-0">Select Customer</h6>
                @endif
            </div>
            <button wire:click="$set('showCustomerModal', false)">
                <i class="bx bx-x"></i>
            </button>
        </div>

        <div class="pos-modal-body">

            @if($showCreateCustomerForm)
            {{-- Create Customer Form --}}
            <div class="pos-create-customer-form">
                <p class="text-muted small mb-3">
                    <i class="bx bx-info-circle me-1"></i>
                    Default password will be set to their phone number.
                </p>

                <div class="pos-form-group">
                    <label class="pos-form-label">Full Name <span class="text-danger">*</span></label>
                    <div class="pos-form-input-wrap">
                        <i class="bx bx-user pos-form-icon"></i>
                        <input type="text" wire:model="newCustomerName"
                            class="pos-form-input @error('newCustomerName') is-invalid @enderror"
                            placeholder="Enter full name" autofocus />
                    </div>
                    @error('newCustomerName')
                    <span class="pos-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pos-form-group">
                    <label class="pos-form-label">Phone Number <span class="text-danger">*</span></label>
                    <div class="pos-form-input-wrap">
                        <i class="bx bx-phone pos-form-icon"></i>
                        <input type="tel" wire:model="newCustomerPhone"
                            class="pos-form-input @error('newCustomerPhone') is-invalid @enderror"
                            placeholder="01XXXXXXXXX" maxlength="11" />
                    </div>
                    @error('newCustomerPhone')
                    <span class="pos-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pos-form-group">
                    <label class="pos-form-label">
                        Email <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <div class="pos-form-input-wrap">
                        <i class="bx bx-envelope pos-form-icon"></i>
                        <input type="email" wire:model="newCustomerEmail"
                            class="pos-form-input @error('newCustomerEmail') is-invalid @enderror"
                            placeholder="email@example.com" />
                    </div>
                    @error('newCustomerEmail')
                    <span class="pos-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <button wire:click="createCustomer" wire:loading.attr="disabled" wire:target="createCustomer"
                    class="pos-create-submit-btn">
                    <span wire:loading.remove wire:target="createCustomer">
                        <i class="bx bx-user-plus me-1"></i> Create & Select Customer
                    </span>
                    <span wire:loading wire:target="createCustomer">
                        <i class="bx bx-loader-alt bx-spin me-1"></i> Creating...
                    </span>
                </button>
            </div>

            @else
            {{-- Walk-in --}}
            <button wire:click="selectWalkIn" class="pos-walkin-btn">
                <div class="pos-walkin-icon"><i class="bx bx-walk"></i></div>
                <div class="text-start">
                    <div class="fw-bold" style="font-size:0.88rem">Walk-in Customer</div>
                    <div class="text-muted" style="font-size:0.75rem">No registration needed — quick sale</div>
                </div>
                <i class="bx bx-chevron-right ms-auto text-muted"></i>
            </button>

            <div class="pos-modal-divider"><span>or find existing customer</span></div>

            {{-- Search --}}
            <div class="pos-form-input-wrap mb-3">
                <i class="bx bx-search pos-form-icon"></i>
                <input type="text" wire:model.live.debounce.300ms="customerSearch"
                    placeholder="Search by name, phone or email..."
                    class="pos-form-input" autofocus />
                @if($customerSearch)
                <button wire:click="$set('customerSearch','')" class="pos-search-clear" style="right:10px">
                    <i class="bx bx-x"></i>
                </button>
                @endif
            </div>

            {{-- Results --}}
            @if($customers->count())
            <div class="pos-customer-list">
                @foreach($customers as $customer)
                <button wire:click="selectCustomer({{ $customer->id }})" class="pos-customer-item">
                    <div class="pos-customer-avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                    <div class="text-start">
                        <div class="fw-semibold" style="font-size:0.88rem">{{ $customer->name }}</div>
                        <div class="text-muted small">
                            {{ $customer->phone }}{{ $customer->email ? ' · '.$customer->email : '' }}
                        </div>
                    </div>
                    <i class="bx bx-chevron-right ms-auto text-muted"></i>
                </button>
                @endforeach
            </div>
            @elseif(strlen($customerSearch) >= 2)
            <div class="pos-no-customer">
                <i class="bx bx-user-x"></i>
                <p>No customer found for "<strong>{{ $customerSearch }}</strong>"</p>
            </div>
            @else
            <p class="text-muted text-center py-2 small">Type at least 2 characters to search.</p>
            @endif

            <div class="pos-modal-divider"><span>don't have an account?</span></div>

            <button wire:click="$set('showCreateCustomerForm', true)" class="pos-new-customer-btn">
                <i class="bx bx-user-plus me-2"></i> Create New Customer
            </button>

            @endif

        </div>
    </div>
</div>
@endif


{{-- ===== RECEIPT MODAL ===== --}}
@if($showReceiptModal && $lastOrder)
<div class="pos-modal-overlay">
    <div class="pos-modal pos-receipt-modal">

        <div class="pos-modal-header no-print">
            <h6 class="mb-0"><i class="bx bx-receipt me-1"></i> Receipt</h6>
            <div class="d-flex gap-2">
                <button onclick="printReceipt()" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-printer"></i> Print
                </button>
                <button wire:click="$set('showReceiptModal', false)"><i class="bx bx-x"></i></button>
            </div>
        </div>

        <div class="pos-modal-body pos-receipt" id="posReceiptContent">
            <div class="receipt-header">
                <img src="{{ asset('storage/logo/' . setting('app_logo')) }}" alt="Logo" style="max-height:40px">
                <h6 class="mt-2">{{ setting('app_name') }}</h6>
                <p class="text-muted small mb-0">{{ $lastOrder['created_at'] }}</p>
                <p class="fw-bold mb-0">{{ $lastOrder['order_number'] }}</p>
            </div>

            <div class="receipt-customer">
                <span>Customer:</span> <strong>{{ $lastOrder['customer'] }}</strong><br>
                <span>Cashier:</span> <strong>{{ $lastOrder['cashier'] }}</strong>
            </div>

            <div class="receipt-divider"></div>

            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lastOrder['items'] as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td class="text-center">{{ $item['quantity'] }}</td>
                        <td class="text-end">{{ setting('currency') }}{{ number_format($item['subtotal'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="receipt-divider"></div>

            <div class="receipt-totals">
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span>{{ setting('currency') }}{{ number_format($lastOrder['subtotal'], 2) }}</span>
                </div>
                @if($lastOrder['discount'] > 0)
                <div class="d-flex justify-content-between text-success">
                    <span>Discount</span>
                    <span>−{{ setting('currency') }}{{ number_format($lastOrder['discount'], 2) }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between fw-bold fs-6 mt-1">
                    <span>Total</span>
                    <span>{{ setting('currency') }}{{ number_format($lastOrder['total'], 2) }}</span>
                </div>
                @if($lastOrder['payment_method'] === 'cash')
                <div class="d-flex justify-content-between text-muted small">
                    <span>Cash</span>
                    <span>{{ setting('currency') }}{{ number_format($lastOrder['cash_received'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Change</span>
                    <span>{{ setting('currency') }}{{ number_format($lastOrder['change'], 2) }}</span>
                </div>
                @endif
            </div>

            <div class="receipt-divider"></div>
            <p class="text-center text-muted small mb-0">Thank you for shopping!</p>
        </div>

    </div>
</div>
@endif


{{-- ===== STYLES ===== --}}
<style>
    .pos-walkin-selected {
        background: #fff8e1;
    }

    .pos-walkin-avatar {
        background: #f5a623;
        font-size: 1rem;
    }

    /* ─── Layout ──────────────────────────────────────────── */
    .pos-wrapper {
        display: flex;
        height: calc(100vh - 70px);
        background: #f4f5fb;
        overflow: hidden;
    }

    /* ─── Mobile Tab Bar ──────────────────────────────────── */
    .pos-mobile-tabs {
        display: none;
        background: white;
        border-bottom: 2px solid #f0f0f0;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .pos-mobile-tab {
        flex: 1;
        padding: 0.75rem 0.5rem;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        font-size: 0.85rem;
        font-weight: 600;
        color: #888;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        position: relative;
        transition: all 0.2s;
    }

    .pos-mobile-tab.active {
        color: red;
        border-bottom-color: red;
    }

    .pos-mobile-tab i {
        font-size: 1.1rem;
    }

    .pos-tab-badge {
        position: absolute;
        top: 6px;
        right: calc(50% - 28px);
        background: red;
        color: white;
        font-size: 0.6rem;
        font-weight: 700;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ─── Left Panel ──────────────────────────────────────── */
    .pos-left {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1rem;
        overflow: hidden;
        gap: 0.75rem;
    }

    /* ─── Filter Bar ──────────────────────────────────────── */
    .pos-filter-bar {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
        align-items: center;
    }

    .pos-filter-bar .pos-search-bar {
        flex: 1;
    }

    .pos-search-bar input {
        height: 38px;
        border-radius: 5px;
        border: 1px solid #ddd;
        outline: none;
    }

    .pos-filter-select-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .pos-filter-select {
        appearance: none;
        -webkit-appearance: none;
        padding: 0.65rem 2rem 0.65rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.85rem;
        background: white;
        color: #333;
        outline: none;
        cursor: pointer;
        min-width: 165px;
    }

    .pos-filter-select:focus {
        border-color: red;
    }

    .pos-filter-chevron {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 1rem;
        pointer-events: none;
    }

    .pos-search-bar {
        position: relative;
        display: flex;
        align-items: center;
    }

    .pos-search-bar>i {
        position: absolute;
        left: 10px;
        color: #aaa;
    }

    .pos-search-input {
        width: 100%;
        padding: 0.55rem 2.2rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.85rem;
        outline: none;
    }

    .pos-search-input:focus {
        border-color: red;
    }

    .pos-search-clear {
        position: absolute;
        right: 10px;
        background: none;
        border: none;
        color: #aaa;
        cursor: pointer;
        font-size: 1.2rem;
    }

    /* ─── Product Grid ────────────────────────────────────── */
    .pos-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.75rem;
        overflow-y: auto;
        padding-bottom: 1rem;
    }

    .pos-product-card {
        background: white;
        border-radius: 10px;
        border: 1px solid #e8e8e8;
        position: relative;
        overflow: visible;
        transition: box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }

    .pos-product-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .pos-product-card.out-of-stock {
        opacity: 0.6;
    }

    .pos-product-img {
        position: relative;
        height: 110px;
        overflow: hidden;
        border-radius: 10px 10px 0 0;
        background: #f5f5f5;
    }

    .pos-product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pos-out-badge {
        position: absolute;
        top: 6px;
        left: 6px;
        background: #dc3545;
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .pos-product-body {
        padding: 0.5rem 0.6rem 0.4rem;
        flex: 1;
    }

    .pos-product-name {
        font-size: 0.8rem;
        font-weight: 600;
        color: #333;
        line-height: 1.3;
        margin-bottom: 2px;
    }

    .pos-product-price {
        font-size: 0.9rem;
        font-weight: 700;
        color: red;
    }

    .pos-product-stock {
        font-size: 0.7rem;
        color: #999;
    }

    .pos-add-btn {
        margin: 0 0.6rem 0.6rem;
        width: calc(100% - 1.2rem);
        padding: 0.4rem;
        background: red;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .pos-add-btn:hover:not(:disabled) {
        background: #c0392b;
    }

    .pos-add-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .pos-variant-btn {
        margin: 0 0.6rem 0.6rem;
        padding: 0.4rem;
        background: #fcdede;
        color: red;
        border: 1px solid #fcdede;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
    }

    .pos-variant-dropdown {
        position: absolute;
        top: calc(100% - 8px);
        left: 10px;
        right: 10px;
        z-index: 9999;
        background: white;
        border-radius: 10px;
        border: 1px solid #eee;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .15);
        padding: 0.35rem;
        max-height: 220px;
        overflow-y: auto;
    }

    .pos-variant-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 0.4rem 0.6rem;
        background: none;
        border: none;
        border-radius: 5px;
        font-size: 0.78rem;
        cursor: pointer;
        text-align: left;
        gap: 0.5rem;
    }

    .pos-variant-item:hover:not(.disabled) {
        background: #fff0f0;
    }

    .pos-variant-item.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .pos-variant-stock {
        color: #aaa;
        font-size: 0.7rem;
    }

    .pos-empty-products {
        grid-column: 1/-1;
        text-align: center;
        padding: 3rem;
        color: #aaa;
        font-size: 0.9rem;
    }

    .pos-empty-products i {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    /* ─── Right Panel ─────────────────────────────────────── */
    .pos-right {
        width: 380px;
        min-width: 380px;
        background: white;
        border-left: 1px solid #e8e8e8;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .pos-right-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #f0f0f0;
        background: #fafafa;
        flex-shrink: 0;
    }

    .pos-clear-btn {
        background: none;
        border: 1px solid #dc3545;
        color: #dc3545;
        border-radius: 6px;
        padding: 0.25rem 0.6rem;
        font-size: 0.8rem;
        cursor: pointer;
    }

    /* ─── Customer Section ────────────────────────────────── */
    .pos-customer-section {
        padding: 0.65rem 1.25rem;
        border-bottom: 1px solid #f0f0f0;
        flex-shrink: 0;
    }

    .pos-customer-add-btn {
        width: 100%;
        padding: 0.45rem;
        background: #f4f5fb;
        border: 1px dashed #bbb;
        border-radius: 8px;
        color: #666;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .pos-customer-add-btn:hover {
        border-color: red;
        color: red;
        background: #fff0f0;
    }

    .pos-customer-selected {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff0f0;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
    }

    .pos-customer-avatar {
        width: 32px;
        height: 32px;
        background: red;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .pos-customer-remove {
        background: none;
        border: none;
        color: #aaa;
        cursor: pointer;
        font-size: 1.1rem;
    }

    /* ─── Cart Items ──────────────────────────────────────── */
    .pos-cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem 1rem;
    }

    .pos-cart-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .pos-cart-img {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .pos-cart-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pos-cart-info {
        flex: 1;
        min-width: 0;
    }

    .pos-cart-name {
        font-size: 0.78rem;
        font-weight: 600;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pos-cart-price {
        font-size: 0.72rem;
        color: #888;
    }

    .pos-cart-qty {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .pos-qty-btn {
        width: 24px;
        height: 24px;
        background: #fff0f0;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: red;
    }

    .pos-qty-val {
        font-size: 0.85rem;
        font-weight: 700;
        min-width: 20px;
        text-align: center;
    }

    .pos-cart-subtotal {
        font-size: 0.8rem;
        font-weight: 700;
        color: #333;
        min-width: 55px;
        text-align: right;
    }

    .pos-cart-remove {
        background: none;
        border: none;
        color: #ccc;
        cursor: pointer;
        font-size: 1rem;
        padding: 0 2px;
    }

    .pos-cart-remove:hover {
        color: #dc3545;
    }

    .pos-cart-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #ccc;
    }

    .pos-cart-empty i {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    /* ─── Discount & Coupon ───────────────────────────────── */
    .pos-discount-section {
        padding: 0.65rem 1.25rem;
        border-top: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .pos-discount-row,
    .pos-coupon-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .pos-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #555;
        white-space: nowrap;
    }

    .pos-select,
    .pos-input {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 0.35rem 0.6rem;
        font-size: 0.82rem;
        outline: none;
        background: white;
    }

    .pos-select:focus,
    .pos-input:focus {
        border-color: red;
    }

    .pos-select {
        width: 65px;
    }

    .pos-input {
        flex: 1;
    }

    .pos-coupon-applied {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #f0fff4;
        border: 1px solid #b2f5c8;
        border-radius: 6px;
        padding: 0.3rem 0.6rem;
        font-size: 0.82rem;
    }

    .pos-coupon-remove {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 0.75rem;
        cursor: pointer;
    }

    .pos-coupon-apply-btn {
        padding: 0.35rem 0.75rem;
        background: red;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.82rem;
        cursor: pointer;
        white-space: nowrap;
    }

    /* ─── Totals ──────────────────────────────────────────── */
    .pos-totals {
        padding: 0.4rem 1.25rem;
        border-top: 1px solid #f0f0f0;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .pos-total-row {
        display: flex;
        justify-content: space-between;
        padding: 0.2rem 0;
        color: #555;
    }

    .pos-grand-total {
        font-size: 1rem;
        font-weight: 700;
        color: #333;
        border-top: 2px solid #eee;
        padding-top: 0.4rem;
        margin-top: 0.25rem;
    }

    /* ─── Payment ─────────────────────────────────────────── */
    .pos-payment-section {
        padding: 0.5rem 1.25rem;
        border-top: 1px solid #f0f0f0;
        flex-shrink: 0;
    }

    .pos-payment-methods {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.4rem;
    }

    .pos-pay-btn {
        flex: 1;
        padding: 0.45rem 0.25rem;
        border: 2px solid #ddd;
        border-radius: 8px;
        background: white;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all 0.2s;
        color: #666;
    }

    .pos-pay-btn.active {
        border-color: red;
        background: red;
        color: white;
    }

    .pos-change-display {
        margin-top: 0.4rem;
        text-align: center;
        font-size: 0.9rem;
        color: #28a745;
        background: #f0fff4;
        border-radius: 6px;
        padding: 0.3rem;
    }

    /* ─── Note & Checkout ─────────────────────────────────── */
    .pos-note-section {
        padding: 0.4rem 1.25rem 0;
        flex-shrink: 0;
    }

    .pos-checkout-wrap {
        padding: 0.5rem 1.25rem 1rem;
        flex-shrink: 0;
    }

    .pos-checkout-btn {
        width: 100%;
        padding: 0.85rem;
        background: linear-gradient(135deg, red, #e8007c);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .pos-checkout-btn:hover {
        opacity: 0.92;
    }

    .pos-checkout-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* ─── Modals ──────────────────────────────────────────── */
    .pos-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .pos-modal {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 440px;
        max-height: 85vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .pos-customer-modal {
        max-width: 460px;
    }

    .pos-receipt-modal {
        max-width: 380px;
    }

    .pos-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .pos-modal-header button {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        color: #666;
    }

    .pos-modal-body {
        padding: 1.25rem;
        overflow-y: auto;
        flex: 1;
    }

    .pos-modal-back-btn {
        background: none;
        border: none;
        color: #555;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
    }

    /* ─── Customer Modal Contents ─────────────────────────── */
    .pos-walkin-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #fff8e1;
        border: 1.5px dashed #f5a623;
        border-radius: 10px;
        cursor: pointer;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
        text-align: left;
    }

    .pos-walkin-btn:hover {
        background: #fff3cd;
        border-color: #e09000;
    }

    .pos-walkin-icon {
        width: 38px;
        height: 38px;
        background: #f5a623;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .pos-modal-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0.85rem 0;
        color: #bbb;
        font-size: 0.75rem;
    }

    .pos-modal-divider::before,
    .pos-modal-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #eee;
    }

    .pos-customer-list {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .pos-customer-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        padding: 0.6rem;
        background: none;
        border: 1px solid #f0f0f0;
        border-radius: 8px;
        cursor: pointer;
        text-align: left;
        transition: all 0.2s;
    }

    .pos-customer-item:hover {
        border-color: red;
        background: #fff5f5;
    }

    .pos-no-customer {
        text-align: center;
        padding: 1.5rem;
        color: #bbb;
    }

    .pos-no-customer i {
        font-size: 2rem;
        display: block;
        margin-bottom: 0.4rem;
    }

    .pos-new-customer-btn {
        width: 100%;
        padding: 0.65rem;
        background: white;
        border: 1.5px solid red;
        color: red;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .pos-new-customer-btn:hover {
        background: red;
        color: white;
    }

    /* ─── Create Customer Form ────────────────────────────── */
    .pos-create-customer-form {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
    }

    .pos-form-group {
        margin-bottom: 1rem;
    }

    .pos-form-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: #444;
        margin-bottom: 0.35rem;
    }

    .pos-form-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .pos-form-icon {
        position: absolute;
        left: 10px;
        color: #aaa;
        font-size: 1rem;
        pointer-events: none;
    }

    .pos-form-input {
        width: 100%;
        padding: 0.55rem 0.75rem 0.55rem 2.2rem;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        font-size: 0.88rem;
        outline: none;
        transition: border-color 0.2s;
        background: white;
    }

    .pos-form-input:focus {
        border-color: red;
    }

    .pos-form-input.is-invalid {
        border-color: #dc3545;
    }

    .pos-form-error {
        color: #dc3545;
        font-size: 0.76rem;
        margin-top: 0.25rem;
        display: block;
    }

    .pos-create-submit-btn {
        width: 100%;
        padding: 0.75rem;
        background: linear-gradient(135deg, red, #e8007c);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        margin-top: 0.5rem;
        transition: opacity 0.2s;
    }

    .pos-create-submit-btn:hover:not(:disabled) {
        opacity: 0.9;
    }

    .pos-create-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* ─── Receipt ─────────────────────────────────────────── */
    .pos-receipt {
        font-size: 0.85rem;
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 1rem;
    }

    .receipt-customer {
        font-size: 0.82rem;
        margin-bottom: 0.75rem;
        line-height: 1.8;
    }

    .receipt-divider {
        border-top: 1px dashed #ccc;
        margin: 0.75rem 0;
    }

    .receipt-table {
        width: 100%;
        font-size: 0.82rem;
        border-collapse: collapse;
    }

    .receipt-table th {
        font-weight: 600;
        padding-bottom: 0.4rem;
        border-bottom: 1px solid #eee;
    }

    .receipt-table td {
        padding: 0.25rem 0;
    }

    .receipt-totals {
        font-size: 0.85rem;
    }

    /* ─── Tablet ──────────────────────────────────────────── */
    @media (max-width: 991px) {
        .pos-right {
            width: 320px;
            min-width: 320px;
        }

        .pos-product-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        }
    }

    /* ─── Mobile ──────────────────────────────────────────── */
    @media (max-width: 767px) {
        .pos-mobile-tabs {
            display: flex;
        }

        .pos-wrapper {
            height: calc(100vh - 70px - 50px);
            position: relative;
        }

        .pos-left,
        .pos-right {
            position: absolute;
            inset: 0;
            width: 100%;
            min-width: 100%;
            height: 100%;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .pos-left {
            transform: translateX(0);
            opacity: 1;
            z-index: 1;
            background: #f4f5fb;
        }

        .pos-right {
            transform: translateX(100%);
            opacity: 0;
            z-index: 1;
            border-left: none;
        }

        .pos-left.pos-panel-hidden {
            transform: translateX(-100%);
            opacity: 0;
            pointer-events: none;
        }

        .pos-right.pos-panel-visible {
            transform: translateX(0);
            opacity: 1;
        }

        .pos-product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.6rem;
        }

        .pos-product-img {
            height: 90px;
        }

        .pos-filter-bar {
            flex-wrap: wrap;
        }

        .pos-filter-bar .pos-search-bar {
            order: -1;
            width: 100%;
        }

        .pos-filter-select {
            width: 100%;
            min-width: unset;
        }

        .pos-modal-overlay {
            align-items: flex-end;
            padding: 0;
        }

        .pos-modal {
            max-width: 100%;
            border-radius: 16px 16px 0 0;
            max-height: 90vh;
        }

        .pos-receipt-modal {
            max-width: 100%;
        }

        .pos-checkout-wrap {
            padding-bottom: 1.5rem;
        }
    }

    /* ─── Small Mobile ────────────────────────────────────── */
    @media (max-width: 480px) {
        .pos-product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .pos-product-img {
            height: 80px;
        }

        .pos-product-name {
            font-size: 0.75rem;
        }

        .pos-product-price {
            font-size: 0.8rem;
        }

        .pos-cart-item {
            gap: 0.35rem;
        }

        .pos-cart-subtotal {
            min-width: 48px;
            font-size: 0.75rem;
        }

        .pos-cart-name {
            font-size: 0.74rem;
        }

        .pos-checkout-btn {
            font-size: 0.88rem;
            padding: 0.75rem;
        }

        .pos-right-header {
            padding: 0.75rem 1rem;
        }

        .pos-customer-section {
            padding: 0.5rem 1rem;
        }

        .pos-cart-items {
            padding: 0.4rem 0.75rem;
        }

        .pos-discount-section {
            padding: 0.5rem 1rem;
        }

        .pos-totals {
            padding: 0.4rem 1rem;
        }

        .pos-payment-section {
            padding: 0.4rem 1rem;
        }

        .pos-note-section {
            padding: 0.3rem 1rem 0;
        }

        .pos-checkout-wrap {
            padding: 0.5rem 1rem 1.25rem;
        }
    }

    /* ─── Very Small ──────────────────────────────────────── */
    @media (max-width: 359px) {
        .pos-product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.4rem;
        }

        .pos-pay-btn {
            font-size: 0.72rem;
            padding: 0.4rem 0.15rem;
        }

        .pos-pay-btn i {
            display: none;
        }
    }

    /* ─── Print ───────────────────────────────────────────── */
    @media print {
        body * {
            visibility: hidden !important;
        }

        #posReceiptContent,
        #posReceiptContent * {
            visibility: visible !important;
        }

        #posReceiptContent {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }
    }
</style>

@push('scripts')
<script>
    function switchPosTab(tab) {
        const productsPanel = document.getElementById('posProducts');
        const cartPanel = document.getElementById('posCart');
        const tabProducts = document.getElementById('tabProducts');
        const tabCart = document.getElementById('tabCart');
        if (!productsPanel || !cartPanel) return;
        if (tab === 'products') {
            productsPanel.classList.remove('pos-panel-hidden');
            cartPanel.classList.remove('pos-panel-visible');
            tabProducts.classList.add('active');
            tabCart.classList.remove('active');
        } else {
            productsPanel.classList.add('pos-panel-hidden');
            cartPanel.classList.add('pos-panel-visible');
            tabCart.classList.add('active');
            tabProducts.classList.remove('active');
        }
    }

    function printReceipt() {
        window.print();
    }
</script>
@endpush

</div>