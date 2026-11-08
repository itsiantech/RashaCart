<!DOCTYPE html>
<html lang="en" data-critters-container=""
    style="--shop-color-primary: #EB0000; --shop-color-secondary: #7A0000; --shop-color-tertiary: #292929;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Standard Meta Tags -->
    <title><?php echo e($meta['title'] ?? $meta_title ?? setting('app_name') ?? config('app.name')); ?></title>

    <!-- Description -->
    <meta name="description" content="<?php echo e($meta['description'] ?? $meta_description ?? 'Default e-commerce description'); ?>">

    <!-- Keywords -->
    <meta name="keywords" content="<?php echo e($meta['keywords'] ?? $meta_keywords ?? 'shopping, bangladesh, ecommerce'); ?>">

    <meta name="tags" content="<?php echo e($meta['tags'] ?? $meta_tags ?? ''); ?>">

    <!-- Open Graph for Social Media -->
    <meta property="og:title" content="<?php echo e($meta['title'] ?? setting('app_name') ?? config('app.name')); ?>">
    <meta property="og:description" content="<?php echo e($meta['meta_description'] ?? ''); ?>">
    <!-- Open Graph (Facebook, WhatsApp, LinkedIn) -->
    <meta property="og:title" content="<?php echo e($meta_title ?? setting('app_name') ?? config('app.name')); ?>">
    <meta property="og:description" content="<?php echo e($meta_description ?? 'Shop at Ordinary Bangladesh.'); ?>">
    <meta property="og:tags" content="<?php echo e($meta_tags ?? 'ecommerce, shopping, Bangladesh'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <!-- Ensure you add an image for social previews -->
    <meta property="og:image" content="<?php echo e(asset('path-to-your-logo.png')); ?>">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($meta_title ?? config('app.name')); ?>">
    <meta name="twitter:description" content="<?php echo e($meta_description ?? ''); ?>">



    <meta name="theme-color" content="#EB0000">
    <meta name="robots" content="index, follow">
    <meta name="author" content="<?php echo e(setting('app_name') ?? config('app.name')); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:image" content="<?php echo e($og_image ?? 'https://www.medleybiz.com.bd/og-image.jpg'); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo e(url()->current()); ?>">
    <meta property="twitter:title" content="<?php echo e($meta_title ?? setting('app_name') ?? config('app.name')); ?>">
    <meta property="twitter:description" content="<?php echo e($meta_description ?? 'Shop the latest products at Ordinary Bangladesh. Fast delivery, secure payments, and excellent customer service.'); ?>">
    <meta property="twitter:image" content="<?php echo e($og_image ?? 'https://www.medleybiz.com.bd/og-image.jpg'); ?>">

    <!-- Favicon (Dynamic from Admin Settings) -->
    <?php $favicon = setting('fev_icon') ?: setting('app_logo'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($favicon): ?>
    <link rel="icon" href="<?php echo e(asset('storage/logo/' . rawurlencode($favicon))); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Custom Meta Tags (Raw HTML) -->
    <?php echo $meta_tags ?? ''; ?>


    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="preconnect" href="https://cdn.saleecom.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.saleecom.com">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Theme CSS -->
    <?php
    $theme = setting('theme') ?? 'default';
    ?>
    <link rel="stylesheet" href="<?php echo e(asset("assets/frontend/themes/$theme/assets/style.css?v=2.0.0.0")); ?>">

    
    <?php echo $__env->make('partials.style-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Material Components -->
    <link rel="stylesheet" href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <?php echo \App\Services\PixelManager::head(); ?>


    
    <?php echo $__env->make('partials.tracking-pixel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('partials.seo-tags', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('track-event', ({
                eventName,
                data
            }) => {
                if (window.fbq) fbq('track', eventName, data);
                if (window.ttq) ttq.track(mapTiktokEvent(eventName), data);
                if (window.gtag) gtag('event', mapGaEvent(eventName), data);
            });
        });

        function mapTiktokEvent(e) {
            return {
                ViewContent: 'ViewContent',
                AddToCart: 'AddToCart',
                InitiateCheckout: 'InitiateCheckout',
                Purchase: 'CompletePayment'
            } [e] || e;
        }

        function mapGaEvent(e) {
            return {
                ViewContent: 'view_item',
                AddToCart: 'add_to_cart',
                InitiateCheckout: 'begin_checkout',
                Purchase: 'purchase'
            } [e] || e;
        }
    </script>

    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>

<body>
    
    <?php echo $__env->make('partials.seo-body', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- Top Bar -->
    

    <!-- Header -->
    <?php echo $__env->make('livewire.frontend.layouts.partial.header.header-default', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <style>
        body { font-family: var(--body-font, 'Segoe UI', sans-serif); }
        .main-header { background-color: var(--hd-bg, #12225B); }
        .main-header .action-btn,
        .main-header .menu-toggle,
        .main-header .search-toggle-btn { color: var(--hd-icon, #fff); }
        .main-header .badge { background-color: var(--hd-badge, #ffc107); color: #111; }
        .search-container input { background-color: var(--hd-search-bg, #fff); }
        .search-container button { background-color: var(--hd-search-btn, #f5a623); color: var(--hd-search-icon, #fff); }
        .search-container button:hover { background-color: var(--hd-search-btn, #f5a623); }
        .main-header .s input[type="text"],
        .main-header .s input[type="search"] { background: var(--hd-search-bg, #fff); }
        .main-header .s button[type="submit"],
        .main-header .s .search-btn { background: var(--hd-search-btn, #f5a623) !important; color: var(--hd-search-icon, #fff) !important; }
        .footer-top, .footer-bottom { background-color: var(--ft-bg, #12225B); color: var(--ft-text, #fff); }
        .footer-title { color: var(--ft-text, #fff); }
        .footer-title::after { background-color: var(--ft-text, #fff); }
        .footer-links a { color: var(--ft-nav, #f0f0f0); }
        .footer-links a:hover { color: var(--ft-nav, #fff); }
        .footer-bottom-links a { color: var(--ft-nav, #fff); }
        .footer-bottom-links a:hover { color: var(--ft-nav, #fff); }
        @media (max-width: 767.98px) {
            .footer-bottom { padding-bottom: 96px; }
        }
    </style>



    <!-- Main Content -->
    <main class="main-content">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($slot)): ?>
        <?php echo e($slot); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>

    </main>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! (request()->routeIs('frontend.product.single.view'))): ?>
    <?php echo $__env->make('livewire.frontend.layouts.partial.footer.footer-default', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php echo $__env->make('partials.cart-drawer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.quick-add-popup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


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
                text: "You won’t be able to revert this!",
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
    <?php echo $__env->make('partials.customer-auth-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>

</html><?php /**PATH C:\Users\user\bandrose\resources\views\livewire\frontend\layouts\app.blade.php ENDPATH**/ ?>