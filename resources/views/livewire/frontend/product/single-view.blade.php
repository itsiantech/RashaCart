<div>


    <section class="product-view">
        <div class="container">
            <div class="pv-grid">
                <!-- Main Product Left -->
                <div class="pv-main">
                    <div class="pv-card">
                        <div class="pv-flex">

                            <!-- Gallery -->
                            <div class="pv-gallery">
                                {{-- Discount Badge --}}
                                @if ($product->discount_type)
                                    <div class="pv-badge">
                                        @if ($product->discount_type == 'percent')
                                            -{{ $product->discount_price }}% OFF
                                        @else
                                            -{{ setting('currency') }}{{ $product->discount_price }} OFF
                                        @endif
                                    </div>
                                @endif

                                {{-- Main Image --}}
                                <div class="pv-image">
                                    <img id="pvMainImg"
                                        src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://placehold.co/500x500' }}"
                                        alt="{{ $product->name }}" loading="lazy">
                                </div>

                                {{-- Thumbnails --}}
                                @if (!empty($product->gallery_image) && is_array($product->gallery_image))
                                    <div class="pv-thumbs">
                                        @foreach ($product->gallery_image as $key => $image)
                                            <button class="pv-thumb {{ $loop->first ? 'active' : '' }}"
                                                data-src="{{ asset('storage/' . $image) }}">
                                                <img src="{{ asset('storage/' . $image) }}"
                                                    alt="thumb {{ $key + 1 }}" loading="lazy">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="pv-info">
                                <h1 class="pv-title">{{ $product->name }}</h1>
                                <p class="">{{ $product->short_description }}</p>

                                <div class="pv-pricing">
                                    <span class="pv-price">{{ setting('currency') }} {{ $product->sale_price }}</span>
                                    @if ($product->discount_type)
                                        <span class="pv-price-strike">{{ setting('currency') }}
                                            {{ $product->price }}</span>
                                    @endif
                                </div>

                                <div class="pv-variant">
                                    @if ($enableVariant && $product->variants->count())
                                        <div class="pv-variant mb-3">
                                            <h6 class="fw-bold mb-2">
                                                <i class="fas fa-palette me-2"></i>
                                                Available Variants
                                            </h6>

                                            <div class="border rounded">
                                                @foreach ($product->variants as $variant)
                                                    <div class="variant-item d-flex justify-content-between align-items-center p-2 border-bottom {{ $selectedVariant == $variant->id ? 'bg-light' : '' }}"
                                                        wire:click="selectVariant({{ $variant->id }})"
                                                        style="cursor: pointer;{{ $selectedVariant == $variant->id ? 'border-left: 3px solid #198754 !important;' : '' }}">

                                                        <div
                                                            class="d-flex align-items-center flex-wrap gap-2 flex-grow-1">
                                                            <!-- Images -->
                                                            <div class="d-flex align-items-center">
                                                                @if (!empty($variant->image_gallery))
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        @foreach (array_slice($variant->image_gallery, 0, 1) as $img)
                                                                            <img src="{{ asset('storage/' . $img) }}"
                                                                                alt="Variant Image"
                                                                                class="rounded border"
                                                                                style="width:30px; height:30px; object-fit:cover;">
                                                                        @endforeach
                                                                        @if (count($variant->image_gallery) > 1)
                                                                            <small
                                                                                class="text-muted">+{{ count($variant->image_gallery) - 1 }}</small>
                                                                        @endif
                                                                    </div>
                                                                @elseif(!empty($variant->image))
                                                                    <img src="{{ asset('storage/' . $variant->image) }}"
                                                                        alt="Variant Image" class="rounded border"
                                                                        style="width:30px; height:30px; object-fit:cover;">
                                                                @else
                                                                    <small class="text-muted">N/A</small>
                                                                @endif
                                                            </div>
                                                            <!-- Attributes -->
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($variant->variantAttributeValues as $vav)
                                                                    <span
                                                                        style="
                                                                            display: inline-block;
                                                                            padding: 0.25rem 0.5rem;
                                                                            font-size: 0.75rem;
                                                                            font-weight: 600;
                                                                            color: #fff;
                                                                            background-color: #f10000; /* Bootstrap bg-success এর shade */
                                                                            border-radius: 0.25rem;
                                                                            margin-right: 0.25rem;
                                                                            margin-bottom: 0.25rem;
                                                                        ">
                                                                        {{ $vav->attributeValue->value ?? '' }}
                                                                    </span>
                                                                @endforeach
                                                            </div>

                                                            <!-- Price -->
                                                            <div class="text-success fw-bold small">
                                                                {{ setting('currency') }}{{ number_format($variant->price_diff, 2) }}
                                                            </div>

                                                            <div
                                                                class="d-flex align-items-center small {{ $variant->stock > 10 ? 'text-success' : ($variant->stock > 0 ? 'text-warning' : 'text-danger') }}">
                                                                <i
                                                                    class="fas {{ $variant->stock > 10 ? 'fa-check' : ($variant->stock > 0 ? 'fa-exclamation-triangle' : 'fa-times') }} me-1"></i>
                                                                {{ $variant->stock ?? 0 }}
                                                            </div>

                                                        </div>

                                                        <!-- Select Button -->
                                                        <div class="ms-2">
                                                            <button
                                                                class="btn btn-sm {{ $selectedVariant == $variant->id ? 'btn-success' : 'btn-outline-danger' }}">
                                                                <i
                                                                    class="fas {{ $selectedVariant == $variant->id ? 'fa-check' : 'fa-mouse-pointer' }} me-1"></i>
                                                                {{ $selectedVariant == $variant->id ? 'Selected' : 'Select' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif                                    
                                </div>

                                <div class="pv-qty-actions">
                                   <div class="pv-qty">
                                        <button wire:loading.attr="disabled" 
                                                wire:target="decreaseQty" 
                                                wire:click="decreaseQty"
                                                class="pv-qty-btn" @disabled($quantity <= 1)>
                                            <span wire:loading.remove wire:target="decreaseQty">-</span>
                                            <i wire:loading wire:target="decreaseQty" class="fa-solid fa-spinner fa-spin"></i>
                                        </button>
                                        
                                        <input type="number" wire:model="quantity" min="1">
                                        <button wire:loading.attr="disabled" 
                                                wire:target="increaseQty" 
                                                wire:click="increaseQty" wire:click="increaseQty" class="pv-qty-btn">
                                              <span wire:loading.remove wire:target="increaseQty">+</span>
                                            <i wire:loading wire:target="increaseQty" class="fa-solid fa-spinner fa-spin"></i>
                                        </button>
                                    </div>




                                    <div class="pv-actions">
                                        <!-- Add to Cart Button -->
                                        <button style="width: 60px!important;" class="basket-btn" wire:click="addToCart({{ $product->id }})"
                                            wire:loading.attr="disabled" wire:target="addToCart">
                                            <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                                                <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                                            </span>
                                            <span wire:loading wire:target="addToCart">
                                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                                            </span>
                                        </button>

                                        <!-- Buy Now Button -->
                                        <button class="btn-order-now" wire:click="buyNow({{ $product->id }})"
                                            wire:loading.attr="disabled" wire:target="buyNow">
                                            <span wire:loading.remove wire:target="buyNow">
                                                অর্ডার করুন
                                            </span>
                                            <span wire:loading wire:target="buyNow">
                                                <i class="fa fa-spinner fa-spin" aria-hidden="true"></i> অপেক্ষা করুন...
                                            </span>
                                        </button>
                                    </div>
                                </div>



                                {{-- Category --}}
                                <div class="pv-meta mt-3">
                                    <div class="pv-breadcrumbs">
                                        <span>Category:</span> <a wire:navigate href="{{route('frontend.category.view',  $product->slug )}}">{{ $product->category->name }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="pv-tabs mt-4">
                            <div class="pv-tab-head">
                                <button class="pv-tab-btn active" data-tab="desc">Description</button>
                                <button class="pv-tab-btn" data-tab="rev">Review(0)</button>
                            </div>

                            <div class="pv-tab-body">
                                <div id="pvTab-desc" class="pv-tab-panel active">
                                    {!! $product->long_description !!}
                                </div>
                                <div id="pvTab-rev" class="pv-tab-panel">
                                    <div class="reviews-section">
                                        <p>No reviews yet.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Right -->
                <aside class="pv-sidebar">
                    <div class="pv-side-card">
                        <h3 class="pv-side-title">Related Products</h3>
                        

                            @if(!isset($relatedProducts) || $relatedProducts->isEmpty())
                               <div class="cart-empty">
                                    <i class="fas fa-shopping-basket"></i>
                                    <p>No releted product found!</p>
                                    
                                </div>
                            @endif

                         <ul class="pv-related-list">
                            @foreach ($relatedProducts as $rp)
                            <li>
                                <a wire:navigate href="{{ route('frontend.product.single.view', $rp->slug) }}" class="pv-related-item">
                                    <img src="{{ $rp->thumbnail ? asset('storage/' . $rp->thumbnail) : 'https://placehold.co/150x150' }}" alt="{{ $rp->name }}">
                                    <div>
                                        <p class="pv-related-name">{{ $rp->name }}</p>
                                        <div class="pv-related-meta">
                                            <span class="pv-related-price">{{ setting('currency') }} {{ $rp->sale_price ?? $rp->price }}</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="pv-side-card">
                        <h3 class="pv-side-title">Category</h3>
                        @include('livewire.frontend.layouts.partial.category.category-list', [
                                            'categories' => $categories->where('parent_id', null),
                                        ])
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- Scripts --}}
@push('scripts')
    <script>
        function initProductViewScripts() {
            // Thumbnail switcher
            document.querySelectorAll('.pv-thumb').forEach(btn => {
                btn.addEventListener('click', () => {
                    const mainImg = document.getElementById('pvMainImg');
                    if (mainImg) mainImg.src = btn.dataset.src;
                    document.querySelectorAll('.pv-thumb').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                });
            });

            // Tabs
            document.querySelectorAll('.pv-tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.pv-tab-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.pv-tab-panel').forEach(p => p.classList.remove('active'));
                    btn.classList.add('active');
                    const id = btn.dataset.tab === 'desc' ? 'pvTab-desc' : 'pvTab-rev';
                    document.getElementById(id)?.classList.add('active');
                });
            });

            // Qty controls
            // const qtyInput = document.getElementById('pvQty');
            // if (qtyInput) {
            //     document.querySelectorAll('.pv-qty-btn').forEach(b => {
            //         b.addEventListener('click', () => {
            //             const act = b.dataset.act;
            //             let v = parseInt(qtyInput.value || '1', 10);
            //             if (act === 'minus') v = Math.max(1, v - 1);
            //             if (act === 'plus') v = v + 1;
            //             qtyInput.value = v;
            //         });
            //     });
            // }
        }

        // প্রথমবার পেজ লোডে চালাও
        document.addEventListener('DOMContentLoaded', initProductViewScripts);

        // Livewire navigate করলে আবার চালাও
        document.addEventListener('livewire:navigated', initProductViewScripts);
    </script>
@endpush


</div>
