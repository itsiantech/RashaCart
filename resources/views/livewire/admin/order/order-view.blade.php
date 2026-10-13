<div class="card p-3">

    {{-- Invoice Header - More Compact --}}
    <div class="row mb-3">

        <div class="d-flex justify-content-between align-items-start mb-4 border-bottom pb-3">

            <div>
                <h4>Invoice #{{ $order->invoice_no }}</h4>
                <p class="mb-0"><strong>Customer:</strong> {{ $order->name }}</p>
                <p class="mb-0"><strong>Phone:</strong> <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></p>
                <p class="mb-0"><strong>Shipping:</strong> {{ $order->shipping_address }}</p>
                <p class="mb-0"><strong>Note:</strong> {{ $order->note ?? '-' }}</p>
                <p class="mb-0"><strong>Shop Name:</strong> {{ $order->items->first()?->product?->user?->vendorDetails?->shop_name ?? 'N/A' }}</p>

                @if ($order->coupon_code)
                <p class="mb-0 mt-1"><strong>Coupon:</strong>
                    {{ $order->coupon_code }}
                    ( -{{ setting('currency') }}{{ number_format($order->coupon_discount_amount, 2) }} )
                </p>
                @endif
            </div>

            <div class="text-end">
                <div class="card bg-light">
                    <div class="card-body py-2">

                        @php
                        $subtotal = $order->items->sum(fn($i) => $i->unit_price * $i->quantity);
                        $delivery = $order->delivery_charge ?? 0;
                        $couponDiscount = $order->coupon_discount_amount ?? 0;
                        $paid = $order->pay_amt ?? 0;

                        // Total after coupon discount
                        $afterDiscount = $subtotal - $couponDiscount;

                        // Final Grand Total
                        $total = $afterDiscount + $delivery;

                        // Due Amount
                        $due = $total - $paid;
                        @endphp

                        <div class="d-flex justify-content-between mb-1">
                            <span>Subtotal:</span>
                            <strong>{{ setting('currency') }} {{ number_format($subtotal, 2) }}</strong>
                        </div>

                        @if ($order->coupon_code)
                        <div class="d-flex justify-content-between mb-1">
                            <span>Coupon ({{ $order->coupon_code }}):</span>
                            <strong class="text-success">- {{ setting('currency') }}
                                {{ number_format($couponDiscount, 2) }}</strong>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between mb-1">
                            <span>After Discount:</span>
                            <strong>{{ setting('currency') }} {{ number_format($afterDiscount, 2) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Delivery Charge:</span>
                            <strong>{{ setting('currency') }} {{ number_format($delivery, 2) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span><strong>Total Amount:</strong></span>
                            <strong>{{ setting('currency') }} {{ number_format($total, 2) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Paid:</span>
                            <span>{{ setting('currency') }} {{ number_format($paid, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Amount Due:</span>
                            <strong class="text-danger">
                                {{ setting('currency') }} {{ number_format($due, 2) }}
                            </strong>
                        </div>

                        <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank"
                            class="btn btn-primary btn-sm w-100 mt-1">
                            <i class="bx bx-printer"></i> Print Invoice
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>



    {{-- Items Table - More Compact --}}
    {{-- Order Items Table --}}
    <div class="table-responsive mb-3">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th width="60">Image</th>
                    <th>Product</th>
                    <th>Variant</th>
                    <th width="110" class="text-end">Unit Price</th>
                    <th width="120" class="text-center">Quantity</th>
                    <th width="110" class="text-end">Subtotal</th>
                    <th width="70" class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($order->items as $item)
                <tr>
                    {{-- Product Image --}}
                    <td>
                        <img src="{{ $item->display_image }}" width="40" class="img-thumbnail">
                    </td>

                    {{-- Product Info --}}
                    <td>
                        @if($item->product)
                        <div class="fw-semibold">
                            {{ $item->product->name }}
                        </div>
                        <small class="text-muted">
                            SKU: {{ $item->product->sku ?? 'N/A' }}
                        </small>
                        @else
                        <span class="text-danger">
                            Product Deleted
                        </span>
                        @endif
                    </td>

                    {{-- Variant --}}
                    <td>
                        @if($item->variant)
                        @if($item->variant->variantAttributeValues->count())
                        @foreach ($item->variant->variantAttributeValues as $vAttr)
                        @if($vAttr->attribute && $vAttr->attributeValue)
                        <span class="badge bg-secondary me-1">
                            {{ $vAttr->attribute->name }}: {{ $vAttr->attributeValue->value }}
                        </span>
                        @endif
                        @endforeach
                        @else
                        <span class="text-muted">No attributes</span>
                        @endif
                        @elseif($item->variant_name)
                        <span class="badge bg-secondary me-1">{{ $item->variant_name }}</span>
                        @if($item->variant_sku)<small class="text-muted d-block">{{ $item->variant_sku }}</small>@endif
                        @else
                        -
                        @endif
                    </td>

                    {{-- Unit Price --}}
                    <td class="text-end fw-semibold">
                        {{ setting('currency') }}
                        {{ number_format($item->unit_price, 2) }}
                    </td>

                    {{-- Quantity --}}
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                wire:click="decreaseQuantity({{ $item->id }})">
                                -
                            </button>

                            <span class="mx-2">
                                {{ $item->quantity }}
                            </span>

                            <button
                                class="btn btn-sm btn-outline-secondary"
                                wire:click="increaseQuantity({{ $item->id }})">
                                +
                            </button>
                        </div>
                    </td>

                    {{-- Subtotal --}}
                    <td class="text-end fw-semibold">
                        {{ setting('currency') }}
                        {{ number_format($item->unit_price * $item->quantity, 2) }}
                    </td>

                    {{-- Action --}}
                    <td class="text-center">
                        <button
                            class="btn btn-sm btn-outline-danger"
                            wire:click="removeItem({{ $item->id }})"
                            title="Remove">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No items found for this order.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    {{-- Add New Item with search --}}
    <div class="mb-3">
        <label class="form-label">Add Product</label>
        <input type="text" class="form-control mb-2" placeholder="Search product..."
            wire:model.live.debounce.300ms="searchProduct">

        @if ($searchResults)
        <ul class="list-group mb-2">
            @foreach ($searchResults as $product)
            <li class="list-group-item d-flex justify-content-between align-items-center"
                wire:click="selectProduct({{ $product->id }})" style="cursor:pointer;">
                {{ $product->name }} -
                {{ setting('currency') }}{{ number_format($product->price, 2) }}
            </li>
            @endforeach
        </ul>
        @endif

        @if ($selectedProduct)
        <div class="mb-2">
            <p><strong>Selected Product:</strong> {{ $selectedProduct->name }}</p>

            @if ($selectedProduct->variants->count())
            <label>Variant:</label>
            <select class="form-select mb-2" wire:model="selectedVariant">
                <option value="">Select Variant</option>
                @foreach ($selectedProduct->variants as $variant)
                <option value="{{ $variant->id }}">
                    {{ $variant->sku }} -
                    {{ setting('currency') }}{{ number_format($variant->price, 2) }}
                </option>
                @endforeach
            </select>
            @endif

            <div class="d-flex gap-2">
                <input type="number" class="form-control w-auto" min="1" wire:model="newItemQuantity">
                <button class="btn btn-success" wire:click="addItem">Add Item</button>
            </div>
        </div>
        @endif
    </div>

</div>