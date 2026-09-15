<div class="cart-list">
    @forelse ($cartItems as $id => $item)
    <div class="cart-item">
        <div class="col-product">
            <img src="{{ $item['img'] ?? 'https://placehold.co/500x500' }}" alt="{{ $item['title'] ?? 'Product' }}">
            <div class="product-details">

                <div class="ci-title">{{ $item['title'] ?? 'Untitled Product' }}</div>

                {{-- Color Variant --}}
                @if (!empty($item['color']))
                <div class="ci-variant ci-color small" style="margin-bottom: 0.5rem;">
                    <i class="fas fa-circle me-1" style="color: #d32f2f;"></i>
                    <strong>Color:</strong>
                    <span style="color: #333; font-weight: 600;">{{ $item['color'] }}</span>
                </div>
                @endif

                {{-- Size Variant --}}
                @if (!empty($item['size']))
                <div class="ci-variant ci-size small" style="margin-bottom: 0.5rem;">
                    <i class="fas fa-expand me-1" style="color: #0056b3;"></i>
                    <strong>Size:</strong>
                    <span style="color: #333; font-weight: 600;">{{ $item['size'] }}</span>
                </div>
                @endif

                {{-- Fallback: only show combined label if NEITHER color nor size exist separately --}}
                @if (empty($item['color']) && empty($item['size']) && !empty($item['variant_label']))
                <div class="ci-variant small text-muted" style="margin-bottom: 0.5rem;">
                    <i class="fas fa-palette me-1"></i>
                    <strong>Variant:</strong>
                    <span style="color: #d32f2f; font-weight: 600;">{{ $item['variant_label'] }}</span>
                </div>
                @endif

                <div class="ci-meta">
                    {{-- Quantity controls --}}
                    <div class="quantity-controls">
                        <button wire:click="decreaseQty('{{ $id }}')" class="ci-minus"
                            wire:loading.attr="disabled" wire:target="decreaseQty('{{ $id }}')">
                            <span wire:loading.remove wire:target="decreaseQty('{{ $id }}')">-</span>
                            <i wire:loading wire:target="decreaseQty('{{ $id }}')" class="fa-solid fa-spinner fa-spin"></i>
                        </button>

                        <input type="number" value="{{ $item['qty'] ?? 1 }}" readonly class="ci-input">

                        <button wire:click="increaseQty('{{ $id }}')" class="ci-plus"
                            wire:loading.attr="disabled" wire:target="increaseQty('{{ $id }}')">
                            <span wire:loading.remove wire:target="increaseQty('{{ $id }}')">+</span>
                            <i wire:loading wire:target="increaseQty('{{ $id }}')" class="fa-solid fa-spinner fa-spin"></i>
                        </button>
                    </div>

                    <div class="ci-price">Price: {{ setting('currency') }} {{ number_format($item['price'] ?? 0) }}</div>
                    <div class="ci-total">Total: {{ setting('currency') }} {{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1)) }}</div>

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
        <a wire:navigate href="{{ route('home') }}" class="btn-continue-shopping">Continue Shopping</a>
    </div>
    @endforelse

    {{-- Subtotal --}}
    @if (count($cartItems) > 0)
    <div class="cart-subtotal d-none">
        <div class="summary-section">Summary</div>
        <div class="subtotal-section">
            <span class="d-flex">
                Subtotal <p class="small">({{ count($cartItems) }} items)</p>:
            </span>
            <strong>
                {{ setting('currency') }} {{ number_format(collect($cartItems)->sum(fn($i) => ($i['price'] ?? 0) * ($i['qty'] ?? 1))) }}
            </strong>
        </div>
    </div>
    @endif
</div>