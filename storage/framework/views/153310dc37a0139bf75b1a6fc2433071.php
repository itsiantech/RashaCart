<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Product Details</h5>
                    <div>
                        <a href="<?php echo e(route('admin.product.edit', $product->id)); ?>" class="btn btn-sm btn-primary">
                            <i class="bx bx-edit"></i> Edit
                        </a>
                        <a href="<?php echo e(route('admin.product.index')); ?>" class="btn btn-sm btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Product Main Gallery -->
                        <div class="col-md-5">
                            <?php
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
                            ?>

                            <div class="main-display mb-3 position-relative" style="min-height: 300px;">
                                <img id="mainProductImage" src="<?php echo e($mainImage); ?>" alt="<?php echo e($product->name); ?>"
                                    class="img-fluid rounded border w-100" style="display: block;">

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($videoId): ?>
                                <div id="videoContainer" class="ratio ratio-16x9" style="display: none;">
                                    <iframe id="videoIframe" src="" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="thumbnail-container d-flex flex-wrap gap-2">
                                <img src="<?php echo e($mainImage); ?>" alt="Main Thumbnail" class="img-thumbnail cursor-pointer"
                                    style="width:80px; height:80px; object-fit:cover;">

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($product->gallery_image)): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->gallery_image; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <img src="<?php echo e(asset('storage/' . $img)); ?>" alt="Gallery Image"
                                    class="img-thumbnail cursor-pointer"
                                    style="width:80px; height:80px; object-fit:cover;">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($videoId): ?>
                                <div class="img-thumbnail cursor-pointer video-thumbnail position-relative"
                                    style="width:80px; height:80px; object-fit:cover;">
                                    <img src="https://img.youtube.com/vi/<?php echo e($videoId); ?>/hqdefault.jpg"
                                        alt="Video Thumbnail" class="w-100 h-100" />
                                    <div class="position-absolute top-50 start-50 translate-middle text-white"
                                        style="font-size:2rem;">
                                        <i class="bx bx-play-circle"></i>
                                    </div>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="col-md-7">
                            <h2><?php echo e($product->name); ?></h2>
                            <p class="text-muted"><?php echo e($product->short_description); ?></p>

                            <div class="d-flex align-items-center mb-3">
                                Status:
                                <span class="badge bg-<?php echo e($product->status == 1 ? 'success' : 'warning'); ?> ms-2">
                                    <?php echo e($product->status == 1 ? 'Active' : 'Inactive'); ?>

                                </span>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->is_featured): ?>
                                <span class="badge bg-info ms-2">
                                    <i class="bx bx-star"></i> Featured
                                </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->discount_price): ?>
                                <h4 class="text-primary">
                                    Sell Price:
                                    <?php echo e(setting('currency')); ?><?php echo e(number_format(
                                            $product->discount_type == 'percentage'
                                                ? $product->price - ($product->price * $product->discount_price) / 100
                                                : $product->price - $product->discount_price,
                                            2,
                                        )); ?>

                                </h4>
                                <del class="text-muted"><?php echo e(setting('currency')); ?><?php echo e(number_format($product->price, 2)); ?></del>
                                <span class="badge bg-danger ms-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->discount_type == 'percentage'): ?>
                                    <?php echo e($product->discount_price); ?>% OFF
                                    <?php else: ?>
                                    <?php echo e(setting('currency')); ?><?php echo e($product->discount_price); ?> OFF
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <?php else: ?>
                                <h4 class="text-primary"><?php echo e(setting('currency')); ?><?php echo e(number_format($product->price, 2)); ?></h4>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <p class="text-muted mt-2">Buying Cost: <?php echo e(setting('currency')); ?><?php echo e(number_format($product->buying_price, 2)); ?></p>
                            </div>

                            <div class="product-meta mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Category:</strong> <?php echo e($product->category->name ?? 'N/A'); ?></p>
                                        <p><strong>SKU:</strong> <?php echo e($product->sku ?? 'N/A'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p>
                                            <strong>Stock:</strong>
                                            <span class="badge <?php if($productStock > 10): ?> bg-success <?php elseif($productStock > 0): ?> bg-warning <?php else: ?> bg-danger <?php endif; ?>">
                                                <?php echo e($productStock); ?> available
                                            </span>
                                        </p>
                                        <p><strong>Has Variants:</strong> <?php echo e($product->has_variant ? 'Yes' : 'No'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="product-description mb-4">
                                <h5>Description</h5>
                                <div class="border p-3 rounded bg-light">
                                    <?php echo $product->long_description ?: '<span class="text-muted">No description available</span>'; ?>

                                </div>
                            </div>

                            <!-- Additional Product Info -->
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong>Created By:</strong>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->user && $product->user->hasRole('vendor')): ?>
                                        <?php echo e($product->user->name); ?>

                                        <?php else: ?>
                                        System
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                    <p><strong>Created At:</strong> <?php echo e($product->created_at->format('M d, Y h:i A')); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Updated At:</strong> <?php echo e($product->updated_at->format('M d, Y h:i A')); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->video): ?>
                                    <p><strong>Video:</strong> <a href="<?php echo e($product->video); ?>" target="_blank" class="text-primary">View Video</a></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Variants Section -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->has_variant && $product->variants->count() > 0): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">Product Variants (<?php echo e($product->variants->count()); ?>)</h5>

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
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($variant->sku ?? 'N/A'); ?></strong>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variant->variantAttributeValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge bg-primary me-1 mb-1">
                                                    <?php echo e($vav->attribute->name); ?>: <?php echo e($vav->attributeValue->value); ?>

                                                </span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->varient_price > 0): ?>
                                                <span class="text-success">+<?php echo e(setting('currency')); ?><?php echo e(number_format($variant->varient_price, 2)); ?></span>
                                                <?php elseif($variant->varient_price < 0): ?>
                                                    <span class="text-danger">-<?php echo e(setting('currency')); ?><?php echo e(number_format(abs($variant->varient_price), 2)); ?></span>
                                                    <?php else: ?>
                                                    <span class="text-muted"><?php echo e(setting('currency')); ?>0.00</span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo e(setting('currency')); ?><?php echo e(number_format($product->price + $variant->varient_price, 2)); ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $variantStock = $variantStocks[$variant->id] ?? 0;
                                                ?>
                                                <span class="badge <?php if($variantStock > 10): ?> bg-success <?php elseif($variantStock > 0): ?> bg-warning <?php else: ?> bg-danger <?php endif; ?>">
                                                    <?php echo e($variantStock); ?> available
                                                </span>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($variant->image)): ?>
                                                <img src="<?php echo e(asset('storage/' . $variant->image)); ?>"
                                                    alt="Variant Image" class="rounded border"
                                                    style="width:60px; height:60px; object-fit:cover;">
                                                <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <!-- Inventory History Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">Recent Stock History</h5>

                            <?php
                            $recentInventory = \App\Models\Inventory::with(['user', 'variant'])
                            ->where('product_id', $product->id)
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();
                            ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentInventory->count() > 0): ?>
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
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentInventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($history->created_at->format('M d, Y H:i')); ?></td>
                                            <td>
                                                <span class="badge <?php if($history->type == 'in'): ?> bg-success <?php else: ?> bg-danger <?php endif; ?>">
                                                    <?php echo e($history->type == 'in' ? 'IN' : 'OUT'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($history->variant): ?>
                                                <?php echo e($history->variant->sku); ?>

                                                <?php else: ?>
                                                <span class="text-muted">Main Product</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="<?php if($history->type == 'in'): ?> text-success <?php else: ?> text-danger <?php endif; ?>">
                                                <?php echo e($history->type == 'in' ? '+' : '-'); ?><?php echo e($history->quantity); ?>

                                            </td>
                                            <td>
                                                <strong><?php echo e($history->current_stock); ?></strong>
                                            </td>
                                            <td><?php echo e($history->notes ?? '-'); ?></td>
                                            <td><?php echo e($history->user->name ?? 'System'); ?></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle"></i> No stock history found for this product.
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

    <?php
        $__scriptKey = '7595634-0';
        ob_start();
    ?>
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
            const videoId = <?php echo \Illuminate\Support\Js::from($videoId)->toHtml() ?>;
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
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/products/product-view.blade.php ENDPATH**/ ?>