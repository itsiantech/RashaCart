<div>
    
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                <li class="breadcrumb-item active">Best Offers</li>
            </ol>
        </nav>
        <button wire:click="$set('showForm', true)" class="btn btn-danger btn-sm">
            <i class="bx bx-plus"></i> Add Best Offer
        </button>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showForm): ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Add New Best Offer</h6>
            <button wire:click="$set('showForm', false)" class="btn-close"></button>
        </div>
        <div class="card-body">
            <div class="row g-3">
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                    <select wire:model.live="product_id" class="form-select <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">-- Select Product --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?> (<?php echo e(setting('currency')); ?><?php echo e($p->sale_price ?? $p->price); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Discount Type</label>
                    <select wire:model.live="discount_type" class="form-select">
                        <option value="percent">Percent (%)</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>

                
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Discount Value <span class="text-danger">*</span></label>
                    <input type="number" wire:model.live="discount_value" min="0"
                        class="form-control <?php $__errorArgs = ['discount_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="<?php echo e($discount_type === 'percent' ? 'e.g. 20' : 'e.g. 100'); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['discount_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Duration (Days) <span class="text-danger">*</span></label>
                    <input type="number" wire:model="days" min="1" max="365"
                        class="form-control <?php $__errorArgs = ['days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="7">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Offer Price Preview</label>
                    <div class="form-control bg-light fw-bold text-success">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->offerPricePreview !== null): ?>
                        <?php echo e(setting('currency')); ?><?php echo e(number_format($this->offerPricePreview, 2)); ?>

                        <?php else: ?>
                        --
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button wire:click="store" wire:loading.attr="disabled" class="btn btn-danger">
                        <span wire:loading.remove wire:target="store">Save Offer</span>
                        <span wire:loading wire:target="store"><i class="bx bx-loader bx-spin"></i> Saving...</span>
                    </button>
                    <button wire:click="$set('showForm', false)" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0">All Best Offers</h6>
            <input type="text" wire:model.live.debounce.300ms="search"
                class="form-control" style="max-width:250px" placeholder="Search by product name...">
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Original Price</th>
                        <th class="text-center">Discount</th>
                        <th class="text-center">Offer Price</th>
                        <th class="text-center">Expires At</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="<?php echo e($offer->isExpired() ? 'table-secondary text-muted' : ''); ?>">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($offer->product->thumbnail): ?>
                                <img src="<?php echo e(asset('storage/' . $offer->product->thumbnail)); ?>"
                                    style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="fw-semibold"><?php echo e($offer->product->name); ?></span>
                            </div>
                        </td>
                        <td class="text-center"><?php echo e(setting('currency')); ?><?php echo e(number_format($offer->product->sale_price ?? $offer->product->price, 2)); ?></td>
                        <td class="text-center">
                            <span class="badge bg-warning text-dark">
                                <?php echo e($offer->discount_value); ?><?php echo e($offer->discount_type === 'percent' ? '%' : ' '.setting('currency')); ?> OFF
                            </span>
                        </td>
                        <td class="text-center fw-bold text-danger"><?php echo e(setting('currency')); ?><?php echo e(number_format($offer->offer_price, 2)); ?></td>
                        <td class="text-center">
                            <span class="<?php echo e($offer->isExpired() ? 'text-danger' : 'text-success'); ?>">
                                <i class="bx bx-time-five me-1"></i>
                                <?php echo e($offer->expires_at->format('d M Y, h:i A')); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($offer->isExpired()): ?>
                                <span class="badge bg-danger ms-1">Expired</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox"
                                    wire:click="toggleActive(<?php echo e($offer->id); ?>)"
                                    <?php if($offer->is_active): echo 'checked'; endif; ?>>
                            </div>
                        </td>
                        <td class="text-center">
                            <button wire:click="confirmDelete(<?php echo e($offer->id); ?>)" class="btn btn-sm btn-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No best offers found.</td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
            <div class="p-3"><?php echo e($offers->links()); ?></div>
        </div>
    </div>
</div><?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\best-offer\best-offer-index.blade.php ENDPATH**/ ?>