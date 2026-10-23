{{-- resources/views/livewire/pre-order-section.blade.php --}}
<div class="container py-5 px-4">

    {{-- Hero Banner --}}
    <div class="rounded-4 p-5 mb-5 text-white text-center"
        style="background: linear-gradient(135deg, orange, orangered);">
        <h1 class="fw-bold fs-2 mb-2">🚀 Pre-Order Products</h1>
        <p class="mb-0 opacity-75">Be the first to get it — reserve yours today before official launch!</p>
    </div>

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold fs-4 mb-1">🚀 Pre-Order</h2>
            <p class="text-muted small mb-0">Reserve yours before it's available</p>
        </div>
        <select wire:model.live="sortBy" class="form-select form-select-sm w-auto">
            <option value="preorder_end_date">Ending Soon</option>
            <option value="created_at">Newest First</option>
            <option value="price">Price</option>
        </select>
    </div>

    {{-- Product Grid --}}
    @if($products->isEmpty())
    <div class="text-center py-5 text-muted">
        <svg class="mb-3 opacity-50" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
        </svg>
        <p class="mb-0">No pre-order products available right now.</p>
    </div>
    @else
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($products as $product)
        <div class="col">
            <div class="card h-100 border-0 shadow-sm overflow-hidden product-card">

                {{-- Image + Badge --}}
                <div class="position-relative overflow-hidden">
                    <img src="{{ $product->image ?? 'https://via.placeholder.com/300' }}"
                        alt="{{ $product->name }}"
                        class="card-img-top product-thumb"
                        style="height: 200px; object-fit: cover;">
                    <span class="badge position-absolute top-0 start-0 m-2 px-2 py-1"
                        style="background-color: #4f46e5; font-size: 0.7rem; letter-spacing: 0.05em;">
                        PRE-ORDER
                    </span>
                </div>

                <div class="product-card">

                    {{-- Wishlist --}}
                    <button class="wishlist-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                    </button>

                    {{-- Discount Badge --}}
                    @if($product->original_price && $product->price < $product->original_price)
                        @php $discount = round((($product->original_price - $product->price) / $product->original_price) * 100); @endphp
                        <div class="discount-badge">-{{ $discount }}%</div>
                        @endif

                        {{-- Image --}}
                        <div class="img-wrapper">
                            <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://placehold.co/500x500' }}"
                                alt="{{ $product->name }}" loading="lazy">
                        </div>

                        {{-- Body --}}
                        <div class="card-body">

                            <p class="product-name">{{ $product->name }}</p>

                            {{-- Dates --}}
                            @if($product->expected_release_date)
                            <div class="date-ships">Ships: {{ $product->expected_release_date->format('M d, Y') }}</div>
                            @endif
                            @if($product->preorder_end_date)
                            <div class="date-ends">Ends: {{ $product->preorder_end_date->format('M d, Y') }}</div>
                            @endif

                            {{-- Price --}}
                            <div class="price-row">
                                <span class="price-current">৳{{ number_format($product->price, 2) }}</span>
                                @if($product->original_price && $product->price < $product->original_price)
                                    <span class="price-original">৳{{ number_format($product->original_price, 2) }}</span>
                                    @endif
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex gap-2">
                                <button class="btn-buy" wire:click="addToCart({{ $product->id }})" wire:loading.attr="disabled">
                                    Pre Order Now
                                </button>
                                <button class="btn-cart"
                                    wire:click="addToCart({{ $product->id }})"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9" cy="21" r="1" />
                                            <circle cx="20" cy="21" r="1" />
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                                        </svg>
                                    </span>
                                    <span wire:loading wire:target="addToCart({{ $product->id }})">
                                        <span class="spinner-border spinner-border-sm" style="width:0.7rem;height:0.7rem;border-width:2px;" role="status"></span>
                                    </span>
                                </button>
                            </div>

                        </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
    {{-- Hover effect --}}

    <style>
        .product-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 2px 14px rgba(0, 0, 0, 0.07);
            transition: box-shadow 0.25s, transform 0.25s;
            position: relative;
        }

        .product-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.13);
            transform: translateY(-3px);
        }

        .product-card .img-wrapper {
            background: #f5f5f5;
            padding: 1.4rem 1rem;
            min-height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-card .img-wrapper img {
            max-height: 145px;
            max-width: 100%;
            object-fit: contain;
        }

        .product-card .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #fff;
            border: none;
            border-radius: 50%;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.13);
            cursor: pointer;
            z-index: 3;
            transition: box-shadow 0.2s;
            padding: 0;
        }

        .product-card .wishlist-btn:hover {
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.18);
        }

        .product-card .wishlist-btn svg {
            stroke: #bbb;
            transition: stroke 0.2s;
        }

        .product-card .wishlist-btn:hover svg {
            stroke: #ff5722;
        }

        .product-card .discount-badge {
            position: absolute;
            bottom: 138px;
            right: 10px;
            background: #ff5722;
            color: #fff;
            font-size: 0.67rem;
            font-weight: 700;
            border-radius: 50%;
            width: 37px;
            height: 37px;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: -0.02em;
            z-index: 3;
        }

        .product-card .card-body {
            padding: 0.75rem 0.9rem 0.9rem;
        }

        .product-card .product-name {
            font-size: 0.82rem;
            font-weight: 500;
            color: #1a1a1a;
            line-height: 1.4;
            margin-bottom: 0.45rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card .date-ships {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6366f1;
            margin-bottom: 2px;
        }

        .product-card .date-ends {
            font-size: 0.8rem;
            font-weight: 600;
            color: #e11d48;
        }

        .product-card .price-row {
            margin: 0.5rem 0 0.75rem;
        }

        .product-card .price-current {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a1a;
        }

        .product-card .price-original {
            font-size: 0.78rem;
            color: #bbb;
            text-decoration: line-through;
            margin-left: 5px;
        }

        .product-card .btn-buy {
            flex: 1;
            background: var(--btn-all-bg, #ff5722);
            color: var(--btn-all-text, #fff);
            border: none;
            border-radius: 8px;
            padding: 0.52rem 0.5rem;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            transition: background 0.15s;
            cursor: pointer;
        }

        .product-card .btn-buy:hover {
            filter: brightness(.92);
        }

        .product-card .btn-cart {
            background: var(--btn-all-bg, #ff5722);
            color: var(--btn-all-text, #fff);
            border: none;
            border-radius: 8px;
            width: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
            cursor: pointer;
        }

        .product-card .btn-cart:hover {
            filter: brightness(.92);
        }

        .product-card {
            transition: box-shadow 0.25s ease, transform 0.25s ease;
            border-radius: 0.85rem !important;
        }

        .product-card:hover {
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.15) !important;
            transform: translateY(-3px);
        }

        .product-thumb {
            transition: transform 0.35s ease;
            display: none;
        }

        .product-card:hover .product-thumb {
            transform: scale(1.05);
        }
    </style>
</div>