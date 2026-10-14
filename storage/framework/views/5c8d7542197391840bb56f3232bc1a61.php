<div class="container-fluid py-4">

    
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-semibold mb-1">Manual Payment Settings</h4>
            <p class="text-muted mb-0 small">Configure mobile banking &amp; bank transfer details for manual payments.</p>
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

    <div class="row g-4">

        
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2">
                    <div class="nav flex-column nav-pills gap-1" role="tablist">

                        <?php
                        $tabs = [
                        'bkash' => ['label' => 'bKash', 'icon' => 'bi-phone', 'color' => '#e2136e'],
                        'nagad' => ['label' => 'Nagad', 'icon' => 'bi-phone', 'color' => '#f05829'],
                        'rocket' => ['label' => 'Rocket', 'icon' => 'bi-phone', 'color' => '#8b1fa9'],
                        'bank' => ['label' => 'Bank Account', 'icon' => 'bi-bank', 'color' => '#0d6efd'],
                        ];
                        $enabledMap = [
                        'bkash' => $bkash_enabled,
                        'nagad' => $nagad_enabled,
                        'rocket' => $rocket_enabled,
                        'bank' => $bank_enabled,
                        ];
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button
                            wire:click="setTab('<?php echo e($key); ?>')"
                            class="nav-link text-start d-flex align-items-center gap-2 px-3 py-2 rounded <?php echo e($activeTab === $key ? 'active' : ''); ?>"
                            style="<?php echo e($activeTab === $key ? 'background-color: ' . $tab['color'] . '1a; color: ' . $tab['color'] . '; font-weight:600;' : ''); ?>"
                            role="tab">
                            <i class="bi <?php echo e($tab['icon']); ?> fs-5"></i>
                            <span><?php echo e($tab['label']); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($enabledMap[$key]): ?>
                            <span class="badge ms-auto" style="background:<?php echo e($tab['color']); ?>; font-size:0.65rem;">ON</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-12 col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'bkash'): ?>
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#e2136e1a;">
                            <i class="bi bi-phone fs-4" style="color:#e2136e;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">bKash Configuration</h5>
                            <small class="text-muted">Set your bKash number for manual payments</small>
                        </div>
                        <div class="ms-auto form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="bkash_enabled" wire:model.live="bkash_enabled"
                                style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-medium" for="bkash_enabled">
                                <?php echo e($bkash_enabled ? 'Enabled' : 'Disabled'); ?>

                            </label>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bkash_enabled): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">bKash Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" wire:model.blur="bkash_number"
                                    class="form-control <?php $__errorArgs = ['bkash_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="01XXXXXXXXX">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bkash_number'];
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
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Account Type <span class="text-danger">*</span></label>
                            <select wire:model="bkash_account_type"
                                class="form-select <?php $__errorArgs = ['bkash_account_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="personal">Personal</option>
                                <option value="merchant">Merchant</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex gap-2 mt-4 mb-0" role="alert">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>How it works:</strong> Customers will be shown this bKash number and asked to
                            send payment manually. They must submit their <em>Transaction ID</em> for verification.
                        </div>
                    </div>
                    <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginal729efde75a55ce4a832fd1b07202e68d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal729efde75a55ce4a832fd1b07202e68d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.payment-disabled-notice','data' => ['method' => 'bKash','color' => '#e2136e']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('payment-disabled-notice'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['method' => 'bKash','color' => '#e2136e']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal729efde75a55ce4a832fd1b07202e68d)): ?>
<?php $attributes = $__attributesOriginal729efde75a55ce4a832fd1b07202e68d; ?>
<?php unset($__attributesOriginal729efde75a55ce4a832fd1b07202e68d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal729efde75a55ce4a832fd1b07202e68d)): ?>
<?php $component = $__componentOriginal729efde75a55ce4a832fd1b07202e68d; ?>
<?php unset($__componentOriginal729efde75a55ce4a832fd1b07202e68d); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'nagad'): ?>
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#f058291a;">
                            <i class="bi bi-phone fs-4" style="color:#f05829;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Nagad Configuration</h5>
                            <small class="text-muted">Set your Nagad number for manual payments</small>
                        </div>
                        <div class="ms-auto form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="nagad_enabled" wire:model.live="nagad_enabled"
                                style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-medium" for="nagad_enabled">
                                <?php echo e($nagad_enabled ? 'Enabled' : 'Disabled'); ?>

                            </label>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nagad_enabled): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nagad Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" wire:model.blur="nagad_number"
                                    class="form-control <?php $__errorArgs = ['nagad_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="01XXXXXXXXX">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nagad_number'];
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
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Account Type <span class="text-danger">*</span></label>
                            <select wire:model="nagad_account_type"
                                class="form-select <?php $__errorArgs = ['nagad_account_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="personal">Personal</option>
                                <option value="merchant">Merchant</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex gap-2 mt-4 mb-0" role="alert">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>How it works:</strong> Customers will be shown this Nagad number and asked to
                            send payment manually. They must submit their <em>Transaction ID</em> for verification.
                        </div>
                    </div>
                    <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginal729efde75a55ce4a832fd1b07202e68d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal729efde75a55ce4a832fd1b07202e68d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.payment-disabled-notice','data' => ['method' => 'Nagad','color' => '#f05829']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('payment-disabled-notice'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['method' => 'Nagad','color' => '#f05829']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal729efde75a55ce4a832fd1b07202e68d)): ?>
<?php $attributes = $__attributesOriginal729efde75a55ce4a832fd1b07202e68d; ?>
<?php unset($__attributesOriginal729efde75a55ce4a832fd1b07202e68d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal729efde75a55ce4a832fd1b07202e68d)): ?>
<?php $component = $__componentOriginal729efde75a55ce4a832fd1b07202e68d; ?>
<?php unset($__componentOriginal729efde75a55ce4a832fd1b07202e68d); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'rocket'): ?>
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#8b1fa91a;">
                            <i class="bi bi-phone fs-4" style="color:#8b1fa9;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Rocket Configuration</h5>
                            <small class="text-muted">Set your Rocket number for manual payments</small>
                        </div>
                        <div class="ms-auto form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="rocket_enabled" wire:model.live="rocket_enabled"
                                style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-medium" for="rocket_enabled">
                                <?php echo e($rocket_enabled ? 'Enabled' : 'Disabled'); ?>

                            </label>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rocket_enabled): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Rocket Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" wire:model.blur="rocket_number"
                                    class="form-control <?php $__errorArgs = ['rocket_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="01XXXXXXXXX">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rocket_number'];
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
                    </div>

                    <div class="alert alert-info d-flex gap-2 mt-4 mb-0" role="alert">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>How it works:</strong> Customers will be shown this Rocket number and asked to
                            send payment manually. They must submit their <em>Transaction ID</em> for verification.
                        </div>
                    </div>
                    <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginal729efde75a55ce4a832fd1b07202e68d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal729efde75a55ce4a832fd1b07202e68d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.payment-disabled-notice','data' => ['method' => 'Rocket','color' => '#8b1fa9']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('payment-disabled-notice'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['method' => 'Rocket','color' => '#8b1fa9']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal729efde75a55ce4a832fd1b07202e68d)): ?>
<?php $attributes = $__attributesOriginal729efde75a55ce4a832fd1b07202e68d; ?>
<?php unset($__attributesOriginal729efde75a55ce4a832fd1b07202e68d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal729efde75a55ce4a832fd1b07202e68d)): ?>
<?php $component = $__componentOriginal729efde75a55ce4a832fd1b07202e68d; ?>
<?php unset($__componentOriginal729efde75a55ce4a832fd1b07202e68d); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'bank'): ?>
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#0d6efd1a;">
                            <i class="bi bi-bank fs-4" style="color:#0d6efd;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Bank Account Configuration</h5>
                            <small class="text-muted">Set your bank details for manual transfers</small>
                        </div>
                        <div class="ms-auto form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="bank_enabled" wire:model.live="bank_enabled"
                                style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-medium" for="bank_enabled">
                                <?php echo e($bank_enabled ? 'Enabled' : 'Disabled'); ?>

                            </label>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bank_enabled): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Bank Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-bank"></i></span>
                                <input type="text" wire:model.blur="bank_name"
                                    class="form-control <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="e.g. Dutch-Bangla Bank Ltd.">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bank_name'];
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

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Account Holder Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" wire:model.blur="bank_account_name"
                                    class="form-control <?php $__errorArgs = ['bank_account_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Full name on account">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bank_account_name'];
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

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Account Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                <input type="text" wire:model.blur="bank_account_number"
                                    class="form-control <?php $__errorArgs = ['bank_account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Bank account number">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bank_account_number'];
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

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Routing Number <span class="text-muted small">(optional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                <input type="text" wire:model.blur="bank_routing_number"
                                    class="form-control <?php $__errorArgs = ['bank_routing_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="9-digit routing number">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bank_routing_number'];
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

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Branch Name <span class="text-muted small">(optional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" wire:model.blur="bank_branch"
                                    class="form-control"
                                    placeholder="e.g. Gulshan Branch, Dhaka">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning d-flex gap-2 mt-4 mb-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            Customers will be asked to transfer funds to this account and submit a
                            <em>payment slip / screenshot</em> for manual verification by the admin.
                        </div>
                    </div>
                    <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginal729efde75a55ce4a832fd1b07202e68d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal729efde75a55ce4a832fd1b07202e68d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.payment-disabled-notice','data' => ['method' => 'Bank Transfer','color' => '#0d6efd']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('payment-disabled-notice'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['method' => 'Bank Transfer','color' => '#0d6efd']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal729efde75a55ce4a832fd1b07202e68d)): ?>
<?php $attributes = $__attributesOriginal729efde75a55ce4a832fd1b07202e68d; ?>
<?php unset($__attributesOriginal729efde75a55ce4a832fd1b07202e68d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal729efde75a55ce4a832fd1b07202e68d)): ?>
<?php $component = $__componentOriginal729efde75a55ce4a832fd1b07202e68d; ?>
<?php unset($__componentOriginal729efde75a55ce4a832fd1b07202e68d); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    
    <div class="d-flex justify-content-end mt-4">
        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary px-5">
            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2" role="status"></span>
            <i wire:loading.remove wire:target="save" class="bi bi-floppy me-1"></i>
            Save All Settings
        </button>
    </div>

</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/payment/manual-payment-index.blade.php ENDPATH**/ ?>