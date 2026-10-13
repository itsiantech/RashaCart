<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="<?php echo e(asset('assets/admin')); ?>/"
    data-template="vertical-menu-template-free">

<head>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <style>
        @import url('https://fonts.maateen.me/solaiman-lipi/font.css');

        [x-cloak] {
            display: none !important;
        }
    </style>

    <title><?php echo $__env->yieldContent('page-title'); ?></title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/admin')); ?>/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin')); ?>/vendor/fonts/boxicons.css" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin')); ?>/vendor/css/core.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin')); ?>/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin')); ?>/css/demo.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin')); ?>/css/myCss.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin')); ?>/css/sidebar-theme.css" />

    <?php
        $sbVars = \App\Services\SidebarTheme::cssVarsForUser(Auth::user());
    ?>
    <style id="sb-theme-vars">
        :root {
            <?php $__currentLoopData = $sbVars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($k); ?>: <?php echo e($v); ?>;
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        }
    </style>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin')); ?>/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin')); ?>/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>

<body class="layout-navbar-fixed layout-menu-fixed layout-content-navbar">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <?php echo $__env->make('livewire.admin.layouts.partial.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="layout-page">
                <?php echo $__env->make('livewire.admin.layouts.partial.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-lg-12 mb-4 order-0">
                                <?php echo e($slot ?? ''); ?><?php echo $__env->yieldContent('content'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.settings.sidebar-theme-picker');

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-4173177605-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="layout-overlay"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo e(asset('assets/admin/vendor/libs/jquery/jquery.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/vendor/libs/popper/popper.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/vendor/js/bootstrap.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>

    <script type="module" src="<?php echo e(asset('assets/admin/js/helpers.js')); ?>"></script>
    <script type="module" src="<?php echo e(asset('assets/admin/js/config.js')); ?>"></script>
    <script type="module" src="<?php echo e(asset('assets/admin/vendor/js/menu.js')); ?>"></script>
    <script type="module" src="<?php echo e(asset('assets/admin/js/main.js')); ?>"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>



    <script>
        // ── Single delegated click handler for hamburger toggle ──
        document.addEventListener('click', function(e) {
            const toggleBtn = e.target.closest('.layout-menu-toggle');
            const overlay = e.target.closest('.layout-overlay');

            // Clicked the overlay → close sidebar
            if (overlay && !toggleBtn) {
                document.documentElement.classList.remove('layout-menu-expanded');
                return;
            }

            // Clicked the hamburger → toggle sidebar
            if (toggleBtn && !overlay) {
                e.preventDefault();
                document.documentElement.classList.toggle('layout-menu-expanded');
                return;
            }
        });

        document.addEventListener('livewire:navigated', () => {
            if (window.Helpers && typeof window.Helpers.initMenu === 'function') {
                window.Helpers.initMenu();
            }
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('success', (event) => {
                const message = event.message || event[0]?.message || 'Success!';
                toastr.success(message);
            });

            Livewire.on('error', (event) => {
                const message = event.message || event[0]?.message || 'Error!';
                toastr.error(message);
            });

            Livewire.on('warning', (event) => {
                const message = event.message || event[0]?.message || 'Warning!';
                toastr.warning(message);
            });

            // Sidebar theme: instant apply without reload
            Livewire.on('sidebar-theme-applied', (event) => {
                const vars = event.vars || (event[0] && event[0].vars) || {};
                const root = document.documentElement;
                Object.entries(vars).forEach(([k, v]) => root.style.setProperty(k, v));
            });
        });

        toastr.options = {
            "progressBar": true,
            "closeButton": true,
            "positionClass": "toast-top-right",
            "timeOut": 2500,
            "preventDuplicates": true,
            "preventOpenDuplicates": true
        };

        window.addEventListener('showDeleteConfirmation', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteConfirmed');
                }
            });
        });
    </script>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/layouts/app.blade.php ENDPATH**/ ?>