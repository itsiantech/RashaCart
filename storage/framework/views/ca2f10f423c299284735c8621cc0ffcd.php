<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Damage &amp; Returned Stock</h4>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-error-circle"></i></span>
                        </div>
                        <h5 class="mb-0"><?php echo e(number_format($metrics['total_qty'])); ?> pcs</h5>
                    </div>
                    <small class="text-muted">Total Damaged (৳<?php echo e(number_format($metrics['total_value'], 2)); ?>)</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time"></i></span>
                        </div>
                        <h5 class="mb-0"><?php echo e(number_format($metrics['pending'])); ?></h5>
                    </div>
                    <small class="text-muted">Pending Action</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-dark"><i class="bx bx-trash"></i></span>
                        </div>
                        <h5 class="mb-0"><?php echo e(number_format($metrics['scrapped'])); ?></h5>
                    </div>
                    <small class="text-muted">Scrapped</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-truck"></i></span>
                        </div>
                        <h5 class="mb-0"><?php echo e(number_format($metrics['returned'])); ?></h5>
                    </div>
                    <small class="text-muted">Returned to Supplier</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-wrench"></i></span>
                        </div>
                        <h5 class="mb-0"><?php echo e(number_format($metrics['refurbished'])); ?></h5>
                    </div>
                    <small class="text-muted">Refurbished</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Report Damage Form -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Report Damaged Stock</h6>
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
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" wire:model="formQuantity" min="1" class="form-control <?php $__errorArgs = ['formQuantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['formQuantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea wire:model="formReason" rows="3" class="form-control <?php $__errorArgs = ['formReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="e.g. expired, damaged in transit, water damage..."></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['formReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <button type="button" wire:click="saveDamage" class="btn btn-danger w-100">
                        <i class="bx bx-error-circle me-1"></i> Log Damage
                    </button>
                    <small class="text-muted d-block mt-2">
                        Damaged units are deducted from sellable stock immediately.
                    </small>
                </div>
            </div>
        </div>

        <!-- Damage List -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Damage Log</h6>
                    <select wire:model.live="statusFilter" class="form-select form-select-sm w-auto">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="scrapped">Scrapped</option>
                        <option value="returned_to_supplier">Returned to Supplier</option>
                        <option value="refurbished">Refurbished</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="12%">Date</th>
                                    <th width="22%">Product</th>
                                    <th width="9%">Variant</th>
                                    <th width="8%">Qty</th>
                                    <th width="18%">Reason</th>
                                    <th width="12%">Status</th>
                                    <th width="19%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($item->created_at->format('M d, Y')); ?></td>
                                        <td><?php echo e(Str::limit($item->product?->name ?? '-', 30)); ?></td>
                                        <td><?php echo e($item->variant?->sku ?? '-'); ?></td>
                                        <td><strong><?php echo e($item->quantity); ?></strong></td>
                                        <td>
                                            <?php echo e(Str::limit($item->reason, 45)); ?>

                                            <br><small class="text-muted">by <?php echo e($item->reportedBy?->name ?? 'System'); ?></small>
                                        </td>
                                        <td>
                                            <?php
                                                $badge = match ($item->action_status) {
                                                    'pending' => 'bg-warning',
                                                    'scrapped' => 'bg-dark',
                                                    'returned_to_supplier' => 'bg-info',
                                                    'refurbished' => 'bg-success',
                                                    default => 'bg-secondary',
                                                };
                                            ?>
                                            <span class="badge <?php echo e($badge); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $item->action_status))); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->action_status === 'refurbished' && $item->restocked): ?>
                                                <br><small class="text-success">Restocked</small>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->action_status === 'pending'): ?>
                                                <select wire:model="actionStatus.<?php echo e($item->id); ?>" class="form-select form-select-sm mb-1">
                                                    <option value="">-- Action --</option>
                                                    <option value="scrapped">Scrap</option>
                                                    <option value="returned_to_supplier">Return to Supplier</option>
                                                    <option value="refurbished">Refurbish (+restock)</option>
                                                </select>
                                                <div class="d-flex gap-1">
                                                    <input type="text" wire:model="actionNote.<?php echo e($item->id); ?>" class="form-control form-control-sm" placeholder="Note (optional)">
                                                    <button type="button" wire:click="applyAction(<?php echo e($item->id); ?>)" class="btn btn-sm btn-primary px-2">
                                                        <i class="bx bx-check"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <small class="text-muted"><?php echo e($item->action_note ?? '-'); ?></small>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->action_at): ?>
                                                    <br><small class="text-muted"><?php echo e($item->action_at->format('M d, Y')); ?></small>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No damage records found</td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <?php echo e($items->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/inventory/damage-stock.blade.php ENDPATH**/ ?>