<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'instance' => 'home',
    'title' => 'New Arrivals',
    'tabNewLabel' => 'New Arrival',
    'tabHotLabel' => 'Hot Items',
    'newArrivals' => collect(),
    'hotItems' => collect(),
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'instance' => 'home',
    'title' => 'New Arrivals',
    'tabNewLabel' => 'New Arrival',
    'tabHotLabel' => 'Hot Items',
    'newArrivals' => collect(),
    'hotItems' => collect(),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $newArrivals = collect($newArrivals)->filter();
    $hotItems = collect($hotItems)->filter();
    $tabs = collect()
        ->when($newArrivals->isNotEmpty(), fn ($c) => $c->put('new', $tabNewLabel))
        ->when($hotItems->isNotEmpty(), fn ($c) => $c->put('hot', $tabHotLabel));
    $showTabs = $tabs->count() > 1;
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tabs->isNotEmpty()): ?>
    <?php if (! $__env->hasRenderedOnce('9c76a413-e830-4ab1-b205-358984f35866')): $__env->markAsRenderedOnce('9c76a413-e830-4ab1-b205-358984f35866'); ?>
        <style>
            /* ── Showcase: tabbed product row ── */
            .sc-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 16px; }
            .sc-tabs { display: flex; gap: 22px; flex-shrink: 0; }
            .sc-tabs label { font-size: .9rem; color: #6b7280; cursor: pointer; padding: 2px 0 8px;
                border-bottom: 2px solid transparent; white-space: nowrap; }
            .sc-tab-new, .sc-tab-hot { position: absolute; opacity: 0; pointer-events: none; }

            .sc-pane { display: none; }
            .sc-pane-single { display: block; }
            .sc-tab-new:checked ~ .sc-body .sc-pane-new,
            .sc-tab-hot:checked ~ .sc-body .sc-pane-hot { display: block; }
            .sc-tab-new:checked ~ .sc-head label[for$="-new"],
            .sc-tab-hot:checked ~ .sc-head label[for$="-hot"] {
                color: #111827; font-weight: 700; border-bottom-color: var(--navy); }

            .sc-slider { display: flex; gap: 14px; overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; }
            .sc-slider::-webkit-scrollbar { display: none; }
            .sc-card { flex: 0 0 calc((100% - (var(--sc-vis, 6) - 1) * 14px) / var(--sc-vis, 6)); min-width: 0; }
            @media (max-width: 1199px) { .sc-card { --sc-vis: 5; } }
            @media (max-width: 991px) { .sc-card { --sc-vis: 4; } }
            @media (max-width: 767px) { .sc-card { --sc-vis: 3; } }
            @media (max-width: 575px) { .sc-card { --sc-vis: 2; scroll-snap-align: start; }
                .sc-slider { scroll-snap-type: x mandatory; } }

            .sc-foot { display: flex; align-items: center; gap: 16px; margin-top: 14px; }
            .sc-track { flex: 1; height: 2px; background: #e8eaf0; border-radius: 2px; overflow: hidden; }
            .sc-fill { height: 100%; width: 25%; background: var(--navy); transition: width .18s ease; }
            .sc-nav { display: flex; gap: 10px; flex-shrink: 0; }
            .sc-arrow { width: 38px; height: 38px; border-radius: 50%; border: 1px solid var(--line); background: #fff;
                color: #374151; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;
                transition: border-color .18s, color .18s, opacity .18s; }
            .sc-arrow:hover:not(:disabled) { border-color: var(--navy); color: var(--navy); }
            .sc-arrow:disabled { opacity: .35; cursor: default; }
            /* Nothing to scroll (e.g. 6 products in 6 slots) hides the whole nav row */
            .sc-sliderwrap.is-static .sc-foot { display: none; }
            @media (max-width: 575px) { .sc-nav { display: none; } }
        </style>
    <?php endif; ?>

    <section class="hp-sec">
        <div class="hp-container">
            <div class="hp-box">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTabs): ?>
                    
                    <input type="radio" class="sc-tab-new" name="sc-tab-<?php echo e($instance); ?>"
                        id="sc-tab-<?php echo e($instance); ?>-new" checked>
                    <input type="radio" class="sc-tab-hot" name="sc-tab-<?php echo e($instance); ?>"
                        id="sc-tab-<?php echo e($instance); ?>-hot">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="sc-head">
                    <h5 class="hp-sec-title"><?php echo e($title); ?></h5>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTabs): ?>
                        <div class="sc-tabs">
                            <label for="sc-tab-<?php echo e($instance); ?>-new"><?php echo e($tabNewLabel); ?></label>
                            <label for="sc-tab-<?php echo e($instance); ?>-hot"><?php echo e($tabHotLabel); ?></label>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="sc-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['new', $newArrivals], ['hot', $hotItems]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$paneKey, $paneProducts]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paneProducts->isNotEmpty()): ?>
                            <div class="sc-pane sc-pane-<?php echo e($paneKey); ?> <?php echo e($showTabs ? '' : 'sc-pane-single'); ?>">
                                <div class="sc-sliderwrap" data-sc-slider>
                                    <div class="sc-slider">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $paneProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $pi = $product->price_info; ?>
                                            <div class="sc-card">
                                                <div class="hp-pcard">
                                                    <a href="<?php echo e(route('frontend.product.single.view', $product->slug)); ?>"
                                                        class="hp-pimg" wire:navigate>
                                                        <img src="<?php echo e(asset('storage/' . $product->thumbnail)); ?>"
                                                            alt="<?php echo e($product->name); ?>" loading="lazy"
                                                            onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pi['percent'] > 0): ?>
                                                            <span class="hp-pbadge">-<?php echo e($pi['percent']); ?>%</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </a>
                                                    <div class="hp-pbody">
                                                        <p class="hp-ptitle"><?php echo e($product->name); ?></p>
                                                        <div class="hp-stars">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($s = 1; $s <= 5; $s++): ?>
                                                                <i class="fas fa-star"></i>
                                                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            <small>(<?php echo e($product->reviews_count ?? 0); ?>)</small>
                                                        </div>
                                                        <div class="hp-price mb-2">
                                                            ৳<?php echo e(number_format($pi['final'], 0)); ?>

                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pi['has_discount']): ?>
                                                                <span class="old">৳<?php echo e(number_format($pi['original'], 0)); ?></span>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                        <?php echo $__env->make('partials.purchase-add-to-cart', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    <div class="sc-foot">
                                        <div class="sc-track"><div class="sc-fill"></div></div>
                                        <div class="sc-nav">
                                            <button type="button" class="sc-arrow" data-sc-dir="-1"
                                                aria-label="Scroll left">
                                                <i class="fas fa-chevron-left" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="sc-arrow" data-sc-dir="1"
                                                aria-label="Scroll right">
                                                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php if (! $__env->hasRenderedOnce('b9abc37f-bb9c-4ca5-9308-5f77b1e0b616')): $__env->markAsRenderedOnce('b9abc37f-bb9c-4ca5-9308-5f77b1e0b616'); ?>
        <script>
            (function () {
                function initShowcases() {
                    var wraps = document.querySelectorAll('[data-sc-slider]');
                    Array.prototype.forEach.call(wraps, function (wrap) {
                        if (wrap.dataset.scBound) return;
                        wrap.dataset.scBound = '1';

                        var slider = wrap.querySelector('.sc-slider');
                        var fill = wrap.querySelector('.sc-fill');
                        var arrows = wrap.querySelectorAll('[data-sc-dir]');

                        function step() {
                            var card = slider.querySelector('.sc-card');
                            return card ? card.offsetWidth + 14 : 220;
                        }

                        function update() {
                            var max = slider.scrollWidth - slider.clientWidth;
                            wrap.classList.toggle('is-static', max <= 1);
                            if (fill) {
                                var ratio = slider.scrollWidth > 0
                                    ? slider.clientWidth / slider.scrollWidth : 1;
                                fill.style.width = Math.max(8, ratio * 100) + '%';
                            }
                            Array.prototype.forEach.call(arrows, function (btn) {
                                var dir = Number(btn.getAttribute('data-sc-dir'));
                                btn.disabled = dir < 0 ? slider.scrollLeft <= 1 : slider.scrollLeft >= max - 1;
                            });
                        }

                        Array.prototype.forEach.call(arrows, function (btn) {
                            btn.addEventListener('click', function () {
                                slider.scrollBy({
                                    left: step() * Number(btn.getAttribute('data-sc-dir')),
                                    behavior: 'smooth'
                                });
                            });
                        });

                        slider.addEventListener('scroll', update);
                        window.addEventListener('resize', update);
                        update();
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initShowcases);
                } else {
                    initShowcases();
                }
                document.addEventListener('livewire:navigated', initShowcases);
            })();
        </script>
    <?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\resources\views/components/product-showcase.blade.php ENDPATH**/ ?>