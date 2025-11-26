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
                            </div>

                            <div class="mb-4">
                                @if ($product->discount_price)
                                    <h4 class="text-primary">
                                        Sell Price:
                                        ৳{{ number_format(
                                            $product->discount_type == 'percentage'
                                                ? $product->price - ($product->price * $product->discount_price) / 100
                                                : $product->price - $product->discount_price,
                                            2,
                                        ) }}
                                    </h4>
                                    <del class="text-muted">৳{{ number_format($product->price, 2) }}</del>
                                @else
                                    <h4 class="text-primary">৳{{ number_format($product->price, 2) }}</h4>
                                @endif
                                <p class="text-muted mt-2">Buying Cost: ৳{{ number_format($product->buying_price, 2) }}
                                </p>
                            </div>

                            <div class="product-meta mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                                        <p><strong>Subcategory:</strong> {{ $product->subcategory->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Stock:</strong> {{ $product->stock ?? 0 }} available</p>
                                        <p><strong>Has Variants:</strong> {{ $product->has_variant ? 'Yes' : 'No' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="product-description mb-4">
                                <h5>Description</h5>
                                <div class="border p-3 rounded bg-light">
                                    {!! $product->long_description !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Variants Section -->
                    @if ($product->has_variant && $product->variants->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">Variants ({{ $product->variants->count() }})</h5>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Attributes</th>
                                                <th>SKU</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Images</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->variants as $variant)
                                                <tr>
                                                    <td>
                                                        @foreach ($variant->variantAttributeValues as $vav)
                                                            <span class="badge bg-dark text-white me-1">
                                                                {{ $vav->attribute->name }}:
                                                                {{ $vav->attributeValue->value }}
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $variant->sku ?? 'N/A' }}</td>
                                                    <td>{{ settings('currency') }}{{ number_format($variant->price_diff, 2) }}
                                                    </td>
                                                    <td>{{ $variant->stock ?? 0 }}</td>
                                                    <td>
                                                        @if (!empty($variant->image_gallery))
                                                            <div class="d-flex flex-wrap gap-2">
                                                                @foreach ($variant->image_gallery as $img)
                                                                    <img src="{{ asset('storage/' . $img) }}"
                                                                        alt="Variant Image" class="rounded border"
                                                                        style="width:60px; height:60px; object-fit:cover;">
                                                                @endforeach
                                                            </div>
                                                        @elseif(!empty($variant->image))
                                                            <img src="{{ asset('storage/' . $variant->image) }}"
                                                                alt="Variant Image" class="rounded border"
                                                                style="width:60px; height:60px; object-fit:cover;">
                                                        @else
                                                            N/A
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
