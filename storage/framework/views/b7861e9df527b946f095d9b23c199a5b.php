<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($meta['title'] ?? (setting('app_name') ?: config('app.name'))); ?></title>
    <meta name="description" content="<?php echo e($meta['description'] ?? ''); ?>">
    <meta name="keywords" content="<?php echo e($meta['keywords'] ?? ''); ?>">
    <meta name="tags" content="<?php echo e($meta['tags'] ?? ''); ?>">

    <?php $favicon = setting('fev_icon') ?: setting('app_logo'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($favicon): ?>
    <link rel="icon" href="<?php echo e(asset('storage/logo/' . rawurlencode($favicon))); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    
    <?php echo $__env->make('partials.style-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo \App\Services\PixelManager::head(); ?>

    <?php echo $__env->make('partials.tracking-pixel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('partials.seo-tags', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <style>
        :root {
            --primary: #7A0000;
            --primary-dark: #5a0000;
            --navy: #12225B;
            --accent: #f5a623;
            --red: #dc2626;
            --orange: #f97316;
            --line: #e5e7eb;
        }

        body { font-family: 'Segoe UI', sans-serif; background: #F3F4F6; }

        /* ── Container (max-w-7xl mx-auto px-4) ── */
        .hp-container { max-width: 1280px; margin: 0 auto; padding: 0 16px; width: 100%; }

        /* ── Navbar ── */
        .navbar-main { background: var(--navy); }
        .hp-navbar-row { display: flex; align-items: center; gap: 18px; height: 62px; }
        .hp-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; flex-shrink: 0; }
        .hp-logo img { height: 40px; width: auto; object-fit: contain; }
        .hp-logo i { color: #fff; font-size: 1.4rem; }
        .hp-logo span { color: #fff; font-weight: 700; font-size: 1.3rem; }
        .hp-search { flex: 1; display: flex; }
        .hp-search input.form-control { border: none; border-radius: 4px 0 0 4px; font-size: .9rem; }
        .hp-search .btn { background: var(--accent); border: none; color: #fff; border-radius: 0 4px 4px 0; padding: 0 18px; }
        .hp-nav-icons { display: flex; align-items: center; gap: 18px; flex-shrink: 0; }
        .nav-icon { color: rgba(255,255,255,.85); font-size: 1.1rem; position: relative; text-decoration: none; }
        .nav-icon:hover { color: #fff; }
        .nav-icon .badge { font-size: .6rem; position: absolute; top: -6px; right: -8px; }

        /* ── Sub Nav ── */
        .subnav { background: var(--navy); border-top: 1px solid rgba(255,255,255,.12); }
        .subnav .nav-link { color: rgba(255,255,255,.85) !important; font-size: .85rem; padding: .4rem .75rem; }
        .subnav .nav-link:hover { color: #fff !important; }
        .subnav .btn-sell { background: var(--accent); color: #fff; font-size: .8rem; border-radius: 20px; padding: .25rem .85rem; }

        /* ── Footer ── */
        .hp-footer { background: var(--navy); color: #fff; margin-top: 32px; padding: 40px 0 18px; }
        .f-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        @media(min-width: 768px) { .f-grid { grid-template-columns: repeat(3, 1fr); } }
        @media(min-width: 992px) { .f-grid { grid-template-columns: 1.35fr 1fr 1fr 1fr .95fr 1.4fr; } }
        .hp-footer h6 { color: #fff; font-weight: 600; font-size: .92rem; margin-bottom: .8rem; }
        .hp-footer a { color: rgba(255,255,255,.75); text-decoration: none; font-size: .85rem; }
        .hp-footer a:hover { color: #fff; }
        .hp-footer ul { list-style: none; padding: 0; margin: 0; }
        .hp-footer ul li { margin-bottom: .4rem; }
        .f-contact p { font-size: .82rem; color: rgba(255,255,255,.75); margin: 0; line-height: 1.9; }
        .f-social { display: flex; gap: 10px; }
        .f-social a { width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,.12); display: inline-flex; align-items: center; justify-content: center; }
        .f-social a:hover { background: var(--accent); }
        .f-app-btn { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.25); border-radius: 8px; padding: 7px 12px; margin-bottom: 8px; max-width: 190px; }
        .f-app-btn i { font-size: 1.3rem; color: #fff; }
        .f-app-btn small { display: block; font-size: .6rem; color: rgba(255,255,255,.65); line-height: 1.1; }
        .f-app-btn strong { font-size: .78rem; color: #fff; line-height: 1.1; }
        .f-bottom { display: flex; flex-direction: column; align-items: center; gap: 8px; border-top: 1px solid rgba(255,255,255,.15); margin-top: 26px; padding-top: 16px; font-size: .82rem; color: rgba(255,255,255,.6); }
        @media(min-width: 768px) { .f-bottom { flex-direction: row; justify-content: space-between; } }

        /* ── Mobile bottom nav ── */
        .mobile-bottom-nav { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: 0 -2px 10px rgba(0,0,0,.1); z-index: 1000; padding: .5rem 0; }
        @media(max-width:768px) { .mobile-bottom-nav { display: flex; justify-content: space-around; } }
        .mobile-bottom-nav a { color: #555; font-size: .7rem; text-align: center; flex: 1; text-decoration: none; }
        .mobile-bottom-nav a i { font-size: 1.2rem; display: block; }
        .mobile-bottom-nav a.active { color: var(--primary); }
        @media(max-width:768px) { body { padding-bottom: 60px; } }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body>

<?php echo $__env->make('partials.seo-body', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>}


<nav class="navbar-main sticky-top">
    <div class="hp-container">
        <div class="hp-navbar-row">
            <a class="hp-logo" href="<?php echo e(route('home')); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('app_logo')): ?>
                    <img src="<?php echo e(asset('storage/logo/' . rawurlencode(setting('app_logo')))); ?>"
                         alt="<?php echo e(setting('app_name') ?: config('app.name')); ?>">
                <?php else: ?>
                    <i class="fas fa-shopping-bag"></i>
                    <span><?php echo e(setting('app_name') ?: config('app.name')); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>

            <form class="hp-search" method="GET" action="<?php echo e(route('home')); ?>">
                <input type="search" name="q" class="form-control" placeholder="Search products...">
                <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            </form>

            <div class="hp-nav-icons">
                <?php echo $__env->make('partials.customer-account-icon', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <a href="#" class="nav-icon d-none d-md-block">
                    <i class="fas fa-heart"></i>
                </a>
                <?php
                    $cartItems = session()->get('cart', []);
                    $cartCount = array_sum(array_column($cartItems, 'qty'));
                ?>
                <a href="<?php echo e(route('frontend.cart.view')); ?>" class="nav-icon"
                   x-data="{ cartCount: <?php echo e($cartCount); ?> }"
                   x-on:cart-updated.window="if ($event.detail && $event.detail.count !== undefined) cartCount = $event.detail.count">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-warning text-dark" x-text="cartCount"><?php echo e($cartCount); ?></span>
                </a>
            </div>
        </div>
    </div>
</nav>


<div class="subnav d-none d-md-block">
    <div class="hp-container">
        <nav class="d-flex align-items-center flex-wrap gap-1 py-1">
            <a href="<?php echo e(route('frontend.category.view')); ?>" class="nav-link">Categories</a>
            <a href="<?php echo e(route('home')); ?>" class="nav-link">Products</a>
            <a href="<?php echo e(route('register')); ?>" class="btn btn-sell ms-1">Sell on <?php echo e(setting('app_name') ?: 'Padmazon'); ?></a>
            <a href="<?php echo e(route('frontend.flash.sale.products')); ?>" class="nav-link">Discounts</a>
            <a href="#" class="nav-link">All Shops</a>
            <a href="<?php echo e(route('frontend.brand.view')); ?>" class="nav-link">All Brands</a>
            <a href="<?php echo e(route('frontend.order.tracking')); ?>" class="nav-link">Track Order</a>
            <a href="<?php echo e(route('home')); ?>" class="nav-link">Home</a>
        </nav>
    </div>
</div>


<?php echo e($slot); ?>



<footer class="hp-footer">
    <div class="hp-container">
        <div class="f-grid">

            <div class="f-contact">
                <h6>Contact Us</h6>
                <p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('contact_address')): ?>
                    <i class="fas fa-map-marker-alt me-2"></i><?php echo e(setting('contact_address')); ?><br>
                    <?php else: ?>
                    <i class="fas fa-map-marker-alt me-2"></i>Dhaka, Bangladesh<br>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('contact_phone')): ?>
                    <i class="fas fa-phone me-2"></i><?php echo e(setting('contact_phone')); ?><br>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('contact_email')): ?>
                    <i class="fas fa-envelope me-2"></i><?php echo e(setting('contact_email')); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>

            <div>
                <h6>Company</h6>
                <ul>
                    <li><a href="<?php echo e(route('frontend.about.us')); ?>">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>

            <div>
                <h6>Seller Center</h6>
                <ul>
                    <li><a href="<?php echo e(route('register')); ?>">Sell on <?php echo e(setting('app_name') ?: 'Padmazon'); ?></a></li>
                    <li><a href="<?php echo e(route('login')); ?>">Seller Dashboard</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="<?php echo e(route('frontend.refund.and.returns')); ?>">Returns Policy</a></li>
                </ul>
            </div>

            <div>
                <h6>Account</h6>
                <ul>
                    <li><a href="<?php echo e(route('login')); ?>">Login</a></li>
                    <li><a href="<?php echo e(route('register')); ?>">Register</a></li>
                    <li><a href="<?php echo e(route('frontend.cart.view')); ?>">My Cart</a></li>
                    <li><a href="<?php echo e(route('frontend.order.tracking')); ?>">Track Order</a></li>
                </ul>
            </div>

            <div>
                <h6>Social</h6>
                <div class="f-social">
                    <a href="<?php echo e(setting('facebook') ?: '#'); ?>"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo e(setting('instagram') ?: '#'); ?>"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo e(setting('twitter') ?: '#'); ?>"><i class="fab fa-twitter"></i></a>
                    <a href="<?php echo e(setting('youtube') ?: '#'); ?>"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div>
                <h6>Mobile App</h6>
                <a href="#" class="f-app-btn">
                    <i class="fab fa-google-play"></i>
                    <span><small>GET IT ON</small><strong>Google Play</strong></span>
                </a>
                <a href="#" class="f-app-btn">
                    <i class="fab fa-apple"></i>
                    <span><small>Download on the</small><strong>App Store</strong></span>
                </a>
            </div>

        </div>

        <div class="f-bottom">
            <span>© <?php echo e(date('Y')); ?> <?php echo e(setting('app_name') ?: 'Padmazon'); ?>. All rights reserved.</span>
            <div class="d-flex gap-3">
                <a href="<?php echo e(route('frontend.privacy.policy')); ?>">Privacy Policy</a>
                <a href="<?php echo e(route('frontend.terms.and.conditions')); ?>">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>


<div class="mobile-bottom-nav d-md-none">
    <a href="<?php echo e(route('home')); ?>">
        <i class="fas fa-home"></i>Home
    </a>
    <a href="<?php echo e(route('frontend.category.view')); ?>">
        <i class="fas fa-th-large"></i>Categories
    </a>
    <a href="<?php echo e(route('frontend.cart.view')); ?>">
        <i class="fas fa-shopping-cart"></i>Cart
    </a>
    <a href="#">
        <i class="fas fa-heart"></i>Wishlist
    </a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Services\CustomerAuthSettings::enabled()): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
        <button type="button" data-cust-open="login" style="border:none;background:none;cursor:pointer;color:inherit;font:inherit;">
            <i class="fas fa-user"></i>Account
        </button>
        <?php else: ?>
        <a href="<?php echo e(route('customer.dashboard')); ?>">
            <i class="fas fa-user"></i>Account
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        <a href="<?php echo e(route('profile.index')); ?>">
            <i class="fas fa-user"></i>Account
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    toastr.options = {
        "progressBar": true,
        "closeButton": true,
        "positionClass": "toast-top-right",
        "timeOut": 2500,
        "preventDuplicates": true,
        "preventOpenDuplicates": true
    };

    <?php if(session()->has('success')): ?>
    toastr.success("<?php echo e(session('success')); ?>");
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(session()->has('error')): ?>
    toastr.error("<?php echo e(session('error')); ?>");
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    document.addEventListener('livewire:init', () => {
        Livewire.on('success', (event) => {
            const message = event.message || event[0]?.message || 'Success!';
            toastr.success(message);
        });

        Livewire.on('error', (event) => {
            const message = event.message || event[0]?.message || 'Error!';
            toastr.error(message);
        });

        Livewire.on('track-event', ({ eventName, data }) => {
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

<?php echo $__env->make('partials.customer-account-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.customer-auth-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->yieldPushContent('scripts'); ?>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/layouts/frontend.blade.php ENDPATH**/ ?>