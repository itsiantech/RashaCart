<nav wire:ignore
  class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar">

  <div wire:ignore class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="bx bx-search fs-4 lh-0"></i>
        <input
          type="text"
          class="form-control border-0 shadow-none"
          placeholder="Search..."
          aria-label="Search..." />
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- Place this tag where you want the button to render. -->
      

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <?php
            $profileRoute = auth()->user()->hasRole('admin')
            ? route('admin.dashboard') 
            : route('admin.vendor.profile', auth()->id()); 
            ?>

            <a href="<?php echo e($profileRoute); ?>">
              <img src="<?php echo e(auth()->user()->vendorDetails?->profile_image 
                    ? Storage::url(auth()->user()->vendorDetails->profile_image) 
                    : asset('images/default-avatar.png')); ?>"
                class="rounded-circle mb-3"
                width="150"
                height="150"
                style="object-fit: cover; border:4px solid #f8f9fa;">
            </a>
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="<?php echo e(Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('assets/admin/img/avatars/user-avater.png')); ?>" alt class="w-px-80 rounded-circle">
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block"><?php echo e(auth()->user()->name); ?></span>
                  <small class="text-muted"><?php echo e(auth()->user()->role); ?></small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="<?php echo e(route('profile.index')); ?>">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle">My Profile</span>
            </a>
          </li>
          
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
              <?php echo csrf_field(); ?>
              <button class="dropdown-item" type="submit"><i class="bx bx-power-off me-2"></i>Log Out</button>
            </form>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
  <script>
    function initHamburger() {
        const toggleBtns = document.querySelectorAll('.layout-menu-toggle');
        
        toggleBtns.forEach(btn => {
            btn.replaceWith(btn.cloneNode(true)); // remove old listeners
        });

        document.querySelectorAll('.layout-menu-toggle').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const html = document.documentElement;
                const menu = document.getElementById('layout-menu');
                const overlay = document.querySelector('.layout-overlay');

                html.classList.toggle('layout-menu-expanded');

                if (overlay) {
                    overlay.classList.toggle('layout-overlay-show');
                    overlay.addEventListener('click', function() {
                        html.classList.remove('layout-menu-expanded');
                        if (overlay) overlay.classList.remove('layout-overlay-show');
                    }, { once: true });
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initHamburger);
    document.addEventListener('livewire:navigated', initHamburger);
</script>
</nav><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/layouts/partial/header.blade.php ENDPATH**/ ?>