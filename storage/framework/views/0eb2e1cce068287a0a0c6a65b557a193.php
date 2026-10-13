<div>
    <div class="card mb-4">
        <div class="card-header">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Stock Management - <?php echo e($product->name); ?></h5>
                <div>                       
                    <a wire:navigate href="<?php echo e(route('admin.product.index')); ?>" class="btn btn-sm btn-primary">
                        <i class="bx bx-left-arrow-alt"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Main Product Stock -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Main Product Stock</h6>
                                <p class="mb-0 text-muted">Current stock:
                                    <strong class="text-primary"><?php echo e($productStock); ?></strong>
                                </p>
                            </div>
                            <button type="button" wire:click="showAddInventory()" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Manage Stock
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variants Stock -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->has_variant && $product->variants->count() > 0): ?>
                <div class="row">
                   
                        <div class="col-md-12">
                            <div class="border rounded p-3 bg-light">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Variants Stock</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="15%">SKU</th>
                                                    <th width="25%">Attributes</th>
                                                    <th width="12%">Price</th>
                                                    <th width="10%">Current Stock</th>
                                                    <th width="15%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $variantStock = $variantStocks[$variant->id] ?? 0;
                                                        $stockBadgeClass = $this->getStockBadgeClass($variantStock);
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo e($variant->sku); ?></strong>
                                                        </td>
                                                        <td>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variant->variantAttributeValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <span class="badge bg-primary me-1 mb-1">
                                                                    <?php echo e($vav->attribute->name); ?>:
                                                                    <?php echo e($vav->attributeValue->value); ?>

                                                                </span>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <strong>৳<?php echo e(number_format($product->price + $variant->varient_price, 2)); ?></strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?php echo e($stockBadgeClass); ?>">
                                                                <?php echo e($variantStock); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                wire:click="showAddInventory(<?php echo e($variant->id); ?>)"
                                                                class="btn btn-primary btn-sm">
                                                                <i class="fas fa-edit"></i> Manage Stock
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                  
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Inventory History -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <h6 class="mb-3">Stock History</h6>
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
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $inventoryHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($history->created_at->format('M d, Y H:i')); ?></td>
                                        <td>
                                            <span
                                                class="badge <?php if($history->type == 'in'): ?> bg-success <?php else: ?> bg-danger <?php endif; ?>">
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
                                        <td
                                            class="<?php if($history->type == 'in'): ?> text-success <?php else: ?> text-danger <?php endif; ?>">
                                            <?php echo e($history->type == 'in' ? '+' : '-'); ?><?php echo e($history->quantity); ?>

                                        </td>
                                        <td>
                                            <strong><?php echo e($history->current_stock); ?></strong>
                                        </td>
                                        <td><?php echo e($history->notes ?? '-'); ?></td>
                                        <td><?php echo e($history->user->name ?? 'System'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No stock history found</td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Modal -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showInventoryModal): ?>
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5)" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedVariantId): ?>
                                Manage Variant Stock
                            <?php else: ?>
                                Manage Product Stock
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h5>
                        <button type="button" wire:click="closeInventoryModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select wire:model="inventoryType" class="form-select">
                                <option value="in">Stock In (Add)</option>
                                <option value="out">Stock Out (Remove)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" wire:model="inventoryQuantity" class="form-control" min="1">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea wire:model="inventoryNotes" class="form-control" rows="3" placeholder="Reason for stock change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeInventoryModal"
                            class="btn btn-secondary">Cancel</button>
                        <button type="button" wire:click="addInventory" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Stock
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/products/product-stock-manage.blade.php ENDPATH**/ ?>