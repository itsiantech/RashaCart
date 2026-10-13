<div class="mb-3 col-12 mb-0">
    <div class="alert alert-warning">
      <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
      <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
    </div>
  </div>
    <form method="POST" action="<?php echo e(route('profile.destroy')); ?>" id="formAccountDeactivation">
      <?php echo csrf_field(); ?>
      <?php echo method_field('DELETE'); ?>
  
      <div class="form-check mb-3">
          <input
              class="form-check-input"
              type="checkbox"
              name="accountActivation"
              id="accountActivation"
              required
          />
          <label class="form-check-label" for="accountActivation">
              I confirm my account deactivation
          </label>
      </div>
  
      <div class="mb-3">
          <label for="password" class="form-label">Confirm Password</label>
          <input
              type="password"
              class="form-control <?php $__errorArgs = ['userDeletion.password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
              id="password"
              name="password"
              placeholder="Enter your password"
              required
          >
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['userDeletion.password'];
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
  
      <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
    </form><?php /**PATH C:\Users\user\bandrose\resources\views/profile/partials/delete-user-form.blade.php ENDPATH**/ ?>