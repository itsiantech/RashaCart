<?php $__env->startSection('page-title'); ?>
    User | Edit
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <div class="row">
    <div class="col-md-12">      
      <div class="card mb-4">
        <h5 class="card-header">Profile Details</h5>
        <!-- Account -->
        <div class="card-body">
          <div class="d-flex align-items-start align-items-sm-center gap-4">
            <img
              src="<?php echo e($user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/admin/img/avatars/user-avater.png')); ?>"
              alt="user-avatar"
              class="d-block rounded"
              height="100"
              width="100"
              id="uploadedAvatar"
            />
            <div class="button-wrapper">
              <h2><?php echo e(Auth::user()->name); ?></h2>
              <p class="text-muted mb-0">
                Role: <?php echo e(Auth::user()->getRoleNames()->join(', ')); ?>

              </p>
            
              <p class="text-muted mb-0"><?php echo e(Auth::user()->address); ?></p>
            </div>
          </div>
        </div>

        <hr class="my-0" />
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-6">            
              <?php echo $__env->make('profile.partials.update-profile-information-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div class="col-12 col-md-6">   
              <?php echo $__env->make('profile.partials.update-password-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
          </div>
        </div>
        <!-- /Account -->
      </div>

      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!Auth::user()->hasRole('admin')): ?>  
        <div class="card">
          <h5 class="card-header">Delete Account</h5>
          <div class="card-body">
          
              <?php echo $__env->make('profile.partials.delete-user-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
          
          </div>
        </div>
      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('livewire.admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\bandrose\resources\views/profile/index.blade.php ENDPATH**/ ?>