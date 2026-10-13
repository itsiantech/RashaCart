 <!-- Register -->
          <div class="card col-xl-4  col-sm-12 offset-md-4">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a wire:navigate href="<?php echo e(route('home')); ?>" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <img src="<?php echo e(asset('storage/logo/' . setting('app_logo'))); ?>" class="img-fluid" style="max-width: 150px; max-height: 50px;">
                  </span> 
                </a>
              </div>
              <!-- /Logo -->
             
              <h4 class="mb-4 mt-4 text-center fs-5">Login</h4>                  
                                
                  <form wire:submit="login">                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" 
                              class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                              type="email" 
                              wire:model="email" 
                              value="<?php echo e(old('email')); ?>" 
                              required 
                              autofocus 
                              autocomplete="email">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                
                    <div class="mb-3 form-password-toggle">
                      <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Password</label>
                        <a href="<?php echo e(route('password.request')); ?>">
                          <small>Forgot Password?</small>
                        </a>
                      </div>
                        <div class="input-group input-group-merge">
                            <input class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  id="password" 
                                  type="password" 
                                  wire:model="password" 
                                  required>
                            <span class="input-group-text cursor-pointer" id="togglePassword">
                                <i class="bx bx-hide"></i>
                            </span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('throttle')): ?>
                        <div class="alert alert-danger">
                            <?php echo e(session('throttle')); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                    <div class="mb-3">
                        <button class="btn btn-primary d-grid w-100" type="submit">Log in</button>
                    </div>
                  </form>
               
                <p class="text-center">
                  
                  <a wire:navigate href="<?php echo e(route('register')); ?>">
                    <span>Create an account</span>
                  </a>
                </p>

                <script>
                  document.getElementById('showUserLoginBtn').addEventListener('click', function () {
                      document.getElementById('userLoginForm').classList.remove('d-none');
                      this.style.display = 'none';
                  });
                </script>
              
              
            </div>
          </div>
          <!-- /Register -->
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/auth/login.blade.php ENDPATH**/ ?>