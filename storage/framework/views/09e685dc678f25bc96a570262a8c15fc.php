<div>
  <div class="bg-white dark:bg-gray-900 shadow rounded p-4 mt-2 mb-5">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('message')): ?>
      <div class="alert alert-success mb-4">
        <?php echo e(session('message')); ?>

      </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="h5 text-dark dark:text-light mb-0">
        Edit Role's Permission
      </h3>
      <a href="<?php echo e(route('admin.rolepermission.index')); ?>" class="btn btn-danger btn-sm">
        <i class="bx bx-arrow-back"></i> Back
      </a>
    </div>

    <form wire:submit.prevent="updatePermission">
      <div class="mb-4">
        <div class="fw-semibold text-dark dark:text-light">
          Role Name: 
          <span class="text-success dark:text-success fw-semibold">
            <?php echo e($currentRole->name); ?>

          </span>
        </div>
      </div>

      <hr class="mb-4">

      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $permission_groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $permissions = \App\Models\User::getpermissionByGroupName($group->group_name);
        ?>

        <div class="row mb-4">
          <div class="col-md-3 d-flex align-items-center">
            <div class="form-check">
              <input 
                type="checkbox"
                wire:click="toggleGroupPermissions('<?php echo e($group->group_name); ?>')"
                <?php if($this->groupChecked($group->group_name)): echo 'checked'; endif; ?>
                class="form-check-input"
                id="group_<?php echo e(Str::slug($group->group_name)); ?>"
              >
              <label class="form-check-label fw-semibold text-dark dark:text-light" for="group_<?php echo e(Str::slug($group->group_name)); ?>">
                <?php echo e($group->group_name); ?>

              </label>
            </div>
          </div>

          <div class="col-md-9 d-flex flex-wrap gap-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="form-check me-3 mb-2">
                <input 
                  type="checkbox"
                  wire:model="selectedPermissions"
                  value="<?php echo e($permission->name); ?>"
                  id="perm_<?php echo e($permission->id); ?>"
                  class="form-check-input"
                >
                <label class="form-check-label text-dark dark:text-light" for="perm_<?php echo e($permission->id); ?>">
                  <?php echo e($permission->name); ?>

                </label>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary btn-sm mt-2">
          Assign permission
        </button>
      </div>
    </form>
  </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\settings\role-permission\role-permission-assign.blade.php ENDPATH**/ ?>