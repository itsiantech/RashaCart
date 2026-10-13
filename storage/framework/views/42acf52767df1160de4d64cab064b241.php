<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Category Wise Stock</h4>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="#" wire:click="selectCategory(0)" wire:navigate.prevent
                    <?php if(!$categoryId): ?> class="text-primary fw-semibold" <?php endif; ?>>All Categories</a>
            </li>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $breadcrumb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="breadcrumb-item <?php echo e($loop->last ? 'active' : ''); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
                        <a href="#" wire:click="selectCategory(<?php echo e($bc->id); ?>)"><?php echo e($bc->name); ?></a>
                    <?php else: ?>
                        <?php echo e($bc->name); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ol>
    </nav>

    <!-- Category Cards -->
    <div class="row g-4 mb-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $displayCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $agg = $aggregate[$cat->id] ?? ['units' => 0, 'value' => 0, 'product_count' => 0]; ?>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 cursor-pointer" wire:click="selectCategory(<?php echo e($cat->id); ?>)" style="cursor: pointer">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-sitemap"></i></span>
                            </div>
                            <span class="badge bg-label-secondary"><?php echo e($agg['product_count']); ?> products</span>
                        </div>
                        <h6 class="mb-1"><?php echo e($cat->name); ?></h6>
                        <p class="text-muted mb-2 small">Total Units: <strong><?php echo e(number_format($agg['units'])); ?></strong></p>
                        <p class="text-success mb-0 small fw-semibold">Stock Value: ৳<?php echo e(number_format($agg['value'], 2)); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="alert alert-light text-muted text-center mb-0">No sub categories found</div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentCategory): ?>
        <!-- Products in this category -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Products in "<?php echo e($currentCategory->name); ?>"</h5>
                <button type="button" wire:click="selectCategory(<?php echo e($currentCategory->parent_id ?? 0); ?>)" class="btn btn-sm btn-label-secondary">
                    <i class="bx bx-left-arrow-alt me-1"></i> Back
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="12%">SKU</th>
                                <th width="35%">Product</th>
                                <th width="14%">Available</th>
                                <th width="12%">Reserved</th>
                                <th width="22%">Stock Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e(($products->currentPage() - 1) * $products->perPage() + $i + 1); ?></td>
                                    <td><strong><?php echo e($p->sku); ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->thumbnail): ?>
                                                <img src="<?php echo e(asset($p->thumbnail)); ?>" class="rounded me-2" width="36" height="36" style="object-fit: cover">
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <span><?php echo e(Str::limit($p->name, 50)); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->has_variant): ?>
                                                <span class="badge bg-label-secondary ms-1">Variant</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                    <td><strong><?php echo e(number_format($p->available_stock)); ?></strong> pcs</td>
                                    <td><span class="badge bg-label-info"><?php echo e(number_format($p->reserved_stock)); ?></span></td>
                                    <td>৳<?php echo e(number_format($p->stock_value, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No products directly in this category</td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <?php echo e($products->links()); ?>

                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/inventory/category-wise-stock.blade.php ENDPATH**/ ?>