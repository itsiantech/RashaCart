<div class="card mb-4">
    <h5 class="card-header">Notification Settings</h5>
    <div class="card-body">
        <form wire:submit.prevent="update">

            <!-- Email Notification Toggle -->
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" wire:model="email_notification" id="emailNotification"
                    value="1">
                <label class="form-check-label" for="emailNotification">Email Notifications</label>
            </div>


            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-white">
                   BULK SMS BD API Settings
                </div>
                <div class="card-body">
                    <!-- Note -->
                    <!-- SMS Notification Toggle -->
                    <div class="form-check form-switch mb-3 mt-3">
                        <input class="form-check-input" type="checkbox" wire:model="sms_notification"
                            id="smsNotification" value="1">
                        <label class="form-check-label" for="smsNotification">SMS Notifications</label>
                    </div>

                    <!-- API Base URL -->
                    <div class="mb-3">
                        <label for="sms_api_url" class="form-label">BulkSmsBD Base URL</label>
                        <input type="text" class="form-control" id="sms_api_url" wire:model="sms_api_url"
                            placeholder="https://api.example.com">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sms_api_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- API Key -->
                    <div class="mb-3">
                        <label for="sms_api_key" class="form-label">API KEY</label>
                        <input type="text" class="form-control" id="sms_api_key" wire:model="sms_api_key"
                            placeholder="Enter your API key">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sms_api_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- API Secret -->
                    <div class="mb-3">
                        <label for="sms_sender_id" class="form-label">Sender ID</label>
                        <input type="text" class="form-control" id="sms_sender_id" wire:model="sms_sender_id"
                            placeholder="Enter your API secret">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sms_sender_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                     <p class="text-muted small mb-3 mt-3">
                        Note: Use your Bulksms BD SMS API credentials if active sms features.  if properly configured, you will see current SMS balance information below.
                    </p>


                   <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('sms_notification', true)): ?>
                        <div class="alert alert-info">
                            <p class="mb-1"> <i class="bi bi-info-circle-fill"></i>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('sms_api_key') && setting('sms_api_url') && setting('sms_sender_id')): ?>
                                    SMS notification service is properly configured.
                                    <br> 
                                    Current SMS Balance: <strong><?php echo e($balance); ?> tk</strong>
                                    <br>
                                    Per SMS Rate: 0.35 tk
                                <?php else: ?>
                                    SMS notification service is not configured yet. Please provide valid API credentials.
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                           
                            
                            
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                </div>
            </div>


            

            <div class="mt-3">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    Save Changes
                </button>
            </div>
        </form>




    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/settings/partial/notification.blade.php ENDPATH**/ ?>