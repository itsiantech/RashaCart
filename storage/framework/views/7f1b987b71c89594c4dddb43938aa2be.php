<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($platform === 'meta'): ?>
                <i class="bx bxl-facebook-circle text-primary me-2"></i>Meta (Facebook) Pixel
            <?php elseif($platform === 'tiktok'): ?>
                <i class="bx bxl-tiktok me-2"></i>TikTok Pixel
            <?php elseif($platform === 'ga4'): ?>
                <i class="bx bxl-google me-2"></i>Google Analytics 4
            <?php elseif($platform === 'google_ads'): ?>
                <i class="bx bxs-megaphone me-2"></i>Google Ads (Enhanced Conversions)
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </h5>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch"
                   wire:model="platforms.<?php echo e($platform); ?>.is_active">
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label d-flex align-items-center">
                Name
                <span class="ms-2 text-muted cursor-pointer" data-bs-toggle="tooltip" title="প্ল্যাটফর্মের একটি নাম দিন">
                    <i class="bx bx-help-circle"></i>
                </span>
            </label>
            <input type="text" class="form-control" placeholder="e.g. Main Facebook Pixel"
                   wire:model="platforms.<?php echo e($platform); ?>.name">
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($platform === 'ga4'): ?>
        <div class="mb-3">
            <label class="form-label d-flex align-items-center">
                Measurement ID
                <span class="ms-2 text-muted cursor-pointer" data-bs-toggle="tooltip" title="GA4 Measurement ID (G-XXXXXXXXXX)">
                    <i class="bx bx-help-circle"></i>
                </span>
            </label>
            <input type="text" class="form-control" placeholder="G-XXXXXXXXXX"
                   wire:model="platforms.<?php echo e($platform); ?>.pixel_id">
        </div>
        <?php else: ?>
        <div class="mb-3">
            <label class="form-label d-flex align-items-center">
                Pixel ID
                <span class="ms-2 text-muted cursor-pointer" data-bs-toggle="tooltip" title="পিক্সেল আইডি">
                    <i class="bx bx-help-circle"></i>
                </span>
            </label>
            <input type="text" class="form-control" placeholder="e.g. 123456789012345"
                   wire:model="platforms.<?php echo e($platform); ?>.pixel_id">
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mb-3">
            <label class="form-label d-flex align-items-center">
                Access Token
                <span class="ms-2 text-muted cursor-pointer" data-bs-toggle="tooltip" title="সার্ভার-সাইড ট্র্যাকিং এর জন্য এক্সেস টোকেন">
                    <i class="bx bx-help-circle"></i>
                </span>
            </label>
            <div class="input-group">
                <input type="password" class="form-control"
                       placeholder="EAAG..."
                       wire:model="platforms.<?php echo e($platform); ?>.access_token">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['access_token_masked']): ?>
                <button type="button" class="btn btn-outline-secondary"
                        wire:click="$set('platforms.' . $platform . '.access_token', '')">
                    Clear
                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <small class="text-muted"><?php echo e($data['access_token_masked'] ? 'Current: ' . $data['access_token_masked'] : 'No token set'); ?></small>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($platform === 'meta'): ?>
        <div class="mb-3">
            <label class="form-label d-flex align-items-center">
                Test Event Code
                <span class="ms-2 text-muted cursor-pointer" data-bs-toggle="tooltip" title="Meta Test Events কোড">
                    <i class="bx bx-help-circle"></i>
                </span>
            </label>
            <input type="text" class="form-control" placeholder="TEST12345"
                   wire:model="platforms.<?php echo e($platform); ?>.test_event_code">
        </div>

        <div class="mb-3">
            <label class="form-label d-flex align-items-center">
                API Version
                <span class="ms-2 text-muted cursor-pointer" data-bs-toggle="tooltip" title="Meta Graph API ভার্সন">
                    <i class="bx bx-help-circle"></i>
                </span>
            </label>
            <input type="text" class="form-control" placeholder="v19.0"
                   wire:model="platforms.<?php echo e($platform); ?>.api_version">
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($platform === 'ga4'): ?>
        <div class="mb-3">
            <label class="form-label d-flex align-items-center">
                API Secret
                <span class="ms-2 text-muted cursor-pointer" data-bs-toggle="tooltip" title="GA4 Measurement Protocol API Secret">
                    <i class="bx bx-help-circle"></i>
                </span>
            </label>
            <input type="password" class="form-control" placeholder="Enter API Secret"
                   wire:model="platforms.<?php echo e($platform); ?>.extra.ga4_api_secret">
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mb-0">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch"
                       wire:model="platforms.<?php echo e($platform); ?>.is_test_mode">
                <label class="form-check-label">Test Mode</label>
            </div>
            <small class="text-muted">Enable to send test events only</small>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex gap-2">
            <button wire:click="testConnection('<?php echo e($platform); ?>')"
                    class="btn btn-outline-primary btn-sm">
                <i class="bx bx-link me-1"></i>Test Connection
            </button>
            <button wire:click="sendTestEvent('<?php echo e($platform); ?>')"
                    class="btn btn-outline-success btn-sm">
                <i class="bx bx-send me-1"></i>Send Test Event
            </button>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/partials/platform-settings.blade.php ENDPATH**/ ?>