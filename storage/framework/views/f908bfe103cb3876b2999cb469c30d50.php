<div class="pg-public">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page->feature_image): ?>
        <div class="pg-hero">
            <img src="<?php echo e(asset('storage/' . $page->feature_image)); ?>" alt="<?php echo e($page->title); ?>">
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="container py-4 py-lg-5">
        <div class="pg-sheet">
            <h1 class="pg-title"><?php echo e($page->title); ?></h1>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page->description): ?>
                <div class="pg-prose"><?php echo $page->description; ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $type = $block['type'] ?? 'text'; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'text'): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($block['content'])): ?>
                        <div class="pg-section pg-prose"><?php echo $block['content']; ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php elseif($type === 'image'): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($block['image'])): ?>
                        <figure class="pg-figure">
                            <img src="<?php echo e(asset('storage/' . $block['image'])); ?>" alt="<?php echo e($block['caption'] ?? ''); ?>" loading="lazy">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($block['caption'])): ?>
                                <figcaption><?php echo e($block['caption']); ?></figcaption>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </figure>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php elseif($type === 'columns'): ?>
                    <?php
                        $columns = $block['columns'] ?? [];
                        $colClass = count($columns) === 3 ? 'col-md-4' : 'col-md-6';
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($columns)): ?>
                        <div class="row g-4 pg-section pg-columns">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="<?php echo e($colClass); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($column['type'] ?? 'text') === 'image'): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($column['image'])): ?>
                                            <figure class="pg-figure mb-0">
                                                <img src="<?php echo e(asset('storage/' . $column['image'])); ?>"
                                                    alt="<?php echo e($column['caption'] ?? ''); ?>" loading="lazy">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($column['caption'])): ?>
                                                    <figcaption><?php echo e($column['caption']); ?></figcaption>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </figure>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php elseif(! empty($column['content'])): ?>
                                        <div class="pg-prose"><?php echo $column['content']; ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <style>
        .pg-public { background: #fff; }
        .pg-public .pg-hero img {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            display: block;
        }
        .pg-public .pg-sheet {
            max-width: 960px;
            margin: 0 auto;
        }
        .pg-public .pg-title {
            font-size: clamp(1.75rem, 3vw, 2.5rem);
            font-weight: 700;
            color: #292929;
            margin-bottom: 1.25rem;
        }
        .pg-public .pg-section { margin-top: 2rem; }
        .pg-public .pg-prose { color: #41464b; font-size: 1rem; line-height: 1.8; }
        .pg-public .pg-prose > :last-child { margin-bottom: 0; }
        .pg-public .pg-prose h1,
        .pg-public .pg-prose h2,
        .pg-public .pg-prose h3,
        .pg-public .pg-prose h4 {
            color: #292929;
            font-weight: 700;
            line-height: 1.3;
            margin: 1.75rem 0 .75rem;
        }
        .pg-public .pg-prose h1 { font-size: 1.9rem; }
        .pg-public .pg-prose h2 { font-size: 1.5rem; }
        .pg-public .pg-prose h3 { font-size: 1.25rem; }
        .pg-public .pg-prose p { margin-bottom: 1rem; }
        .pg-public .pg-prose img {
            max-width: 100%;
            height: auto;
            border-radius: .6rem;
            margin: .5rem 0;
        }
        .pg-public .pg-prose blockquote {
            border-left: 4px solid var(--shop-color-primary, #EB0000);
            background: #fbf7f7;
            margin: 1.5rem 0;
            padding: .9rem 1.25rem;
            border-radius: .35rem;
            color: #555;
        }
        .pg-public .pg-prose ul,
        .pg-public .pg-prose ol { padding-left: 1.4rem; margin-bottom: 1rem; }
        .pg-public .pg-prose li { margin-bottom: .35rem; }
        .pg-public .pg-prose a { color: var(--shop-color-primary, #EB0000); text-decoration: underline; }
        .pg-public .pg-prose iframe,
        .pg-public .pg-prose video {
            max-width: 100%;
            border-radius: .6rem;
        }
        .pg-public .pg-prose .ql-align-center { text-align: center; }
        .pg-public .pg-prose .ql-align-right { text-align: right; }
        .pg-public .pg-prose .ql-align-justify { text-align: justify; }
        .pg-public .pg-figure { margin: 0; }
        .pg-public .pg-figure img {
            width: 100%;
            height: auto;
            border-radius: .75rem;
            display: block;
        }
        .pg-public .pg-figure figcaption {
            margin-top: .6rem;
            font-size: .875rem;
            color: #7a7f87;
            text-align: center;
        }
        .pg-public .pg-columns .pg-prose { font-size: .98rem; }
        @media (max-width: 575.98px) {
            .pg-public .pg-hero img { max-height: 240px; }
            .pg-public .pg-section { margin-top: 1.5rem; }
        }
    </style>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\frontend\pages\public-page.blade.php ENDPATH**/ ?>