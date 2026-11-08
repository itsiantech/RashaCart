

<div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Manage Stock</h4>
            <p class="text-muted mb-0">
                Campaign: <strong><?php echo e($flashSale->title); ?></strong>
                &nbsp;|&nbsp;
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($flashSale->status === 'live'): ?>
                <span class="badge bg-success"><i class="bx bx-radio-circle-marked bx-flashing me-1"></i> Live</span>
                <?php elseif($flashSale->status === 'upcoming'): ?>
                <span class="badge bg-warning text-dark"><i class="bx bx-time me-1"></i> Upcoming</span>
                <?php else: ?>
                <span class="badge bg-secondary"><i class="bx bx-x-circle me-1"></i> Expired</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
        <a wire:navigate href="<?php echo e(route('admin.flash.sales.index')); ?>" class="btn btn-outline-secondary">
            <i class="tf-icons bx bx-arrow-back me-1"></i> Back
        </a>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Total Products</p>
                    <h4 class="mb-0"><?php echo e($flashSale->items->count()); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Out of Stock</p>
                    <h4 class="mb-0 text-danger">
                        <?php echo e($flashSale->items->filter(fn($i) => ($i->product->stock ?? 0) <= 0)->count()); ?>

                    </h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Low Stock (≤10)</p>
                    <h4 class="mb-0 text-warning">
                        <?php echo e($flashSale->items->filter(fn($i) => ($i->product->stock ?? 0) > 0 && ($i->product->stock ?? 0) <= 10)->count()); ?>

                    </h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">In Stock</p>
                    <h4 class="mb-0 text-success">
                        <?php echo e($flashSale->items->filter(fn($i) => ($i->product->stock ?? 0) > 10)->count()); ?>

                    </h4>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0">
                <i class="tf-icons bx bx-package me-2 text-primary"></i>
                Product Stock Levels
            </h6>
            <small class="text-muted">Update each product's stock individually</small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Sale Price</th>
                        <th>Discount</th>
                        <th>Current Stock</th>
                        <th style="width: 180px;">New Stock</th>
                        <th>Update</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $flashSale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $currentStock = $item->product->stock ?? 0; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>

                        
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product->thumbnail ?? false): ?>
                                <img
                                    src="<?php echo e(asset('storage/' . $item->product->thumbnail)); ?>"
                                    width="40" height="40"
                                    class="rounded"
                                    style="object-fit:cover;" />
                                <?php else: ?>
                                <div class="avatar avatar-sm">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="bx bx-image"></i>
                                    </span>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div>
                                    <span class="fw-semibold d-block" style="font-size:13px;"><?php echo e($item->product->name); ?></span>
                                    <small class="text-muted">ID: <?php echo e($item->product->id); ?></small>
                                </div>
                            </div>
                        </td>

                        
                        <td>
                            <span class="fw-bold text-success">৳<?php echo e(number_format($item->discounted_price, 2)); ?></span>
                            <br>
                            <small class="text-muted text-decoration-line-through">৳<?php echo e(number_format($item->product->price, 2)); ?></small>
                        </td>

                        
                        <td>
                            <span class="badge bg-label-danger"><?php echo e(number_format($item->discount_percent, 0)); ?>% OFF</span>
                        </td>

                        
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentStock <= 0): ?>
                                <span class="badge bg-danger">Out of Stock</span>
                                <?php elseif($currentStock <= 10): ?>
                                    <span class="badge bg-warning text-dark">
                                    <i class="bx bx-error-circle me-1"></i><?php echo e($currentStock); ?> Low
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-success">
                                        <i class="bx bx-check-circle me-1"></i><?php echo e($currentStock); ?>

                                    </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>

                        
                        <td>
                            <div class="input-group input-group-sm">
                                <button
                                    type="button"
                                    onclick="decrementStock(<?php echo e($item->id); ?>)"
                                    class="btn btn-outline-secondary px-2">
                                    <i class="bx bx-minus"></i>
                                </button>
                                <input
                                    type="number"
                                    wire:model="stocks.<?php echo e($item->id); ?>"
                                    id="stock-input-<?php echo e($item->id); ?>"
                                    class="form-control text-center"
                                    min="0"
                                    style="max-width:70px;" />
                                <button
                                    type="button"
                                    onclick="incrementStock(<?php echo e($item->id); ?>)"
                                    class="btn btn-outline-secondary px-2">
                                    <i class="bx bx-plus"></i>
                                </button>
                            </div>
                        </td>

                        
                        <td>
                            <button
                                wire:click="updateStock(<?php echo e($item->id); ?>)"
                                wire:loading.attr="disabled"
                                wire:target="updateStock(<?php echo e($item->id); ?>)"
                                class="btn btn-info btn-sm">
                                <span wire:loading wire:target="updateStock(<?php echo e($item->id); ?>)">
                                    <i class="bx bx-loader-alt bx-spin"></i>
                                </span>
                                <span wire:loading.remove wire:target="updateStock(<?php echo e($item->id); ?>)">
                                    <i class="tf-icons bx bx-save me-1"></i> Save
                                </span>
                            </button>
                        </td>
                        <td>
                            <a href="javascript:void(0);"
                                wire:click="confirmRemoveItem(<?php echo e($item->id); ?>)"
                                title="Remove from Flash Sale">
                                <button type="button" class="btn btn-danger" style="padding: 12px 10px;">Delete</button>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bx bx-package fs-2 d-block mb-2"></i>
                            No products found in this campaign.
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="bx bx-error-circle me-1"></i> Remove Product
                    </h5>
                </div>
                <div class="modal-body">
                    <p>Remove <strong><?php echo e($deleteItemName); ?></strong> from this flash sale campaign?</p>
                    <small class="text-muted">This only removes it from the campaign, the product itself won't be deleted.</small>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" wire:click="cancelRemove" class="btn btn-outline-secondary btn-sm">
                        Cancel
                    </button>
                    <button type="button" wire:click="removeItem" class="btn btn-danger btn-sm">
                        <span wire:loading wire:target="removeItem" class="spinner-border spinner-border-sm me-1"></span>
                        <span wire:loading.remove wire:target="removeItem">Remove</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function incrementStock(itemId) {
        const input = document.getElementById('stock-input-' + itemId);
        if (input) {
            input.value = parseInt(input.value || 0) + 1;
            input.dispatchEvent(new Event('input'));
        }
    }

    function decrementStock(itemId) {
        const input = document.getElementById('stock-input-' + itemId);
        if (input) {
            const val = parseInt(input.value || 0);
            if (val > 0) {
                input.value = val - 1;
                input.dispatchEvent(new Event('input'));
            }
        }
    }

    document.addEventListener('livewire:initialized', () => {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        Livewire.on('show-delete-modal', () => deleteModal.show());
        Livewire.on('hide-delete-modal', () => deleteModal.hide());
    });
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\offers\flash-sales-stock-manage.blade.php ENDPATH**/ ?>