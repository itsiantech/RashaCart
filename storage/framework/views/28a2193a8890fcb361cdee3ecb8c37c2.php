<div class="container-fluid py-4">

    
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-semibold mb-1">API Payment Gateway</h4>
            <p class="text-muted mb-0 small">Configure automated payment gateway integrations.</p>
        </div>
        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary px-4">
            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2" role="status"></span>
            <i wire:loading.remove wire:target="save" class="bi bi-floppy me-1"></i>
            Save Settings
        </button>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($successMessage): ?>
    <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4" role="alert"
        x-data x-init="setTimeout(() => $el.remove(), 4000)">
        <i class="bi bi-check-circle-fill"></i>
        <span><?php echo e($successMessage); ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded d-flex align-items-center justify-content-center"
                    style="width:44px;height:44px;background:#0a5c1a1a;">
                    <i class="bi bi-shield-lock fs-5" style="color:#0a5c1a;"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-semibold">SSL Commerz</h6>
                    <small class="text-muted">Bangladesh's leading payment gateway</small>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sslcommerz_enabled): ?>
                    <span class="badge bg-success-subtle text-success px-3 py-2">
                        <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;vertical-align:middle;"></i>
                        Active
                    </span>
                    <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                        <i class="bi bi-circle me-1" style="font-size:0.5rem;vertical-align:middle;"></i>
                        Inactive
                    </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch"
                            id="sslcommerz_enabled" wire:model.live="sslcommerz_enabled"
                            style="width:2.5em;height:1.3em;">
                        <label class="form-check-label" for="sslcommerz_enabled">Enable</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sslcommerz_enabled): ?>

            
            <div class="mb-4">
                <label class="form-label fw-medium">Environment Mode</label>
                <div class="d-flex gap-2">
                    <div>
                        <input type="radio" class="btn-check" id="mode_sandbox"
                            wire:model.live="sslcommerz_mode" value="sandbox">
                        <label class="btn btn-outline-warning" for="mode_sandbox">
                            <i class="bi bi-bug me-1"></i> Sandbox
                        </label>
                    </div>
                    <div>
                        <input type="radio" class="btn-check" id="mode_live"
                            wire:model.live="sslcommerz_mode" value="live">
                        <label class="btn btn-outline-success" for="mode_live">
                            <i class="bi bi-lightning-charge me-1"></i> Live
                        </label>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sslcommerz_mode === 'sandbox'): ?>
                <div class="mt-2 text-warning small d-flex align-items-center gap-1">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Sandbox mode — transactions will not be real. Use for testing only.
                </div>
                <?php else: ?>
                <div class="mt-2 text-success small d-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i>
                    Live mode — real transactions will be processed.
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="row g-3">

                
                <div class="col-md-6">
                    <label class="form-label fw-medium">
                        Store ID <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shop"></i></span>
                        <input type="text" wire:model.blur="sslcommerz_store_id"
                            class="form-control font-monospace <?php $__errorArgs = ['sslcommerz_store_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="<?php echo e($sslcommerz_mode === 'sandbox' ? 'testbox...' : 'your_store_id'); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sslcommerz_store_id'];
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
                    <small class="text-muted">Provided by SSL Commerz in your merchant dashboard.</small>
                </div>

                
                <div class="col-md-6">
                    <label class="form-label fw-medium">
                        Store Password <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="<?php echo e($showPassword ? 'text' : 'password'); ?>"
                            wire:model.blur="sslcommerz_store_password"
                            class="form-control font-monospace <?php $__errorArgs = ['sslcommerz_store_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Store password / API secret">
                        <button class="btn btn-outline-secondary" type="button"
                            wire:click="togglePasswordVisibility">
                            <i class="bi <?php echo e($showPassword ? 'bi-eye-slash' : 'bi-eye'); ?>"></i>
                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sslcommerz_store_password'];
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
                    <small class="text-muted">Stored encrypted in the database.</small>
                </div>

                
                <div class="col-md-4">
                    <label class="form-label fw-medium">Currency</label>
                    <select wire:model="sslcommerz_currency" class="form-select">
                        <option value="BDT">BDT — Bangladeshi Taka</option>
                        <option value="USD">USD — US Dollar</option>
                    </select>
                </div>

            </div>

            
            <div class="mt-4 p-3 rounded bg-light border">
                <p class="fw-medium mb-2 small text-muted text-uppercase" style="letter-spacing:.05em;">
                    <i class="bi bi-link-45deg me-1"></i> Callback URLs (add these in your SSL Commerz dashboard)
                </p>
                <div class="row g-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['success' => 'Success URL', 'fail' => 'Fail URL', 'cancel' => 'Cancel / IPN URL']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12">
                        <label class="form-label small mb-1 text-muted"><?php echo e($label); ?></label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control font-monospace bg-white"
                                value="<?php echo e(url('/payment/' . $route)); ?>" readonly>
                            <button class="btn btn-outline-secondary btn-sm"
                                onclick="navigator.clipboard.writeText('<?php echo e(url('/payment/' . $route)); ?>'); this.innerHTML='<i class=\'bi bi-check\'></i>'; setTimeout(()=>this.innerHTML='<i class=\'bi bi-copy\'></i>',1500)"
                                type="button">
                                <i class="bi bi-copy"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="mt-4 d-flex align-items-center gap-3">
                <button wire:click="testConnection"
                    wire:loading.attr="disabled"
                    wire:target="testConnection"
                    class="btn btn-outline-primary">
                    <span wire:loading wire:target="testConnection"
                        class="spinner-border spinner-border-sm me-2"></span>
                    <i wire:loading.remove wire:target="testConnection"
                        class="bi bi-plug me-1"></i>
                    Test Connection
                </button>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($testResult === 'success'): ?>
                <span class="text-success d-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i> Connection successful!
                </span>
                <?php elseif($testResult === 'failed'): ?>
                <span class="text-danger d-flex align-items-center gap-1">
                    <i class="bi bi-x-circle-fill"></i> Connection failed. Check your credentials.
                </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="alert alert-light border mt-4 mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-book text-primary fs-5"></i>
                <span>
                    New to SSL Commerz?
                    <a href="https://developer.sslcommerz.com/doc/v4/" target="_blank" class="link-primary">
                        Read the official documentation <i class="bi bi-box-arrow-up-right small"></i>
                    </a>
                </span>
            </div>

            <?php else: ?>
            
            <div class="text-center py-5 text-muted">
                <i class="bi bi-toggle-off display-4 d-block mb-3"></i>
                <p class="mb-0">SSL Commerz is currently <strong>disabled</strong>.</p>
                <p class="small">Toggle the switch above to configure your credentials.</p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm border-dashed opacity-50">
        <div class="card-body text-center py-4 text-muted">
            <i class="bi bi-plus-circle fs-3 d-block mb-2"></i>
            <small>More payment gateways coming soon (bKash API, Nagad API…)</small>
        </div>
    </div>

    
    <div class="d-flex justify-content-end mt-4">
        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary px-5">
            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2"></span>
            <i wire:loading.remove wire:target="save" class="bi bi-floppy me-1"></i>
            Save All Settings
        </button>
    </div>

</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/payment/api-payment-index.blade.php ENDPATH**/ ?>