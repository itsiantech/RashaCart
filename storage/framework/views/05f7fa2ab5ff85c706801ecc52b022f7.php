<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Stock Adjustment (Manual)</h4>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-edit-alt"></i></span>
                        </div>
                        <h5 class="mb-0"><?php echo e(number_format($metrics['total'])); ?></h5>
                    </div>
                    <small class="text-muted">Total Adjustments</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-plus-circle"></i></span>
                        </div>
                        <h5 class="mb-0">+<?php echo e(number_format($metrics['increase_qty'])); ?></h5>
                    </div>
                    <small class="text-muted">Increased Units</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-minus-circle"></i></span>
                        </div>
                        <h5 class="mb-0">-<?php echo e(number_format($metrics['decrease_qty'])); ?></h5>
                    </div>
                    <small class="text-muted">Decreased Units</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-transfer"></i></span>
                        </div>
                        <h5 class="mb-0"><?php echo e(number_format($metrics['net'])); ?></h5>
                    </div>
                    <small class="text-muted">Net Change</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Adjustment Form -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Manual Stock Adjustment</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select wire:model="formProductId" class="form-select <?php $__errorArgs = ['formProductId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Select Product --</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?> (<?php echo e($p->sku); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['formProductId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formVariants->count()): ?>
                        <div class="mb-3">
                            <label class="form-label">Variant</label>
                            <select wire:model="formVariantId" class="form-select">
                                <option value="">-- Main Product --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $formVariants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($v->id); ?>"><?php echo e($v->sku); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Direction <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="direction" value="increase" id="dir-in">
                                <label class="form-check-label text-success" for="dir-in">Increase (+)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="direction" value="decrease" id="dir-out">
                                <label class="form-check-label text-danger" for="dir-out">Decrease (−)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" wire:model="adjustQuantity" min="1" class="form-control <?php $__errorArgs = ['adjustQuantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['adjustQuantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason (Mandatory) <span class="text-danger">*</span></label>
                        <textarea wire:model="reason" rows="3" class="form-control <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="e.g. physical count correction, found in warehouse..."></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <button type="button" wire:click="saveAdjustment" class="btn btn-primary w-100">
                        <i class="bx bx-save me-1"></i> Apply Adjustment
                    </button>
                    <small class="text-muted d-block mt-2">
                        Every adjustment is recorded with the reason and your user ID in the audit trail.
                    </small>
                </div>
            </div>
        </div>

        <!-- Audit Trail -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Adjustment History (Audit Trail)</h6>
                    <select wire:model.live="typeFilter" class="form-select form-select-sm w-auto">
                        <option value="">All</option>
                        <option value="increase">Increase only</option>
                        <option value="decrease">Decrease only</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="14%">Date</th>
                                    <th width="28%">Product</th>
                                    <th width="10%">Variant</th>
                                    <th width="10%">Change</th>
                                    <th width="24%">Reason</th>
                                    <th width="14%">Adjusted By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($h->created_at->format('M d, Y H:i')); ?></td>
                                        <td><?php echo e(Str::limit($h->product?->name ?? '-', 35)); ?></td>
                                        <td><?php echo e($h->variant?->sku ?? '-'); ?></td>
                                        <td>
                                            <span class="badge <?php echo e($h->quantity_change > 0 ? 'bg-success' : 'bg-danger'); ?>">
                                                <?php echo e($h->quantity_change > 0 ? '+' : ''); ?><?php echo e($h->quantity_change); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e(Str::limit($h->reason, 50)); ?></td>
                                        <td><?php echo e($h->adjustedBy?->name ?? 'System'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No adjustments found</td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <?php echo e($history->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/inventory/stock-adjustment.blade.php ENDPATH**/ ?>