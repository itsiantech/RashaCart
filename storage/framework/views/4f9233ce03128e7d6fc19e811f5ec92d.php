<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.addons.index')); ?>" wire:navigate>Add Ons</a></li>
            <li class="breadcrumb-item active">Hello World</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="d-inline-flex align-items-center justify-content-center"
                    style="width:46px;height:46px;border-radius:.55rem;background:rgba(105,108,255,.12);color:#696cff;font-size:1.35rem;">
                    <i class="bx bx-smile"></i>
                </span>
                <div>
                    <h5 class="mb-0">Hello World</h5>
                    <small class="text-muted">Reference add-on for the Add-on system</small>
                </div>
            </div>

            <p class="mb-3">
                This page only exists while the <strong>HelloWorld</strong> add-on is active.
                Deactivate it from <a href="<?php echo e(route('admin.addons.index')); ?>" wire:navigate>Manage Add Ons</a>
                and this page and its sidebar item disappear immediately.
            </p>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small mb-1">Filter demo</div>
                        <code>order.initial_status</code> currently returns
                        <span class="badge bg-label-primary"><?php echo e($initialStatus); ?></span>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small mb-1">Slot demo</div>
                        <code>admin.menu</code> is
                        <span class="badge <?php echo e($hasMenuSlot ? 'bg-label-success' : 'bg-label-secondary'); ?>">
                            <?php echo e($hasMenuSlot ? 'registered' : 'not registered'); ?>

                        </span>
                        — the sidebar item you clicked lives in
                        <code>addons/HelloWorld/resources/views/admin/menu.blade.php</code>.
                    </div>
                </div>
            </div>

            <p class="text-muted small mt-3 mb-0">
                Copy this folder as a starting point, or run
                <code>php artisan addon:make MyAddon</code> to scaffold a new one.
            </p>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\addons\HelloWorld\resources\views\admin\page.blade.php ENDPATH**/ ?>