
<div class="border rounded p-3 mb-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h6 class="mb-0">Attributes (Variants) <small class="text-muted">(Generate combinations)</small></h6>
        <label class="form-check-label small">
            <input type="checkbox" class="form-check-input me-1" wire:model.live="has_attributes">
            এই product-এ attributes ব্যবহার হবে
            <small class="text-muted d-block">OFF থাকলে নিচের variants save হবে না (simple product)।</small>
        </label>
    </div>
    <small class="text-muted d-block mb-3">Attribute টিক দিন → value সিলেক্ট করুন → "Generate" চাপুন। আগের variant ডুপ্লিকেট হবে না। <b>*</b> = customer-কে বাছতেই হবে (required)।</small>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($has_attributes): ?>

    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $productAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attribute->is_active ?? true): ?>
        <div class="col-md-4">
            <div class="border rounded p-2 h-100">
                <label class="form-check-label fw-bold d-block mb-2">
                    <input type="checkbox" class="form-check-input me-1"
                        wire:click="toggleGenAttr(<?php echo e($attribute->id); ?>)"
                        <?php echo e(isset($genAttrs[$attribute->id]) ? 'checked' : ''); ?>>
                    <?php echo e($attribute->name); ?>

                    <small class="text-muted">(<?php echo e($attribute->type); ?>)</small>
                </label>
                <label class="small d-block mb-2">
                    <input type="checkbox" class="form-check-input me-1"
                        wire:model="attrRequired.<?php echo e($attribute->id); ?>">
                    Required <b class="text-danger">*</b>
                </label>
                <div class="d-flex flex-wrap gap-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attribute->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $av): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button"
                        wire:click="toggleGenValue(<?php echo e($attribute->id); ?>, <?php echo e($av->id); ?>)"
                        class="btn btn-sm <?php echo e(in_array($av->id, $genValues[$attribute->id] ?? []) ? 'btn-primary' : 'btn-outline-secondary'); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($av->color_hex): ?>
                        <span class="d-inline-block rounded-circle border me-1" style="width:12px;height:12px;background:<?php echo e($av->color_hex); ?>;vertical-align:-1px;"></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php echo e($av->value); ?>

                    </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button type="button" wire:click="openQuickAdd(<?php echo e($attribute->id); ?>)"
                        class="btn btn-sm btn-outline-success" title="নতুন value যোগ করুন">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
        <button type="button" wire:click="generateVariants" class="btn btn-success btn-sm">
            <i class="fas fa-magic me-1"></i> Generate Variants
        </button>
        <a href="<?php echo e(route('admin.attribute.index')); ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-cog me-1"></i> Attributes ম্যানেজ করুন
        </a>
        <span class="text-muted small">|</span>
        <div class="input-group input-group-sm" style="max-width:220px;">
            <span class="input-group-text">৳</span>
            <input type="number" step="0.01" min="0" class="form-control" placeholder="Bulk price" id="bulkPriceInput">
            <button type="button" class="btn btn-outline-secondary"
                onclick="Livewire.dispatch('bulk-price')">Apply</button>
        </div>
        <div class="input-group input-group-sm" style="max-width:220px;">
            <input type="number" step="1" min="0" class="form-control" placeholder="Bulk stock" id="bulkStockInput">
            <button type="button" class="btn btn-outline-secondary"
                onclick="Livewire.dispatch('bulk-stock')">Apply</button>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm"
            wire:click="autoVariantPrices" title="সব variant-এর দাম খালি করে product দাম follow করাও">
            Auto price
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm"
            wire:click="copyProductStockToVariants" title="Product-এর stock সংখ্যাটা সব variant-এ বসাও">
            Product stock →
        </button>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showQuickAdd): ?>
    <div class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,.4);">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title mb-0">নতুন value যোগ করুন</h6>
                    <button type="button" class="btn-close" wire:click="$set('showQuickAdd', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small">Value নাম <span class="text-danger">*</span></label>
                        <input type="text" wire:model="quickValue" class="form-control form-control-sm" placeholder="e.g. Yellow">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['quickValue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Color (optional)</label>
                        <input type="color" wire:model="quickColor" class="form-control form-control-color form-control-sm" value="#000000">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="$set('showQuickAdd', false)">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" wire:click="quickAddValue">Add</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 
</div>

    <?php
        $__scriptKey = '385845758-0';
        ob_start();
    ?>
<script>
    $wire.on('bulk-price', () => {
        const v = document.getElementById('bulkPriceInput').value;
        if (v !== '') $wire.bulkSetVariantPrice(parseFloat(v));
    });
    $wire.on('bulk-stock', () => {
        const v = document.getElementById('bulkStockInput').value;
        if (v !== '') $wire.bulkSetVariantStock(parseInt(v));
    });
</script>
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/products/partials/variant-generator.blade.php ENDPATH**/ ?>