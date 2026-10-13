<div>
    <div class="col-xxl">
        <!-- Products List -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <!-- Title + Tabs Column -->
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h5 class="mb-2">Product Management</h5>
                        <ul class="nav nav-pills gap-2">
                            <li class="nav-item">
                                <button type="button"
                                        class="nav-link py-1 <?php echo e($tab === 'all' ? 'active' : ''); ?>"
                                        wire:click="$set('tab', 'all')">
                                    <i class="bx bx-package me-1"></i>All Products
                                    <span class="badge <?php echo e($tab === 'all' ? 'bg-white text-primary' : 'bg-primary'); ?> ms-1"><?php echo e($countAll); ?></span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button"
                                        class="nav-link py-1 <?php echo e($tab === 'trash' ? 'active' : ''); ?>"
                                        wire:click="$set('tab', 'trash')">
                                    <i class="bx bx-trash me-1"></i>Trash
                                    <span class="badge <?php echo e($tab === 'trash' ? 'bg-white text-danger' : 'bg-danger'); ?> ms-1"><?php echo e($countTrash); ?></span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Search and Add Button Column -->
                    <div class="col-md-6 col-12">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <!-- Search Input -->
                            <div class="flex-grow-1" style="min-width: 150px;">
                                <input
                                    type="text"
                                    class="form-control"
                                    wire:model.live="search"
                                    placeholder="Search products..."
                                >
                            </div>

                            <div class="flex-shrink-0">
                                <a wire:navigate href="<?php echo e(route('admin.product.create')); ?>"
                                   class="btn btn-primary d-flex align-items-center"
                                   style="white-space: nowrap;"
                                >
                                    <i class="bx bx-plus me-1"></i>
                                    <span>Add New</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Action Bar -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCount > 0): ?>
                <div class="border-top border-bottom px-3 py-2 d-flex align-items-center flex-wrap gap-2"
                     style="background-color: rgba(115, 103, 240, 0.08);">
                    <span class="badge bg-primary">
                        <?php echo e($selectedCount); ?> product(s) selected
                    </span>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'all'): ?>
                        <button type="button"
                                class="btn btn-sm btn-danger"
                                wire:click="bulkTrash"
                                wire:confirm="Move the selected product(s) to trash?">
                            <i class="bx bx-trash me-1"></i> Move to Trash
                        </button>
                    <?php else: ?>
                        <button type="button"
                                class="btn btn-sm btn-success"
                                wire:click="bulkRestore">
                            <i class="bx bx-rotate-left me-1"></i> Restore
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-danger"
                                wire:click="bulkDelete"
                                wire:confirm="Permanently delete the selected product(s)? This action cannot be undone and related data will be removed!">
                            <i class="bx bx-trash me-1"></i> Permanent Delete
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           title="Select all products on this page"
                                           wire:click="selectAll"
                                           <?php if($allSelected): echo 'checked'; endif; ?>>
                                    Product Info
                                </div>
                            </th>
                            <th>Pricing</th>
                            <th>Orders</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->isEmpty()): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bx bx-package fs-1"></i>
                                        <p class="mt-2"><?php echo e($tab === 'trash' ? 'Trash is empty' : 'No products found'); ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $totalStock = $this->getTotalStock($product);
                                $stockBadgeClass = $this->getStockBadgeClass($totalStock);
                                $mainStock = $this->calculateProductStock($product);
                                $isTrash = $tab === 'trash';
                            ?>
                            <tr>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isTrash): ?>
                                        <img src="<?php echo e($product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('assets/admin/img/avatars/user-avater.png')); ?>"
                                             alt="<?php echo e($product->name); ?>"
                                             class="rounded"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <a href="<?php echo e(route('frontend.product.single.view', $product->slug)); ?>"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="position-relative">
                                            <img src="<?php echo e($product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('assets/admin/img/avatars/user-avater.png')); ?>"
                                                 alt="<?php echo e($product->name); ?>"
                                                 class="rounded"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->is_featured): ?>
                                                <span class="badge bg-info position-absolute top-0 start-0 mt-1 ms-1" style="font-size: 8px;">
                                                    <i class="bx bx-star"></i>
                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-2">
                                        <input type="checkbox"
                                               class="form-check-input mt-1"
                                               wire:model="selected.<?php echo e($product->id); ?>">
                                        <div>
                                            <div class="fw-semibold"><?php echo e(Str::limit($product->name, 35)); ?></div>
                                            <div class="text-muted small mt-1">
                                                SKU: <?php echo e($product->sku ?? 'N/A'); ?>

                                            </div>
                                            <div class="text-muted small">
                                                <?php echo e($product->category->name ?? 'No Category'); ?>

                                            </div>
                                            <div class="text-muted small">
                                                Brand: <?php echo e($product->brand->name ?? 'No Brand'); ?>

                                            </div>
                                            <div class="text-muted small">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isTrash): ?>
                                                    <span class="text-danger">Deleted: <?php echo e($product->deleted_at->format('d M Y')); ?></span>
                                                <?php else: ?>
                                                    Added: <?php echo e($product->created_at->format('d M Y')); ?>

                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->has_variant): ?>
                                                <span class="badge bg-primary mt-1" style="font-size: 10px;">
                                                    <?php echo e($product->variants->count()); ?> Variants
                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold text-primary">
                                        ৳<?php echo e(number_format($product->sale_price, 2)); ?>

                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->discount_price): ?>
                                        <div class="text-danger small mt-1">
                                            <i class="bx bx-discount"></i>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->discount_type == 'percentage'): ?>
                                                <?php echo e($product->discount_price); ?>% OFF
                                            <?php else: ?>
                                                ৳<?php echo e(number_format($product->discount_price, 2)); ?> OFF
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="text-muted small">
                                        Cost: ৳<?php echo e(number_format($product->buying_price, 2)); ?>

                                    </div>
                                </td>

                                <td>
                                    <div class="text-center">
                                        <span class="fw-semibold fs-6"><?php echo e($product->orderitems->count()); ?></span>
                                        <div class="text-muted small">Orders</div>
                                    </div>
                                </td>

                                <td>
                                    <div class="text-center">
                                        <span class="badge <?php echo e($stockBadgeClass); ?> fs-6 px-2 py-1">
                                            <?php echo e($totalStock); ?>

                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->has_variant): ?>
                                            <div class="text-muted small mt-1">
                                                Main: <?php echo e($mainStock); ?>

                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>

                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isTrash): ?>
                                        <span class="badge bg-dark">Trashed</span>
                                    <?php else: ?>
                                        <span class="badge <?php echo e($product->status ? 'bg-success' : 'bg-danger'); ?>">
                                            <?php echo e($product->status ? 'Active' : 'Inactive'); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isTrash): ?>
                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    wire:click="restoreProduct(<?php echo e($product->id); ?>)"
                                                    title="Restore Product"
                                                    data-bs-toggle="tooltip">
                                                <i class="bx bx-rotate-left"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    wire:click="deleteProduct(<?php echo e($product->id); ?>)"
                                                    wire:confirm="Permanently delete this product? This action cannot be undone and related data will be removed!"
                                                    title="Permanent Delete"
                                                    data-bs-toggle="tooltip">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth()->user()->can('admin.product.stock.manage')): ?>
                                                <a wire:navigate href="<?php echo e(route('admin.product.stock.manage', $product->id)); ?>"
                                                   class="btn btn-sm btn-success"
                                                   title="Manage Stock"
                                                   data-bs-toggle="tooltip">
                                                    <i class="bx bxs-analyse"></i>inventory
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <a wire:navigate href="<?php echo e(route('admin.product.view', $product->id)); ?>"
                                               class="btn btn-sm btn-info"
                                               title="View Details"
                                               data-bs-toggle="tooltip">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <a wire:navigate href="<?php echo e(route('admin.product.edit', $product->id)); ?>"
                                               class="btn btn-sm btn-warning"
                                               title="Edit Product"
                                               data-bs-toggle="tooltip">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button
                                                class="btn btn-sm btn-danger"
                                                wire:click="trashProduct(<?php echo e($product->id); ?>)"
                                                wire:confirm="Move this product to trash?"
                                                title="Move to Trash"
                                                data-bs-toggle="tooltip"
                                            >
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="p-3 border-top">
                    <?php echo e($products->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

    <?php
        $__scriptKey = '1047468259-0';
        ob_start();
    ?>
<script>
    // Initialize tooltips
    document.addEventListener('livewire:initialized', () => {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/products/product-index.blade.php ENDPATH**/ ?>