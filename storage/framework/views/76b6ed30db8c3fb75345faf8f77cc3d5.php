<div>
    <h4 class="fw-bold mb-4">Server Side Tracking</h4>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link <?php echo e($activeTab === 'meta' ? 'active' : ''); ?>"
                    wire:click="$set('activeTab', 'meta')">
                <i class="bx bxl-facebook-circle me-2"></i>Meta (Facebook)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?php echo e($activeTab === 'tiktok' ? 'active' : ''); ?>"
                    wire:click="$set('activeTab', 'tiktok')">
                <i class="bx bxl-tiktok me-2"></i>TikTok
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?php echo e($activeTab === 'ga4' ? 'active' : ''); ?>"
                    wire:click="$set('activeTab', 'ga4')">
                <i class="bx bxl-google me-2"></i>GA4
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?php echo e($activeTab === 'google_ads' ? 'active' : ''); ?>"
                    wire:click="$set('activeTab', 'google_ads')">
                <i class="bx bxs-megaphone me-2"></i>Google Ads
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?php echo e($activeTab === 'guide' ? 'active' : ''); ?>"
                    wire:click="$set('activeTab', 'guide')">
                <i class="bx bx-book me-2"></i>Setup Guide (বাংলা)
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        
        <div class="tab-pane <?php echo e($activeTab === 'meta' ? 'active show' : ''); ?>">
            <?php echo $__env->make('livewire.admin.partials.platform-settings', ['platform' => 'meta', 'data' => $platforms['meta']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        
        <div class="tab-pane <?php echo e($activeTab === 'tiktok' ? 'active show' : ''); ?>">
            <?php echo $__env->make('livewire.admin.partials.platform-settings', ['platform' => 'tiktok', 'data' => $platforms['tiktok']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        
        <div class="tab-pane <?php echo e($activeTab === 'ga4' ? 'active show' : ''); ?>">
            <?php echo $__env->make('livewire.admin.partials.platform-settings', ['platform' => 'ga4', 'data' => $platforms['ga4']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        
        <div class="tab-pane <?php echo e($activeTab === 'google_ads' ? 'active show' : ''); ?>">
            <?php echo $__env->make('livewire.admin.partials.platform-settings', ['platform' => 'google_ads', 'data' => $platforms['google_ads']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        
        <div class="tab-pane <?php echo e($activeTab === 'guide' ? 'active show' : ''); ?>">
            <?php echo $__env->make('livewire.admin.partials.tracking-setup-guide', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab !== 'guide'): ?>
    <div class="mt-4">
        <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">Save Settings</span>
            <span wire:loading wire:target="save">Saving...</span>
        </button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/server-side-tracking.blade.php ENDPATH**/ ?>