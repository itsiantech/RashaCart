

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Manage Stock</h4>
            <p class="text-muted mb-0">Campaign: <strong><?php echo e($flashSale->title); ?></strong></p>
        </div>
        <a wire:navigate href="<?php echo e(route('admin.flash.sales.view', $flashSale->id)); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back to Campaign
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bx bx-check-circle me-1"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Sale Price</th>
                        <th>Current Stock</th>
                        <th>Update Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $flashSale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product->image): ?>
                                        <img src="<?php echo e(asset('storage/' . $item->product->image)); ?>" width="40" height="40" class="rounded object-fit-cover" />
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                            <i class="bx bx-image text-muted"></i>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="fw-semibold small"><?php echo e($item->product->name); ?></span>
                                </div>
                            </td>
                            <td class="text-success fw-bold">৳<?php echo e(number_format($item->discounted_price, 2)); ?></td>
                            <td>
                                <span class="badge bg-label-<?php echo e($item->product->stock > 10 ? 'success' : ($item->product->stock > 0 ? 'warning' : 'danger')); ?> fs-6">
                                    <?php echo e($item->product->stock); ?>

                                </span>
                            </td>
                            <td style="width: 150px;">
                                <input
                                    type="number"
                                    wire:model="stocks.<?php echo e($item->id); ?>"
                                    class="form-control form-control-sm"
                                    min="0"
                                    placeholder="Stock"
                                />
                            </td>
                            <td>
                                <button
                                    wire:click="updateStock(<?php echo e($item->id); ?>)"
                                    wire:loading.attr="disabled"
                                    wire:target="updateStock(<?php echo e($item->id); ?>)"
                                    class="btn btn-primary btn-sm"
                                >
                                    <span wire:loading wire:target="updateStock(<?php echo e($item->id); ?>)" class="spinner-border spinner-border-sm me-1"></span>
                                    <i wire:loading.remove wire:target="updateStock(<?php echo e($item->id); ?>)" class="bx bx-save me-1"></i>
                                    Update
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No products in this campaign.</td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\offers\flash-sales-view.blade.php ENDPATH**/ ?>