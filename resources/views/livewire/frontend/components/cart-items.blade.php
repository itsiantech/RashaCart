<div class="cart-list">
    @forelse ($cartItems as $id => $item)
        <div class="cart-item">
            <div class="col-product">
                <img src="{{ asset($item['img']) }}" alt="{{ $item['title'] }}">
                <div class="product-details">
                    <div class="ci-title">{{ $item['title'] }}</div>


                     @php
                        $variant = \App\Models\ProductVariant::with([
                            'variantAttributeValues.attribute', 
                            'variantAttributeValues.attributeValue'
                        ])->find($item['variant_id']);
                    @endphp

                    @if ($variant && $variant->variantAttributeValues->count())
                        <div class="ci-variant small">
                            <strong>Variant:</strong>
                            {{ $variant->variantAttributeValues->map(fn($v) => ($v->attribute->name ?? '') . ': ' . ($v->attributeValue->value ?? ''))->join(' / ') }}
                        </div>
                    @endif

                    @if (!empty($item['color']) || !empty($item['size']))
                        <div class="ci-variant">
                            {{ collect([$item['color'] ?? null, $item['size'] ?? null])->filter()->join(' / ') }}
                        </div>
                    @endif

                    <div class="ci-meta">
                        <div class="quantity-controls">
                            <button wire:click="decreaseQty('{{ $id }}')" class="ci-minus"
                                wire:loading.attr="disabled" wire:target="decreaseQty('{{ $id }}')">
                                <span wire:loading.remove wire:target="decreaseQty('{{ $id }}')">-</span>
                                <i wire:loading wire:target="decreaseQty('{{ $id }}')" class="fa-solid fa-spinner fa-spin"></i>
                            </button>

                            <input type="number" value="{{ $item['qty'] }}" readonly class="ci-input">

                            <button wire:click="increaseQty('{{ $id }}')" class="ci-plus"
                                wire:loading.attr="disabled" wire:target="increaseQty('{{ $id }}')">
                                <span wire:loading.remove wire:target="increaseQty('{{ $id }}')">+</span>
                                <i wire:loading wire:target="increaseQty('{{ $id }}')" class="fa-solid fa-spinner fa-spin"></i>
                            </button>
                        </div>

                        <div class="ci-price">Price: {{ setting('currency') }} {{ number_format($item['price']) }}</div>
                        <div class="ci-total">Toral: {{ setting('currency') }} {{ number_format($item['price'] * $item['qty']) }}</div>

                        <button wire:click="removeItem('{{ $id }}')" class="ci-remove" wire:loading.attr="disabled">
                            <i class="fa-solid fa-xmark"></i>
                            <span wire:loading wire:target="removeItem('{{ $id }}')">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="cart-empty">
            <i class="fas fa-shopping-basket"></i>
            <p>Your Cart List is Empty!</p>
            <p>Sorry! your cart has no item to show. Please add some product to see here.</p>
            <a wire:navigate href="{{route('home')}}" class="btn-continue-shopping">Continue Shopping</a>
        </div>
    @endforelse

    {{-- Subtotal --}}
    @if(count($cartItems) > 0)
        <div class="cart-subtotal">
            <span>Subtotal:</span>
            <strong>{{ setting('currency') }} {{ number_format(collect($cartItems)->sum(fn($i) => $i['price'] * $i['qty'])) }}</strong>
        </div>
    @endif
</div>
