<!DOCTYPE html>
<html lang="en" dir="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-critters-container="" style="--shop-color-primary: #EB0000; --shop-color-secondary: #7A0000; --shop-color-tertiary: #292929;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Meta Tags ডাইনামিকভাবে লোড হবে -->
    <?php echo SEOMeta::generate(); ?>

    <?php echo OpenGraph::generate(); ?>


    <!-- Fallback যদি SEO টুলস না থাকে -->
    <?php if (! empty(trim($__env->yieldContent('title')))): ?>
    <title><?php echo $__env->yieldContent('title'); ?> - <?php echo e(config('app.name')); ?></title>
    <?php else: ?>
    <title><?php echo e(config('app.name')); ?></title>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (! empty(trim($__env->yieldContent('meta_description')))): ?>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description'); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- CSRF Token & Scripts -->
    <?php echo csrf_field(); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>



    <meta name="description"
        content="<?php echo $__env->yieldContent('description', 'Shop the latest products at Ordinary Bangladesh. Fast delivery, secure payments, and excellent customer service.'); ?>">
    <meta name="theme-color" content="#EB0000">
    <meta name="robots" content="index, follow">
    <link rel="icon" href="https://zetech.top/demo/img/tara-mart-logo-icon.png">


    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="preconnect" href="https://cdn.saleecom.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.saleecom.com">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <?php
    $theme = setting('theme') ?? 'default';
    ?>

    <link rel="stylesheet" href="<?php echo e(asset("assets/frontend/themes/$theme/assets/style.css")); ?>">

    <link rel="stylesheet" href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Social Media Meta Tags -->
    <meta property="og:title" content="Tara Mart - Your Trusted Online Shopping Destination">
    <meta property="og:description"
        content="Shop the latest products at Tara Mart. Fast delivery, secure payments, and excellent customer service.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://zetech.top/demo/img/tara-mart-logo-icon.png">
    <meta name="twitter:card" content="summary_large_image">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>

<body>
    <!-- Top Bar -->
    

    <!-- Header -->
    <?php echo $__env->make('livewire.frontend.layouts.partial.header.header-default', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Vendor Alert -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
    <div class="alert alert-warning alert-dismissible fade show text-center mb-0" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($slot)): ?>
        <?php echo e($slot); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>

        <!-- Right Side Shopping Cart -->
        <?php
        $cartItems = session()->get('cart', []);
        $cartCount = array_sum(array_column($cartItems, 'qty'));
        ?>
        <div class="cart-sidebar" x-data="{ cartCount: <?php echo e($cartCount); ?> }" x-on:cart-updated.window="cartCount = $event.detail.count">

            <div class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" x-text="cartCount"><?php echo e($cartCount); ?></span>
            </div>
            <div class="cart-dropdown">
                <div class="cart-header">
                    <h4>Your Cart</h4>
                    <span class="close-cart">&times;</span>
                </div>

                <div class="cart-items">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('frontend.components.cart-items', ['cartItems' => $cartItems]);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1367568757-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
                <div class="cart-footer">
                    <div class="cart-actions">
                        <a wire:navigate href="<?php echo e(route('frontend.cart.view')); ?>"
                            class="btn btn-secondary btn-view-cart">Go to Cart</a>
                        <a wire:navigate href="<?php echo e(route('frontend.checkout')); ?>"
                            class="btn btn-primary btn-checkout">Checkout</a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <?php echo $__env->make('livewire.frontend.layouts.partial.footer.footer-default', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="<?php echo e(asset('assets/frontend/themes/' . setting('theme') . '/js/tabs.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/frontend/themes/' . setting('theme') . '/js/main.js')); ?>"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {

            // Livewire success/error events
            Livewire.on('success', (event) => {
                const message = event.message || event[0]?.message || 'Success!';
                toastr.success(message);
            });

            Livewire.on('error', (event) => {
                const message = event.message || event[0]?.message || 'Error!';
                toastr.error(message);
            });

            // Check for Laravel session flash messages (middleware or controller)
            <?php if(session() -> has('success')): ?>
            toastr.success("<?php echo e(session('success')); ?>");
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(session() -> has('error')): ?>
            toastr.error("<?php echo e(session('error')); ?>");
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        });

        // Toastr options
        toastr.options = {
            "progressBar": true,
            "closeButton": true,
            "positionClass": "toast-top-right",
            "timeOut": 2500,
            "preventDuplicates": true,
            "preventOpenDuplicates": true
        };

        // SweetAlert Delete Confirmation
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

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>

</html><?php /**PATH C:\Users\user\bandrose\resources\views\components\layouts\app.blade.php ENDPATH**/ ?>