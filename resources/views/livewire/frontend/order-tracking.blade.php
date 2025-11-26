<div>
    <!-- Order Tracking Form -->
    <div class="order-tracking py-5">
        <div class="container">
            <h2 class="mb-4">Track Your Order</h2>

            <form wire:submit.prevent="trackOrder" class="mb-4">
                <div class="form-group mb-3">
                    <input type="text" wire:model.defer="invoiceNo" class="form-control" placeholder="Enter your invoice number" required>
                </div>
                <button type="submit" class="track-btn" >Track Order</button>
            </form>

            @if($order)
                <div class="order-details mt-4">
                    <h4>Order Details (Invoice: {{ $order->invoice_no }})</h4>
                    <p><strong>Placed on:</strong> {{ $order->created_at->format('d M Y') }}</p>
                    <p><strong>Status:</strong> <span class="bg-danger text-light p-2">{{ ucfirst($order->status) }}</span></p>                    
                    
                    @if($order->items->count())
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Unit Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <img src="{{ $item->product->thumbnail ? asset('storage/' . $item->product->thumbnail) : asset('assets/admin/img/avatars/user-avater.png') }}" width="50" class="img-thumbnail">
                                            </td>
                                            <td>{{ $item->product->name }}</td>
                                            <td>
                                                @if($item->variant)
                                                    @foreach($item->variant->variantAttributeValues as $vAttr)
                                                        <span class="text-success me-1">
                                                            {{ $vAttr->attribute->name ?? '' }}: {{ $vAttr->attributeValue->value ?? '' }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{setting('currency')}} {{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{setting('currency')}} {{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @php
                                        $subtotal = $order->items->sum(fn($i) => $i->unit_price * $i->quantity);
                                        $delivery = $order->delivery_charge ?? 0;
                                        $paid = $order->pay_amt ?? 0;
                                        $total = $subtotal + $delivery;
                                        $due = $total - $paid;
                                    @endphp
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                                        <td>{{setting('currency')}} {{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Delivery Charge:</td>
                                        <td>{{setting('currency')}}{{ number_format($delivery, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Total Amount:</td>
                                        <td>{{setting('currency')}}{{ number_format($total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Paid:</td>
                                        <td>{{setting('currency')}}{{ number_format($paid, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold text-danger">Amount Due:</td>
                                        <td class="text-danger">{{setting('currency')}}{{ number_format($due, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
