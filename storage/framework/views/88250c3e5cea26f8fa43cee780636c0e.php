<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(setting('app_name') ?: config('app.name')); ?></title>

    <?php $favicon = setting('fev_icon') ?: setting('app_logo'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($favicon): ?>
    <link rel="icon" href="<?php echo e(asset('storage/logo/' . rawurlencode($favicon))); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* ── Layout container (max-w-7xl mx-auto px-4) ── */
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
        .nav-icon { color: rgba(255,255,255,.85); font-size: 1.1rem; position: relative; }
        .nav-icon:hover { color: #fff; }
        .nav-icon .badge { font-size: .6rem; position: absolute; top: -6px; right: -8px; }

        /* ── Sub Nav ── */
        .subnav { background: var(--navy); border-top: 1px solid rgba(255,255,255,.12); }
        .subnav .nav-link { color: rgba(255,255,255,.85) !important; font-size: .85rem; padding: .4rem .75rem; }
        .subnav .nav-link:hover { color: #fff !important; }
        .subnav .btn-sell { background: var(--accent); color: #fff; font-size: .8rem; border-radius: 20px; padding: .25rem .85rem; }

        /* ── Hero: categories + banner ── */
        .hp-hero { display: grid; grid-template-columns: 1fr; gap: 16px; margin-top: 16px; }
        @media(min-width: 992px) { .hp-hero { grid-template-columns: 240px 1fr; } }

        .hp-cat { display: none; }
        @media(min-width: 992px) { .hp-cat { display: block; } }
        .hp-cat-card { background: #fff; border-radius: 12px; border: 1px solid var(--line); height: 380px; overflow-y: auto; }
        .hp-cat-item { display: flex; justify-content: space-between; align-items: center; padding: .55rem 1rem; font-size: .85rem; color: #4b5563; text-decoration: none; border-bottom: 1px solid #f3f4f6; transition: background .15s, color .15s, padding-left .15s; }
        .hp-cat-item:last-child { border-bottom: none; }
        .hp-cat-item:hover { background: #f7f8fb; color: var(--primary); padding-left: 1.2rem; }
        .hp-cat-item i { font-size: .65rem; color: #9ca3af; }

        .hero-carousel { border-radius: 12px; overflow: hidden; }
        .hero-carousel .carousel-item img { height: 380px; width: 100%; object-fit: cover; display: block; }
        @media(max-width: 991px) { .hero-carousel .carousel-item img { height: 220px; } }
        .hp-slide-fallback .inner { height: 380px; display: flex; flex-direction: column; justify-content: center; padding: 0 8%; background: linear-gradient(120deg, var(--navy), var(--primary)); }
        @media(max-width: 991px) { .hp-slide-fallback .inner { height: 220px; } }
        .hp-slide-fallback .tag { color: #fde68a; font-weight: 600; letter-spacing: 1px; font-size: .8rem; }
        .hp-slide-fallback h2 { color: #fff; font-weight: 800; font-size: 2rem; margin: 4px 0; }
        .hp-slide-fallback .off { color: #fde68a; font-weight: 700; font-size: 1.4rem; }
        .carousel-caption { background: linear-gradient(90deg, rgba(18,34,91,.85), rgba(18,34,91,0) 70%); border-radius: 8px; text-align: left; left: 5%; right: auto; bottom: 20%; padding: 1rem 1.4rem; }

        /* ── Mobile category pills ── */
        .hp-pills { display: flex; gap: 8px; overflow-auto; padding-bottom: 8px; scrollbar-width: none; }
        .hp-pill { background: var(--primary); color: #fff; font-size: .78rem; padding: .4rem .8rem; border-radius: 20px; flex-shrink: 0; text-decoration: none; }
        .hp-pill:hover { color: #fff; }

        /* ── Section shell ── */
        .hp-sec { margin-top: 24px; }
        .hp-box { background: #fff; border-radius: 12px; border: 1px solid var(--line); padding: 20px; }
        .hp-sec-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .hp-sec-title { font-size: 1.1rem; font-weight: 700; color: #111827; border-left: 4px solid var(--red); padding-left: 10px; margin: 0; }
        .hp-see-more { font-size: .85rem; color: var(--primary); text-decoration: none; }
        .hp-see-more:hover { text-decoration: underline; }

        /* ── Deal / Flash sale ── */
        .hp-deal-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
        @media(min-width: 992px) { .hp-deal-grid { grid-template-columns: 250px 1fr; } }
        @media(min-width: 1200px) { .hp-deal-grid { grid-template-columns: 280px 1fr; } }
        .hp-deal-left { background: linear-gradient(160deg, #fb923c, var(--orange) 55%, #ea580c); border-radius: 10px; color: #fff; padding: 22px 18px; display: flex; flex-direction: column; }
        .hp-deal-title { font-size: 1.05rem; font-weight: 700; line-height: 1.35; }
        .hp-deal-title .pct { color: #fde68a; }
        .hp-countdown { display: flex; gap: 8px; margin: 16px 0; }
        .cd-box { background: rgba(255,255,255,.18); border-radius: 8px; text-align: center; padding: 6px 8px; min-width: 52px; }
        .cd-box .num { font-size: 1.15rem; font-weight: 700; display: block; }
        .cd-box .lbl { font-size: .62rem; opacity: .85; }
        .btn-deal-cta { margin-top: auto; background: #fff; color: var(--orange); border: none; border-radius: 3px; font-weight: 600; font-size: .85rem; padding: .5rem 1rem; display: inline-block; text-align: center; text-decoration: none; }
        .btn-deal-cta:hover { background: #fff7ed; color: #ea580c; }
        .hp-deal-products { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        @media(min-width: 576px) { .hp-deal-products { grid-template-columns: repeat(3, 1fr); } }
        @media(min-width: 992px) { .hp-deal-products { grid-template-columns: repeat(5, 1fr); } }

        /* ── Product card ── */
        .hp-pcard { background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 10px; display: flex; flex-direction: column; transition: box-shadow .2s, transform .2s; height: 100%; }
        .hp-pcard:hover { box-shadow: 0 8px 20px rgba(17,24,39,.1); transform: translateY(-2px); }
        .hp-pimg { position: relative; display: block; aspect-ratio: 1 / 1; background: #fafafa; border-radius: 6px; padding: 8px; overflow: hidden; }
        .hp-pimg img { width: 100%; height: 100%; object-fit: contain; }
        .hp-pbadge { position: absolute; top: 8px; left: 8px; background: var(--red); color: #fff; font-size: .68rem; font-weight: 600; border-radius: 4px; padding: 2px 7px; }
        .hp-pbody { display: flex; flex-direction: column; flex: 1; padding-top: 8px; }
        .hp-ptitle { font-size: .82rem; font-weight: 600; color: #1f2937; line-height: 1.35; margin: 0 0 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.2em; }
        .hp-stars { margin-bottom: 4px; }
        .hp-stars i { font-size: .62rem; color: #000; }
        .hp-stars small { color: #6b7280; font-size: .7rem; }
        .hp-price { font-weight: 700; color: #111827; font-size: .95rem; }
        .hp-price .old { text-decoration: line-through; color: #9ca3af; font-weight: 400; font-size: .78rem; margin-left: 4px; }
        .btn-add-cart { width: 100%; background: #fff; color: var(--navy); border: 1px solid var(--navy); border-radius: 3px; font-size: .8rem; padding: .42rem; font-weight: 600; margin-top: auto; transition: background .2s ease, color .2s ease; }
        .btn-add-cart:hover { background: var(--navy); color: #fff; }

        /* ── Top shops ── */
        .hp-shops { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        @media(min-width: 768px) { .hp-shops { grid-template-columns: repeat(4, 1fr); } }
        @media(min-width: 992px) { .hp-shops { grid-template-columns: repeat(6, 1fr); } }
        .hp-shop-card { background: #fff; border: 1px solid var(--line); border-radius: 10px; text-align: center; padding: 16px 8px; text-decoration: none; transition: box-shadow .2s, transform .2s; }
        .hp-shop-card:hover { box-shadow: 0 8px 20px rgba(17,24,39,.1); transform: translateY(-2px); }
        .hp-shop-card img { width: 64px; height: 64px; object-fit: cover; border-radius: 50%; border: 2px solid #f3f4f6; margin-bottom: 8px; }
        .hp-shop-logo-fallback { width: 64px; height: 64px; border-radius: 50%; background: #eef1f7; color: var(--navy); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 8px; }
        .hp-shop-name { font-size: .78rem; font-weight: 600; color: #1f2937; margin: 0; }
        .hp-shop-count { font-size: .7rem; color: #6b7280; }

        /* ── Top brands ── */
        .hp-brands { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        @media(min-width: 768px) { .hp-brands { grid-template-columns: repeat(6, 1fr); } }
        .hp-brand-card { background: #fff; border: 1px solid var(--line); border-radius: 10px; height: 88px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; text-decoration: none; transition: box-shadow .2s; padding: 8px; }
        .hp-brand-card:hover { box-shadow: 0 8px 20px rgba(17,24,39,.1); }
        .hp-brand-card img { max-height: 56px; max-width: 90%; object-fit: contain; }
        .hp-brand-card span { font-size: .72rem; color: #4b5563; font-weight: 600; }

        /* ── Homepage Categories grid ── */
        .hp-cat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        @media(min-width: 576px) { .hp-cat-grid { grid-template-columns: repeat(3, 1fr); } }
        @media(min-width: 768px) { .hp-cat-grid { grid-template-columns: repeat(4, 1fr); } }
        @media(min-width: 992px) { .hp-cat-grid { grid-template-columns: repeat(6, 1fr); } }

        /* ── Recommended grid ── */
        .rp-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        @media(min-width: 576px) { .rp-grid { grid-template-columns: repeat(3, 1fr); } }
        @media(min-width: 992px) { .rp-grid { grid-template-columns: repeat(6, 1fr); } }

        /* ── Load more ── */
        .hp-load-wrap { text-align: center; margin-top: 22px; }
        .hp-load-more { display: inline-block; border: 1px solid var(--navy); color: var(--navy); background: #fff; border-radius: 20px; padding: .55rem 2rem; font-size: .85rem; font-weight: 600; text-decoration: none; transition: background .2s, color .2s; }
        .hp-load-more:hover { background: var(--navy); color: #fff; }

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
    
    <?php echo $__env->make('partials.seo-tags', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                <a href="<?php echo e(route('profile.index')); ?>" class="nav-icon d-none d-md-block">
                    <i class="fas fa-user"></i>
                </a>
                <a href="#" class="nav-icon d-none d-md-block">
                    <i class="fas fa-heart"></i>
                </a>
                <a href="<?php echo e(route('frontend.cart.view')); ?>" class="nav-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-warning text-dark"><?php echo e($cartCount ?? 0); ?></span>
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
            <a href="<?php echo e(route('register')); ?>" class="btn btn-sell ms-1">Sell on Padmazon</a>
            <a href="#" class="nav-link">Discounts</a>
            <a href="#" class="nav-link">All Shops</a>
            <a href="<?php echo e(route('frontend.brand.view')); ?>" class="nav-link">All Brands</a>
            <a href="<?php echo e(route('frontend.order.tracking')); ?>" class="nav-link">Track Order</a>
            <a href="<?php echo e(route('home')); ?>" class="nav-link">Home</a>
        </nav>
    </div>
</div>


<section class="hp-container">
    <div class="hp-hero">

        
        <aside class="hp-cat">
            <?php echo $__env->make('livewire.frontend.layouts.partial.category.category-flyout', ['categories' => $categories], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </aside>

        
        <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $heroBanners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="carousel-item <?php if($index === 0): ?> active <?php endif; ?>">
                    <img src="<?php echo e(asset('storage/'.$banner->image)); ?>" class="d-block w-100" alt="<?php echo e($banner->title); ?>">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 class="mb-1 fw-bold"><?php echo e($banner->title); ?></h5>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banner->description): ?>
                        <p class="mb-2 small"><?php echo e($banner->description); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banner->link): ?>
                        <a href="<?php echo e($banner->link); ?>" class="btn btn-sm btn-warning fw-semibold">Shop Now</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="carousel-item active hp-slide-fallback">
                    <div class="inner">
                        <span class="tag">LIMITED TIME</span>
                        <h2>SUPER SALE</h2>
                        <span class="off">UP TO 40% OFF</span>
                        <div class="mt-3">
                            <a href="<?php echo e(route('frontend.flash.sale.products')); ?>" class="btn btn-sm btn-warning fw-semibold">Shop Now</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heroBanners->count() > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
            <div class="carousel-indicators">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $heroBanners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo e($i); ?>" <?php if($i===0): ?> class="active" <?php endif; ?>></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="hp-pills d-lg-none mt-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('frontend.category.view', $cat->slug)); ?>" class="hp-pill"><?php echo e($cat->name); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($flashSaleProducts->count()): ?>
<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-deal-grid">

                <div class="hp-deal-left">
                    <div class="hp-deal-title">
                        Limited Time <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($offerPercent > 0): ?><span class="pct"><?php echo e($offerPercent); ?>%</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> Discount Offer Products
                    </div>
                    <div class="hp-countdown">
                        <div class="cd-box"><span class="num" id="cdDays">00</span><span class="lbl">Days</span></div>
                        <div class="cd-box"><span class="num" id="cdHours">00</span><span class="lbl">Hours</span></div>
                        <div class="cd-box"><span class="num" id="cdMins">00</span><span class="lbl">Min</span></div>
                        <div class="cd-box"><span class="num" id="cdSecs">00</span><span class="lbl">Sec</span></div>
                    </div>
                    <a href="<?php echo e(route('frontend.flash.sale.products')); ?>" class="btn-deal-cta">View Products</a>
                </div>

                <div class="hp-deal-products">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $flashSaleProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $pi = $product->price_info; ?>
                    <div class="hp-pcard">
                        <a href="<?php echo e(route('frontend.product.single.view', $product->slug)); ?>" class="hp-pimg">
                            <img src="<?php echo e(asset('storage/'.$product->thumbnail)); ?>" alt="<?php echo e($product->name); ?>"
                                 onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pi['percent'] > 0): ?>
                            <span class="hp-pbadge">-<?php echo e($pi['percent']); ?>%</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </a>
                        <div class="hp-pbody">
                            <p class="hp-ptitle"><?php echo e($product->name); ?></p>
                            <div class="hp-stars">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($s=1;$s<=5;$s++): ?>
                                <i class="fas fa-star"></i>
                                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <small>(<?php echo e($product->reviews_count); ?>)</small>
                            </div>
                            <div class="hp-price mb-2">
                                ৳<?php echo e(number_format($pi['final'], 0)); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pi['has_discount']): ?>
                                <span class="old">৳<?php echo e(number_format($pi['original'], 0)); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <form method="POST" action="<?php echo e(route('frontend.cart.add', $product->id)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-add-cart">
                                    <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topShops->count()): ?>
<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title">Top Shops</h5>
                <a href="#" class="hp-see-more">See more <i class="fas fa-chevron-right" style="font-size:.7rem"></i></a>
            </div>
            <div class="hp-shops">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $topShops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('frontend.user.products.view', $shop->id)); ?>" class="hp-shop-card">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shop->vendorDetails?->profile_image): ?>
                    <img src="<?php echo e(asset('storage/'.$shop->vendorDetails->profile_image)); ?>" alt="<?php echo e($shop->vendorDetails->shop_name ?? $shop->name); ?>">
                    <?php else: ?>
                    <span class="hp-shop-logo-fallback"><i class="fas fa-store"></i></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <p class="hp-shop-name"><?php echo e($shop->vendorDetails->shop_name ?? $shop->name); ?></p>
                    <span class="hp-shop-count"><?php echo e($shop->products_count); ?> items</span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topBrands->count()): ?>
<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title">Top Brands</h5>
                <a href="<?php echo e(route('frontend.brand.view')); ?>" class="hp-see-more">See more <i class="fas fa-chevron-right" style="font-size:.7rem"></i></a>
            </div>
            <div class="hp-brands">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $topBrands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('frontend.brands.products.view', $brand->slug ?? $brand->id)); ?>" class="hp-brand-card">
                    <img src="<?php echo e(asset('storage/'.$brand->logo)); ?>" alt="<?php echo e($brand->name); ?>"
                         onerror="this.src='https://via.placeholder.com/120x60?text=Brand'">
                    <span><?php echo e($brand->name); ?></span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title">Recommended For You</h5>
            </div>
            <div class="rp-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recommendedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $pi = $product->price_info; ?>
                <div class="hp-pcard">
                    <a href="<?php echo e(route('frontend.product.single.view', $product->slug)); ?>" class="hp-pimg">
                        <img src="<?php echo e(asset('storage/'.$product->thumbnail)); ?>" alt="<?php echo e($product->name); ?>"
                             onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pi['percent'] > 0): ?>
                        <span class="hp-pbadge">-<?php echo e($pi['percent']); ?>%</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </a>
                    <div class="hp-pbody">
                        <p class="hp-ptitle"><?php echo e($product->name); ?></p>
                        <div class="hp-stars">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($s=1;$s<=5;$s++): ?>
                            <i class="fas fa-star"></i>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <small>(<?php echo e($product->reviews_count); ?>)</small>
                        </div>
                        <div class="hp-price mb-2">
                            ৳<?php echo e(number_format($pi['final'], 0)); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pi['has_discount']): ?>
                            <span class="old">৳<?php echo e(number_format($pi['original'], 0)); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <form method="POST" action="<?php echo e(route('frontend.cart.add', $product->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-add-cart">
                                <i class="fas fa-cart-plus me-1"></i>Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-4 text-muted" style="grid-column:1/-1">
                    <i class="fas fa-box-open fa-2x mb-2"></i><p class="mb-0">No products found</p>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($show < $totalProducts): ?>
            <div class="hp-load-wrap">
                <a href="<?php echo e(route('home', ['show' => min($show + 12, $totalProducts)])); ?>" class="hp-load-more">
                    LOAD MORE PRODUCTS
                </a>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>



<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($homeCategories->count()): ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $homeCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homeCat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
$categoryProducts = $homeCat->category->products->take($homeCat->product_limit);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categoryProducts->count()): ?>
<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title"><?php echo e($homeCat->category->name); ?></h5>
                <a href="<?php echo e(route('frontend.category.products.view', $homeCat->category->slug)); ?>" class="hp-see-more">See more <i class="fas fa-chevron-right" style="font-size:.7rem"></i></a>
            </div>
            <div class="hp-cat-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categoryProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $pi = $product->price_info; ?>
                <div class="hp-pcard">
                    <a href="<?php echo e(route('frontend.product.single.view', $product->slug)); ?>" class="hp-pimg">
                        <img src="<?php echo e(asset('storage/'.$product->thumbnail)); ?>" alt="<?php echo e($product->name); ?>"
                             onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pi['percent'] > 0): ?>
                        <span class="hp-pbadge">-<?php echo e($pi['percent']); ?>%</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </a>
                    <div class="hp-pbody">
                        <p class="hp-ptitle"><?php echo e($product->name); ?></p>
                        <div class="hp-stars">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($s=1;$s<=5;$s++): ?>
                            <i class="fas fa-star"></i>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <small>(<?php echo e($product->reviews_count); ?>)</small>
                        </div>
                        <div class="hp-price mb-2">
                            ৳<?php echo e(number_format($pi['final'], 0)); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pi['has_discount']): ?>
                            <span class="old">৳<?php echo e(number_format($pi['original'], 0)); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <form method="POST" action="<?php echo e(route('frontend.cart.add', $product->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-add-cart">
                                <i class="fas fa-cart-plus me-1"></i>Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<footer class="hp-footer">
    <div class="hp-container">
        <div class="f-grid">

            <div class="f-contact">
                <h6>Contact Us</h6>
                <p>
                    <i class="fas fa-map-marker-alt me-2"></i>Dhaka, Bangladesh<br>
                    <i class="fas fa-phone me-2"></i>+880 1234 567890<br>
                    <i class="fas fa-envelope me-2"></i>support@padmazon.com
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
                    <li><a href="<?php echo e(route('register')); ?>">Sell on Padmazon</a></li>
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
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
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
    <a href="<?php echo e(route('home')); ?>" class="active">
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
    <a href="<?php echo e(route('profile.index')); ?>">
        <i class="fas fa-user"></i>Account
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success') || session('error')): ?>
<div id="cartToast" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:<?php echo e(session('error') ? '#dc3545' : '#12225B'); ?>;color:#fff;padding:10px 22px;border-radius:4px;z-index:2000;box-shadow:0 4px 14px rgba(0,0,0,.25);font-size:.9rem;">
    <?php echo e(session('success') ?? session('error')); ?>

</div>
<script>setTimeout(function(){ document.getElementById('cartToast') && document.getElementById('cartToast').remove(); }, 2500);</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($flashSaleProducts->count()): ?>
<script>
(function () {
    var target = <?php echo e($countdownTarget->timestamp * 1000); ?>;
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        var diff = Math.max(0, Math.floor((target - Date.now()) / 1000));
        var d = Math.floor(diff / 86400); diff -= d * 86400;
        var h = Math.floor(diff / 3600);  diff -= h * 3600;
        var m = Math.floor(diff / 60);
        var s = diff - m * 60;
        function set(id, v) { var el = document.getElementById(id); if (el) el.textContent = pad(v); }
        set('cdDays', d); set('cdHours', h); set('cdMins', m); set('cdSecs', s);
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\user\bandrose\resources\views/homepage.blade.php ENDPATH**/ ?>