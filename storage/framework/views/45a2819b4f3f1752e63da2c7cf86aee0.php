<div>
    <h4 class="fw-bold mb-4">Google SEO</h4>

    <ul class="nav nav-tabs mb-4 flex-wrap">
        <li class="nav-item"><button class="nav-link <?php echo e($tab === 'console' ? 'active' : ''); ?>" wire:click="setTab('console')">Search Console</button></li>
        <li class="nav-item"><button class="nav-link <?php echo e($tab === 'ga4' ? 'active' : ''); ?>" wire:click="setTab('ga4')">Analytics 4</button></li>
        <li class="nav-item"><button class="nav-link <?php echo e($tab === 'gtm' ? 'active' : ''); ?>" wire:click="setTab('gtm')">Tag Manager</button></li>
        <li class="nav-item"><button class="nav-link <?php echo e($tab === 'sitemap' ? 'active' : ''); ?>" wire:click="setTab('sitemap')">Sitemap</button></li>
        <li class="nav-item"><button class="nav-link <?php echo e($tab === 'robots' ? 'active' : ''); ?>" wire:click="setTab('robots')">Robots.txt</button></li>
        <li class="nav-item"><button class="nav-link <?php echo e($tab === 'schema' ? 'active' : ''); ?>" wire:click="setTab('schema')">Schema Markup</button></li>
    </ul>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'console'): ?>
    <div class="card"><div class="card-body">
        <div class="mb-3">
            <label class="form-label">Verification code <small class="text-muted">(google-site-verification content)</small></label>
            <input type="text" class="form-control" wire:model="gsc_code" placeholder="dBw5Cv...">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['gsc_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <small class="text-muted">Search Console → Settings → Verification → HTML tag থেকে code-টা বসান। Save করলেই homepage-এ meta tag আসবে।</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Property URL</label>
            <input type="url" class="form-control" wire:model="gsc_property" placeholder="https://example.com">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['gsc_property'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <button class="btn btn-primary" wire:click="saveConsole">Save</button>
        <a class="btn btn-outline-secondary ms-2" target="_blank" href="https://search.google.com/search-console">Open Search Console ↗</a>
    </div></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'ga4'): ?>
    <div class="card"><div class="card-body">
        <div class="alert alert-info">এই ID tracking module-এর সাথে <b>shared</b> — এখানে বদলালে browser pixel + server API দুটোতেই যাবে। ডুপ্লিকেট tag বসবে না। <a href="<?php echo e(route('admin.server-side-tracking.index')); ?>">Tracking page →</a></div>
        <div class="mb-3">
            <label class="form-label">Measurement ID</label>
            <input type="text" class="form-control" wire:model="ga4_id" placeholder="G-XXXXXXXXXX" style="max-width:300px">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ga4_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <small class="text-muted d-block">Analytics → Admin → Data Streams → Measurement ID। খালি রাখলে GA4 বন্ধ।</small>
        </div>
        <button class="btn btn-primary" wire:click="saveGa4">Save</button>
    </div></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'gtm'): ?>
    <div class="card"><div class="card-body">
        <div class="mb-3">
            <label class="form-label">Container ID</label>
            <input type="text" class="form-control" wire:model="gtm_id" placeholder="GTM-XXXXXXX" style="max-width:300px">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['gtm_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="form-check form-switch mb-3">
            <input type="checkbox" class="form-check-input" role="switch" wire:model="gtm_active">
            <label class="form-check-label">Tag Manager চালু</label>
        </div>
        <button class="btn btn-primary" wire:click="saveGtm">Save</button>
        <a class="btn btn-outline-secondary ms-2" target="_blank" href="https://tagmanager.google.com">Open Tag Manager ↗</a>
    </div></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'sitemap'): ?>
    <div class="card"><div class="card-body">
        <div class="alert alert-success">Live sitemap: <a target="_blank" href="<?php echo e(url('/sitemap.xml')); ?>"><?php echo e(url('/sitemap.xml')); ?></a> (১ ঘণ্টা cache, product/category save-এ auto-refresh)।</div>
        <div class="form-check form-switch mb-2">
            <input type="checkbox" class="form-check-input" role="switch" wire:model="sitemap_products">
            <label class="form-check-label">Products অন্তর্ভুক্ত</label>
        </div>
        <div class="form-check form-switch mb-3">
            <input type="checkbox" class="form-check-input" role="switch" wire:model="sitemap_categories">
            <label class="form-check-label">Categories অন্তর্ভুক্ত</label>
        </div>
        <button class="btn btn-primary" wire:click="saveSitemap">Save</button>
        <small class="text-muted d-block mt-2">Search Console → Sitemaps-এ উপরের URL submit করুন।</small>
    </div></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'robots'): ?>
    <div class="card"><div class="card-body">
        <div class="alert alert-success">Live file: <a target="_blank" href="<?php echo e(url('/robots.txt')); ?>"><?php echo e(url('/robots.txt')); ?></a></div>
        <div class="mb-3">
            <label class="form-label">Robots.txt content <small class="text-muted">(খালি রাখলে default)</small></label>
            <textarea class="form-control font-monospace" rows="8" wire:model="robots_content"
                placeholder="User-agent: *&#10;Allow: /&#10;&#10;Sitemap: <?php echo e(url('/sitemap.xml')); ?>"></textarea>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['robots_content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <button class="btn btn-primary" wire:click="saveRobots">Save</button>
        <button class="btn btn-outline-secondary ms-2" wire:click="resetRobots">Reset to default</button>
    </div></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'schema'): ?>
    <div class="card"><div class="card-body">
        <small class="text-muted d-block mb-3">Organization info (নাম/logo/ফোন) All Settings থেকে auto আসে। Product schema-তে দাম/স্টক/রেটিং auto বসে।</small>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['schema_org' => 'Organization', 'schema_website' => 'WebSite', 'schema_product' => 'Product (দাম/স্টক/রেটিং)', 'schema_breadcrumb' => 'BreadcrumbList']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="form-check form-switch mb-2">
            <input type="checkbox" class="form-check-input" role="switch" wire:model="<?php echo e($key); ?>">
            <label class="form-check-label"><?php echo e($label); ?></label>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <button class="btn btn-primary mt-2" wire:click="saveSchema">Save</button>
        <a class="btn btn-outline-secondary ms-2 mt-2" target="_blank" href="https://validator.schema.org">Test with Schema Validator ↗</a>
    </div></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/settings/seo-settings.blade.php ENDPATH**/ ?>