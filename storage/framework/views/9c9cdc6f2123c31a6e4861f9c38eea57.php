
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h5 class="mb-0">Image Options <small class="text-muted">(ছবির option — attributes থেকে আলাদা layer)</small></h5>
            <label class="form-check-label small">
                <input type="checkbox" class="form-check-input me-1" wire:model.live="has_image_options">
                এই product-এ image options ব্যবহার হবে
            </label>
        </div>
        <small class="text-muted d-block mb-3">প্রতিটি option = ১টা ছবি + label + নিজস্ব দাম/স্টক (খালি রাখলে variant-এর দাম/স্টক)। দাম precedence: option → variant → product। স্টক: min(variant, option)।</small>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($has_image_options): ?>
        <label class="small d-block mb-3">
            <input type="checkbox" class="form-check-input me-1" wire:model="require_image_option">
            Customer-কে image option বাছতেই হবে <b class="text-danger">*</b>
            <small class="text-muted">(OFF করলে শুধু variant দিয়েও order হবে — যেমন size-only product)</small>
        </label>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="12%">ছবি</th>
                        <th width="18%">Label *</th>
                        <th width="12%">নিজস্ব দাম</th>
                        <th width="12%">Sale দাম</th>
                        <th width="10%">স্টক</th>
                        <th width="8%">Active</th>
                        <th width="10%"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $imageOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($opt['preview'])): ?>
                            <img src="<?php echo e($opt['preview']); ?>" class="img-thumbnail" width="60" height="60">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <input type="file" wire:model="imageOptions.<?php echo e($i); ?>.new_upload"
                                accept="image/*" class="form-control form-control-sm mt-1">
                        </td>
                        <td>
                            <input type="text" wire:model="imageOptions.<?php echo e($i); ?>.label"
                                class="form-control form-control-sm" placeholder="e.g. Red — সামনে">
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" wire:model="imageOptions.<?php echo e($i); ?>.price"
                                class="form-control form-control-sm" placeholder="auto">
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" wire:model="imageOptions.<?php echo e($i); ?>.sale_price"
                                class="form-control form-control-sm" placeholder="auto">
                        </td>
                        <td>
                            <input type="number" step="1" min="0" wire:model="imageOptions.<?php echo e($i); ?>.stock"
                                class="form-control form-control-sm" style="max-width:80px;" placeholder="auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($opt['stock'] ?? null) === null || ($opt['stock'] ?? null) === ''): ?>
                            <small class="text-muted">variant stock</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input" wire:model="imageOptions.<?php echo e($i); ?>.is_active">
                        </td>
                        <td>
                            <button type="button" wire:click="removeImageOption(<?php echo e($i); ?>)"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Optionটি মুছে ফেলবেন?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <button type="button" wire:click="addImageOption" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add Image Option
        </button>
        <span class="text-muted small">|</span>
        <div class="input-group input-group-sm" style="max-width:220px;">
            <span class="input-group-text">৳</span>
            <input type="number" step="0.01" min="0" class="form-control" placeholder="Bulk দাম" id="bulkOptPriceInput">
            <button type="button" class="btn btn-outline-secondary"
                onclick="Livewire.dispatch('bulk-opt-price')">Apply</button>
        </div>
        <div class="input-group input-group-sm" style="max-width:220px;">
            <input type="number" step="1" min="0" class="form-control" placeholder="Bulk স্টক" id="bulkOptStockInput">
            <button type="button" class="btn btn-outline-secondary"
                onclick="Livewire.dispatch('bulk-opt-stock')">Apply</button>
        </div>
        <div class="mt-2">
                <?php
        $__scriptKey = '546097912-0';
        ob_start();
    ?>
            <script>
                $wire.on('bulk-opt-price', () => {
                    const v = document.getElementById('bulkOptPriceInput').value;
                    if (v !== '') $wire.bulkSetOptionPrice(parseFloat(v));
                });
                $wire.on('bulk-opt-stock', () => {
                    const v = document.getElementById('bulkOptStockInput').value;
                    if (v !== '') $wire.bulkSetOptionStock(parseInt(v));
                });
            </script>
                <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>
        </div>

        
        <?php $previewRows = $this->previewCombinations(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($previewRows)): ?>
        <h6 class="mt-4 mb-1">⚡ Effective price & stock <small class="text-muted">(customer exactly এটাই পাবে)</small></h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Combination</th><th>Variant</th><th>Image option</th><th>Effective দাম</th><th>Available</th></tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $previewRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><small><?php echo e($pr['sku']); ?></small></td>
                        <td><small><?php echo e($pr['variant']); ?></small></td>
                        <td><small><?php echo e($pr['option']); ?></small></td>
                        <td><strong>৳<?php echo e(number_format($pr['price'], 2)); ?></strong></td>
                        <td><?php echo e($pr['stock'] > 0 ? $pr['stock'].' ✅' : '0 ⛔'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/products/partials/image-options-manager.blade.php ENDPATH**/ ?>