
<div>
    <div class="card-header bg-primary text-white mb-3">Customer Login / Register</div>
    <p class="text-muted small">Control the storefront customer account system. Guest checkout always keeps working — disabling only hides the header account icon.</p>

    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="customer_login_enabled"
            wire:model.live="customer_login_enabled" role="switch">
        <label class="form-check-label fw-semibold" for="customer_login_enabled">Enable Customer Login/Register</label>
        <div class="form-text">ON: show the user icon in the frontend header. OFF: hide it completely; customers order as guests.</div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="customer_login_identifier" class="form-label fw-semibold">Login Identifier</label>
            <select class="form-select" id="customer_login_identifier" wire:model.live="customer_login_identifier">
                <option value="email">Email only</option>
                <option value="phone">Phone only</option>
                <option value="both">Both (Email + Phone)</option>
            </select>
            <div class="form-text">Frontend login &amp; registration forms show only the selected option(s).</div>
        </div>
    </div>

    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="customer_google_enabled"
            wire:model.live="customer_google_enabled" role="switch">
        <label class="form-check-label fw-semibold" for="customer_google_enabled">Enable Google Sign-In</label>
        <div class="form-text">Shows the "Continue with Google" button in the register tab. Requires GOOGLE_CLIENT_ID / SECRET in API Settings.</div>
    </div>

    <div class="alert alert-info small mb-0">
        Settings save instantly and clear the settings cache (<code>Settings::clearCache()</code>).
        Current: login <strong><?php echo e($customer_login_enabled ? 'ON' : 'OFF'); ?></strong>,
        identifier <strong><?php echo e($customer_login_identifier); ?></strong>,
        Google <strong><?php echo e($customer_google_enabled ? 'ON' : 'OFF'); ?></strong>.
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/settings/partial/customer-login-settings.blade.php ENDPATH**/ ?>