<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Supplier Stock (Purchase Orders)</h4>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-receipt"></i></span>
                        </div>
                        <h5 class="mb-0"><?php echo e(number_format($metrics['total_pos'])); ?></h5>
                    </div>
                    <small class="text-muted">Total Purchase Orders</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-cube"></i></span>
                        </div>
                        <h5 class="mb-0"><?php echo e(number_format($metrics['total_qty'])); ?></h5>
                    </div>
                    <small class="text-muted">Total Received Quantity</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-taka"></i></span>
                        </div>
                        <h5 class="mb-0">৳<?php echo e(number_format($metrics['total_value'], 2)); ?></h5>
                    </div>
                    <small class="text-muted">Total Purchase Value (COGS)</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-credit-card"></i></span>
                        </div>
                        <h5 class="mb-0">৳<?php echo e(number_format($metrics['total_due'], 2)); ?></h5>
                    </div>
                    <small class="text-muted">Total Due</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="PO number or supplier...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select wire:model.live="supplierId" class="form-select">
                        <option value="">All Suppliers</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date Range</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" wire:model.live="from" class="form-control">
                        </div>
                        <div class="col-6">
                            <input type="date" wire:model.live="to" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Purchase Orders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="4%"></th>
                            <th width="12%">PO Number</th>
                            <th width="18%">Supplier</th>
                            <th width="11%">Date</th>
                            <th width="11%">Received Qty</th>
                            <th width="12%">Purchase Cost</th>
                            <th width="12%">Net Total</th>
                            <th width="10%">Due</th>
                            <th width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-sm btn-label-secondary p-0 px-1"
                                        wire:click="toggleExpand(<?php echo e($po->id); ?>)">
                                        <i class="bx <?php echo e(in_array($po->id, $expanded) ? 'bx-chevron-up' : 'bx-chevron-down'); ?>"></i>
                                    </button>
                                </td>
                                <td><strong><?php echo e($po->invoice_no); ?></strong></td>
                                <td><?php echo e($po->supplier?->name ?? '-'); ?></td>
                                <td><?php echo e(optional($po->purchase_date)->format('M d, Y')); ?></td>
                                <td><?php echo e(number_format($po->total_quantity)); ?> pcs</td>
                                <td>৳<?php echo e(number_format($po->purchase_cost, 2)); ?></td>
                                <td>৳<?php echo e(number_format($po->net_total, 2)); ?></td>
                                <td>
                                    <span class="badge <?php echo e($po->due_amount > 0 ? 'bg-danger' : 'bg-success'); ?>">
                                        ৳<?php echo e(number_format($po->due_amount, 2)); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php $statusClass = match ($po->status) { 'received' => 'bg-success', 'ordered' => 'bg-warning', default => 'bg-secondary' }; ?>
                                    <span class="badge <?php echo e($statusClass); ?>"><?php echo e(ucfirst($po->status ?? '-')); ?></span>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($po->id, $expanded)): ?>
                                <tr>
                                    <td colspan="9" class="bg-light">
                                        <h6 class="mb-2">PO Items — <?php echo e($po->invoice_no); ?></h6>
                                        <table class="table table-sm table-bordered mb-0 bg-white">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Variant / Specs</th>
                                                    <th width="12%">Quantity</th>
                                                    <th width="15%">Unit Cost</th>
                                                    <th width="15%">Total Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $po->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                    <tr>
                                                        <td><?php echo e($item->product?->name ?? $item->product_name); ?></td>
                                                        <td><?php echo e($item->variant?->sku ?? ($item->specs ?? '-')); ?></td>
                                                        <td><?php echo e(number_format($item->quantity)); ?></td>
                                                        <td>৳<?php echo e(number_format($item->unit_cost, 2)); ?></td>
                                                        <td>৳<?php echo e(number_format($item->total_cost, 2)); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">No items</td>
                                                    </tr>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No purchase orders found</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <?php echo e($purchases->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/inventory/supplier-stock.blade.php ENDPATH**/ ?>