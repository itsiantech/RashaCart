<div>

    <div class="card-header bg-primary text-white mb-3">Business Settings</div>
    <form wire:submit.prevent="update" enctype="multipart/form-data">

        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="currency" class="form-label">currency Icon</label>
                <select class="form-control <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="currency"
                    wire:model.defer="currency">
                    <option value="">Select Currency</option>
                    <option value="৳">BDT - Bangladeshi Taka (৳)</option>
                    <option value="$">USD - US Dollar ($)</option>
                    <option value="€">EUR - Euro (€)</option>
                    <option value="£">GBP - British Pound (£)</option>
                    <option value="¥">JPY - Japanese Yen (¥)</option>
                    <option value="₹">INR - Indian Rupee (₹)</option>
                    <option value="₩">KRW - South Korean Won (₩)</option>
                    <option value="₽">RUB - Russian Ruble (₽)</option>
                    <option value="₺">TRY - Turkish Lira (₺)</option>
                    <option value="₫">VND - Vietnamese Dong (₫)</option>
                    <option value="₱">PHP - Philippine Peso (₱)</option>
                    <option value="₪">ILS - Israeli Shekel (₪)</option>
                    <option value="฿">THB - Thai Baht (฿)</option>
                    <option value="₦">NGN - Nigerian Naira (₦)</option>
                </select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="dc_inside" class="form-label">Inside City Delivery Charge</label>
                <input type="number" class="form-control <?php $__errorArgs = ['dc_inside'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="dc_inside"
                    wire:model.defer="dc_inside" min="1" max="500" placeholder="0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['dc_inside'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="dc_outside" class="form-label">Outside City Delivery Charge</label>
                <input type="number" class="form-control <?php $__errorArgs = ['dc_outside'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="dc_outside"
                    wire:model.defer="dc_outside" min="1" max="500" placeholder="0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['dc_outside'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" wire:model="attribute_system" id="attribute_system"
                value="1">
            <label class="form-check-label" for="attribute_system">Attribute System</label>
        </div>

       


        <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2" wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">
                    Save Changes
                </span>
                <span wire:loading wire:target="update">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Saving...
                </span>
            </button>
        </div>
    </form>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/settings/partial/business-settings.blade.php ENDPATH**/ ?>