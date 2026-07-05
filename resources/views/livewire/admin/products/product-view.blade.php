<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Product Details</h5>
                    <div>
                        <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Product Main Gallery -->
                        <div class="col-md-5">
                            @php
                            $mainImage = $product->thumbnail
                            ? asset('storage/' . $product->thumbnail)
                            : asset('assets/admin/img/avatars/user-avater.png');

                            $videoId = null;
                            if ($product->video) {
                            $url = $product->video;
                            if (str_contains($url, 'watch?v=')) {
                            parse_str(parse_url($url, PHP_URL_QUERY), $query);
                            $videoId = $query['v'] ?? null;
                            } elseif (str_contains($url, 'youtu.be/')) {
                            $parts = explode('/', $url);
                            $videoId = end($parts);
                            }
                            }
                            @endphp

                            <div class="main-display mb-3 position-relative" style="min-height: 300px;">
                                <img id="mainProductImage" src="{{ $mainImage }}" alt="{{ $product->name }}"
                                    class="img-fluid rounded border w-100" style="display: block;">

                                @if ($videoId)
                                <div id="videoContainer" class="ratio ratio-16x9" style="display: none;">
                                    <iframe id="videoIframe" src="" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                                @endif
                            </div>

                            <div class="thumbnail-container d-flex flex-wrap gap-2">
                                <img src="{{ $mainImage }}" alt="Main Thumbnail" class="img-thumbnail cursor-pointer"
                                    style="width:80px; height:80px; object-fit:cover;">

                                @if (!empty($product->gallery_image))
                                @foreach ($product->gallery_image as $img)
                                <img src="{{ asset('storage/' . $img) }}" alt="Gallery Image"
                                    class="img-thumbnail cursor-pointer"
                                    style="width:80px; height:80px; object-fit:cover;">
                                @endforeach
                                @endif

                                @if ($videoId)
                                <div class="img-thumbnail cursor-pointer video-thumbnail position-relative"
                                    style="width:80px; height:80px; object-fit:cover;">
                                    <img src="https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg"
                                        alt="Video Thumbnail" class="w-100 h-100" />
                                    <div class="position-absolute top-50 start-50 translate-middle text-white"
                                        style="font-size:2rem;">
                                        <i class="bx bx-play-circle"></i>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="col-md-7">
                            <h2>{{ $product->name }}</h2>
                            <p class="text-muted">{{ $product->short_description }}</p>

                            <div class="d-flex align-items-center mb-3">
                                Status:
                                <span class="badge bg-{{ $product->status == 1 ? 'success' : 'warning' }} ms-2">
                                    {{ $product->status == 1 ? 'Active' : 'Inactive' }}
                                </span>

                                @if($product->is_featured)
                                <span class="badge bg-info ms-2">
                                    <i class="bx bx-star"></i> Featured
                                </span>
                                @endif
                            </div>

                            <div class="mb-4">
                                @if ($product->discount_price)
                                <h4 class="text-primary">
                                    Sell Price:
                                    {{setting('currency')}}{{ number_format(
                                            $product->discount_type == 'percentage'
                                                ? $product->price - ($product->price * $product->discount_price) / 100
                                                : $product->price - $product->discount_price,
                                            2,
                                        ) }}
                                </h4>
                                <del class="text-muted">{{setting('currency')}}{{ number_format($product->price, 2) }}</del>
                                <span class="badge bg-danger ms-2">
                                    @if($product->discount_type == 'percentage')
                                    {{ $product->discount_price }}% OFF
                                    @else
                                    {{setting('currency')}}{{ $product->discount_price }} OFF
                                    @endif
                                </span>
                                @else
                                <h4 class="text-primary">{{setting('currency')}}{{ number_format($product->price, 2) }}</h4>
                                @endif
                                <p class="text-muted mt-2">Buying Cost: {{setting('currency')}}{{ number_format($product->buying_price, 2) }}</p>
                            </div>

                            <div class="product-meta mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                                        <p><strong>SKU:</strong> {{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p>
                                            <strong>Stock:</strong>
                                            <span class="badge @if($productStock > 10) bg-success @elseif($productStock > 0) bg-warning @else bg-danger @endif">
                                                {{ $productStock }} available
                                            </span>
                                        </p>
                                        <p><strong>Has Variants:</strong> {{ $product->has_variant ? 'Yes' : 'No' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="product-description mb-4">
                                <h5>Description</h5>
                                <div class="border p-3 rounded bg-light">
                                    {!! $product->long_description ?: '<span class="text-muted">No description available</span>' !!}
                                </div>
                            </div>

                            <!-- Additional Product Info -->
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong>Created By:</strong>
                                        @if($product->user && $product->user->hasRole('vendor'))
                                        {{ $product->user->name }}
                                        @else
                                        System
                                        @endif
                                    </p>
                                    <p><strong>Created At:</strong> {{ $product->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Updated At:</strong> {{ $product->updated_at->format('M d, Y h:i A') }}</p>
                                    @if($product->video)
                                    <p><strong>Video:</strong> <a href="{{ $product->video }}" target="_blank" class="text-primary">View Video</a></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Variants Section -->
                    @if ($product->has_variant && $product->variants->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">Product Variants ({{ $product->variants->count() }})</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>SKU</th>
                                            <th>Attributes</th>
                                            <th>Price Adjustment</th>
                                            <th>Final Price</th>
                                            <th>Stock</th>
                                            <th>Image</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product->variants as $variant)
                                        <tr>
                                            <td>
                                                <strong>{{ $variant->sku ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                @foreach ($variant->variantAttributeValues as $vav)
                                                <span class="badge bg-primary me-1 mb-1">
                                                    {{ $vav->attribute->name }}: {{ $vav->attributeValue->value }}
                                                </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($variant->varient_price > 0)
                                                <span class="text-success">+{{setting('currency')}}{{ number_format($variant->varient_price, 2) }}</span>
                                                @elseif($variant->varient_price < 0)
                                                    <span class="text-danger">-{{setting('currency')}}{{ number_format(abs($variant->varient_price), 2) }}</span>
                                                    @else
                                                    <span class="text-muted">{{setting('currency')}}0.00</span>
                                                    @endif
                                            </td>
                                            <td>
                                                <strong>{{setting('currency')}}{{ number_format($product->price + $variant->varient_price, 2) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                $variantStock = $variantStocks[$variant->id] ?? 0;
                                                @endphp
                                                <span class="badge @if($variantStock > 10) bg-success @elseif($variantStock > 0) bg-warning @else bg-danger @endif">
                                                    {{ $variantStock }} available
                                                </span>
                                            </td>
                                            <td>
                                                @if(!empty($variant->image))
                                                <img src="{{ asset('storage/' . $variant->image) }}"
                                                    alt="Variant Image" class="rounded border"
                                                    style="width:60px; height:60px; object-fit:cover;">
                                                @else
                                                <span class="text-muted">No Image</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Inventory History Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">Recent Stock History</h5>

                            @php
                            $recentInventory = \App\Models\Inventory::with(['user', 'variant'])
                            ->where('product_id', $product->id)
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();
                            @endphp

                            @if($recentInventory->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Variant</th>
                                            <th>Quantity</th>
                                            <th>Stock After</th>
                                            <th>Notes</th>
                                            <th>Seller</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentInventory as $history)
                                        <tr>
                                            <td>{{ $history->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <span class="badge @if($history->type == 'in') bg-success @else bg-danger @endif">
                                                    {{ $history->type == 'in' ? 'IN' : 'OUT' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($history->variant)
                                                {{ $history->variant->sku }}
                                                @else
                                                <span class="text-muted">Main Product</span>
                                                @endif
                                            </td>
                                            <td class="@if($history->type == 'in') text-success @else text-danger @endif">
                                                {{ $history->type == 'in' ? '+' : '-' }}{{ $history->quantity }}
                                            </td>
                                            <td>
                                                <strong>{{ $history->current_stock }}</strong>
                                            </td>
                                            <td>{{ $history->notes ?? '-' }}</td>
                                            <td>{{ $history->user->name ?? 'System' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle"></i> No stock history found for this product.
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // This will run every time the component updates (including initial load)
    Livewire.hook('component.init', ({
        component
    }) => {
        if (component.name === 'admin.products.product-view') {
            initProductGallery();
        }
    });

    // Also run when the DOM is updated (Livewire morphdom updates)
    Livewire.hook('morph.updated', (el) => {
        if (document.querySelector('.product-gallery')) {
            initProductGallery();
        }
    });

    function initProductGallery() {
        // Get all elements
        const thumbnails = document.querySelectorAll('.thumbnail-container .img-thumbnail');
        const mainProductImage = document.getElementById('mainProductImage');
        const videoThumbnail = document.querySelector('.video-thumbnail');
        const videoContainer = document.getElementById('videoContainer');
        const videoIframe = document.getElementById('videoIframe');

        // Remove existing event listeners to prevent duplicates
        thumbnails.forEach(thumb => {
            thumb.removeEventListener('click', handleThumbnailClick);
            if (!thumb.classList.contains('video-thumbnail')) {
                thumb.addEventListener('click', handleThumbnailClick);
            }
        });

        if (videoThumbnail) {
            videoThumbnail.removeEventListener('click', handleVideoThumbnailClick);
            videoThumbnail.addEventListener('click', handleVideoThumbnailClick);
        }

        function handleThumbnailClick() {
            // Update main image source
            mainProductImage.src = this.src;
            mainProductImage.style.display = 'block';

            // Hide video container if visible
            if (videoContainer) {
                videoContainer.style.display = 'none';
                videoIframe.src = '';
            }

            // Update active state
            document.querySelectorAll('.thumbnail-container .img-thumbnail').forEach(t => {
                t.classList.remove('border-primary', 'border-2');
            });
            this.classList.add('border-primary', 'border-2');
        }

        function handleVideoThumbnailClick() {
            const videoId = @js($videoId);
            if (videoId) {
                videoIframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
                videoContainer.style.display = 'block';
                mainProductImage.style.display = 'none';

                // Update active state
                document.querySelectorAll('.thumbnail-container .img-thumbnail').forEach(t => {
                    t.classList.remove('border-primary', 'border-2');
                });
                this.classList.add('border-primary', 'border-2');
            }
        }

        // Initialize first thumbnail as active if none are active
        const activeThumbnails = document.querySelectorAll('.thumbnail-container .img-thumbnail.border-primary');
        if (thumbnails.length > 0 && activeThumbnails.length === 0) {
            thumbnails[0].classList.add('border-primary', 'border-2');
        }
    }
</script>
@endscript