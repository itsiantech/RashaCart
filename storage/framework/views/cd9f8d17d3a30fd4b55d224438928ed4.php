<div class="container my-4">
  <!-- Header Section -->
  <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 px-3 px-sm-0 gap-2">
    <div>
      <h3 class="h5 text-dark dark:text-white">
        Add New Admin
      </h3>
      <p class="text-muted mb-0">
        Create a new admin user with a specific role
      </p>
    </div>

    <!-- Back Button -->
    <div class="mt-3 mt-sm-0">
      <a wire:navigate href="<?php echo e(route('admin.user.index')); ?>" class="text-secondary text-decoration-none d-flex align-items-center gap-1 hover-text-primary">
        <i class="bx bx-arrow-back fs-5"></i>
        <span>Back</span>
      </a>
    </div>
  </div>

  <!-- Form Section -->
  <div class="px-3 px-sm-0">
    <form wire:submit.prevent="save">
      <!-- Name & Email Fields -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6">
          <label for="name" class="form-label">Full Name</label>
          <input 
            type="text" 
            id="name" 
            wire:model="name" 
            class="form-control" 
            placeholder="Enter full name" 
            required
          />
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="col-12 col-sm-6">
          <label for="phone" class="form-label">Mobile Number</label>
          <input 
            type="text" 
            id="phone" 
            wire:model="phone" 
            class="form-control" 
            placeholder="017XXXXXXXX" 
            required
          />
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="col-12 col-sm-6">
          <label for="email" class="form-label">Email Address</label>
          <input 
            type="email" 
            id="email" 
            wire:model="email" 
            class="form-control" 
            placeholder="Enter email address" 
            required
          />
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
      </div>

      <!-- Password Fields -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6">
          <label for="password" class="form-label">Password</label>
          <input 
            type="password" 
            id="password" 
            wire:model="password" 
            class="form-control" 
            placeholder="Enter password" 
            required
          />
          
        </div>

        <div class="col-12 col-sm-6">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input 
            type="password" 
            id="password_confirmation" 
            wire:model="password_confirmation" 
            class="form-control" 
            placeholder="Confirm password" 
            required
          />
        </div>
      </div>

      <!-- Roles Selection -->
      <div class="mb-3">
        <label class="form-label">Role *</label>
        <div>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="radio" 
                name="selectedRole" 
                id="role_<?php echo e($role->name); ?>" 
                wire:model="selectedRole" 
                value="<?php echo e($role->name); ?>"
              >
              <label class="form-check-label" for="role_<?php echo e($role->name); ?>">
                <?php echo e($role->name); ?>

              </label>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['selectedRole'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </div>

      <!-- Submit Button -->
      <div>
        <button 
          type="submit" 
          class="btn btn-primary w-100 w-sm-auto d-flex justify-content-center align-items-center"
          wire:loading.attr="disabled"
          wire:target="save"
        >
          Create Admin
        </button>
      </div>
    </form>
  </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\settings\user\user-create.blade.php ENDPATH**/ ?>