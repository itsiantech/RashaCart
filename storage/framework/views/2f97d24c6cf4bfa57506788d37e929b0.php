<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Attribute / Variant Stock</h4>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected): ?>
        <!-- Variant Matrix -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected->thumbnail): ?>
                        <img src="<?php echo e(asset($selected->thumbnail)); ?>" class="rounded me-2" width="42" height="42" style="object-fit: cover">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div>
                        <h5 class="mb-0"><?php echo e($selected->name); ?></h5>
                        <small class="text-muted">SKU: <?php echo e($selected->sku); ?></small>
                    </div>
                </div>
                <button type="button" wire:click="selectProduct(0)" class="btn btn-sm btn-label-secondary">
                    <i class="bx bx-left-arrow-alt me-1"></i> Back
                </button>
            </div>
            <div class="card-body">
                <!-- Matrix Metrics -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h5 class="mb-0"><?php echo e(number_format($metrics['total_variants'])); ?></h5>
                            <small class="text-muted">Total Variants</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h5 class="mb-0 text-info"><?php echo e(number_format($metrics['total_units'])); ?></h5>
                            <small class="text-muted">Total Units</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h5 class="mb-0 text-warning"><?php echo e(number_format($metrics['low'])); ?></h5>
                            <small class="text-muted">Low Stock Variants</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h5 class="mb-0 text-danger"><?php echo e(number_format($metrics['out'])); ?></h5>
                            <small class="text-muted">Out of Stock Variants</small>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">SKU</th>
                                <th width="30%">Attributes (Variant Matrix)</th>
                                <th width="12%">Price</th>
                                <th width="12%">Available Stock</th>
                                <th width="12%">Reserved</th>
                                <th width="12%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $matrix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($row['sku']); ?></strong></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $row['attributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-label-primary me-1 mb-1">
                                                <?php echo e($attr['name']); ?>: <?php echo e($attr['value']); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>৳<?php echo e(number_format($row['price'], 2)); ?></td>
                                    <td>
                                        <strong><?php echo e(number_format($row['available'])); ?></strong> pcs
                                    </td>
                                    <td><span class="badge bg-label-info"><?php echo e(number_format($row['reserved'])); ?></span></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_out']): ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php elseif($row['is_low']): ?>
                                            <span class="badge bg-warning">Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">In Stock</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No variants found for this product</td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Product List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Variant Products</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                    placeholder="Search product name or SKU...">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="12%">SKU</th>
                            <th width="45%">Product</th>
                            <th width="15%">Variants</th>
                            <th width="15%">Action</th>
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
                                        <span><?php echo e(Str::limit($p->name, 60)); ?></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-label-primary"><?php echo e($p->variants_count); ?> variants</span></td>
                                <td>
                                    <button type="button" wire:click="selectProduct(<?php echo e($p->id); ?>)" class="btn btn-sm btn-primary">
                                        <i class="bx bx-grid-alt me-1"></i> View Matrix
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No variant products found</td>
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
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/inventory/variant-stock.blade.php ENDPATH**/ ?>