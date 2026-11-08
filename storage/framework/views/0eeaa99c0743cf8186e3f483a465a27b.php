<div>
    <form wire:submit.prevent="save">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Add New Product</h5>
                    <div>
                        <a wire:navigate href="<?php echo e(route('admin.product.index')); ?>"
                            class="btn btn-primary btn-sm d-flex align-items-center" style="white-space: nowrap;">
                            <i class="bx bx-left-arrow-alt me-1"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" wire:model.live="name" class="form-control" placeholder="Product name">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" wire:model="slug" class="form-control" placeholder="product-slug"
                            readonly>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select wire:model="category_id" class="form-select">
                            <option value="">Select Category</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>">
                                <?php echo e($category->depth ? str_repeat('— ', $category->depth) : ''); ?><?php echo e($category->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select wire:model="brand_id" class="form-select">
                            <option value="">Select Brand</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brand->id); ?>"><?php echo e($brand->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['brand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            Additional Categories <small class="text-muted">(optional — একাধিক টিক দেওয়া যাবে)</small>
                        </label>
                        <div class="border rounded p-3" style="max-height: 240px; overflow-y: auto;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check" style="margin-left: <?php echo e($category->depth * 20); ?>px;">
                                <input class="form-check-input" type="checkbox"
                                       id="additional-category-<?php echo e($category->id); ?>"
                                       value="<?php echo e($category->id); ?>"
                                       wire:model="additional_category_ids">
                                <label class="form-check-label" for="additional-category-<?php echo e($category->id); ?>">
                                    <?php echo e($category->name); ?>

                                </label>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <small class="text-muted">Primary category ছাড়া যেসব category-তে product দেখাতে চান, সেগুলো টিক দিন।</small>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['additional_category_ids.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger small"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Short Description <small class="text-muted">(optional)</small></label>
                        <textarea wire:model="short_description" class="form-control" rows="3" placeholder="Brief description"></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['short_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Long Description</label>

                        <div wire:ignore class="lx-desc-editor">
                            <div id="quill-editor"><?php echo $long_description; ?></div>
                        </div>
                        <div class="lx-desc-hint"><i class="bx bx-move-vertical"></i> নিচের ডান কোণা ধরে টেনে এডিটর বড়/ছোট করুন</div>

                        <style>
                            .lx-desc-editor{display:flex;flex-direction:column;height:300px;min-height:220px;max-height:75vh;resize:vertical;overflow:hidden;border:1px solid #dfe3e8;border-radius:.65rem;background:#fff;box-shadow:0 1px 2px rgba(16,24,40,.04);transition:border-color .15s,box-shadow .15s}
                            .lx-desc-editor:focus-within{border-color:#696cff;box-shadow:0 0 0 .2rem rgba(105,108,255,.12)}
                            .lx-desc-editor .ql-toolbar.ql-snow{border:0;border-bottom:1px solid #eef0f3;background:#f8f9fc;padding:8px 10px;border-radius:.65rem .65rem 0 0}
                            .lx-desc-editor .ql-container.ql-snow{border:0;flex:1;min-height:0;overflow-y:auto;font-size:15px;font-family:inherit}
                            .lx-desc-editor .ql-editor{min-height:100%;line-height:1.75;padding:14px 16px}
                            .lx-desc-editor .ql-editor.ql-blank::before{color:#a1a7b3;font-style:normal}
                            .lx-desc-editor .ql-snow .ql-tooltip{z-index:5}
                            .lx-desc-hint{font-size:.75rem;color:#8a91a0;margin-top:4px}
                        </style>

                        <input id="long_description_input" type="hidden" wire:model="long_description">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['long_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <script>
                            (function() {
                                var QUILL_CSS = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
                                var QUILL_JS = 'https://cdn.quilljs.com/1.3.6/quill.min.js';

                                function loadCssOnce(href) {
                                    if (!document.querySelector('link[href="' + href + '"]')) {
                                        var l = document.createElement('link');
                                        l.rel = 'stylesheet';
                                        l.href = href;
                                        document.head.appendChild(l);
                                    }
                                }

                                function loadScript(src, cb) {
                                    if (window.Quill) {
                                        cb();
                                        return;
                                    }
                                    var existing = document.querySelector('script[src="' + src + '"]');
                                    if (existing) {
                                        existing.addEventListener('load', cb);
                                        return;
                                    }
                                    var s = document.createElement('script');
                                    s.src = src;
                                    s.onload = cb;
                                    document.body.appendChild(s);
                                }

                                function initQuill() {
                                    var editorEl = document.getElementById('quill-editor');
                                    var hiddenInput = document.getElementById('long_description_input');
                                    if (!editorEl || !window.Quill) return;

                                    // Avoid double initialization
                                    if (editorEl.__quill) return;

                                    // IMPORTANT: capture the original content BEFORE Quill transforms the DOM.
                                    // Once `new Quill()` runs, editorEl.innerHTML becomes Quill's internal
                                    // markup (contenteditable wrapper, ql-clipboard, ql-tooltip, etc.) —
                                    // reading it afterwards is what corrupted saved descriptions.
                                    var originalContent = hiddenInput.value || editorEl.innerHTML || '';

                                    var quill = new Quill(editorEl, {
                                        theme: 'snow',
                                        placeholder: 'Product-এর বিস্তারিত বর্ণনা লিখুন…',
                                        modules: {
                                            toolbar: [
                                                [{
                                                    header: [1, 2, 3, false]
                                                }],
                                                ['bold', 'italic', 'underline', 'strike'],
                                                ['blockquote', 'code-block'],
                                                [{
                                                    list: 'ordered'
                                                }, {
                                                    list: 'bullet'
                                                }],
                                                ['link', 'image'],
                                                ['clean']
                                            ]
                                        }
                                    });

                                    editorEl.__quill = quill;

                                    if (originalContent) {
                                        quill.clipboard.dangerouslyPasteHTML(originalContent);
                                    }

                                    // Always read clean content back from quill.root (the actual
                                    // .ql-editor node) — never from editorEl, which also contains
                                    // the toolbar and other Quill internals.
                                    hiddenInput.value = quill.root.innerHTML;
                                    try {
                                        hiddenInput.dispatchEvent(new Event('input', {
                                            bubbles: true
                                        }));
                                    } catch (e) {}

                                    quill.on('text-change', function() {
                                        var html = quill.root.innerHTML;
                                        hiddenInput.value = html;
                                        try {
                                            hiddenInput.dispatchEvent(new Event('input', {
                                                bubbles: true
                                            }));
                                        } catch (e) {}

                                        function findClosestLivewireId(node) {
                                            var el = node;
                                            while (el) {
                                                if (el.getAttribute) {
                                                    var v = el.getAttribute('wire:id') || el.getAttribute('wire\:id');
                                                    if (v) return v;
                                                }
                                                el = el.parentElement;
                                            }
                                            return null;
                                        }

                                        var lwId = findClosestLivewireId(editorEl);
                                        if (lwId && window.Livewire && Livewire.find) {
                                            try {
                                                Livewire.find(lwId).set('long_description', html);
                                            } catch (e) {
                                                Livewire.emit('longDescriptionUpdated', html);
                                            }
                                        } else {
                                            Livewire && Livewire.emit && Livewire.emit('longDescriptionUpdated', html);
                                        }
                                    });

                                    // Keep Quill in sync after Livewire updates
                                    if (window.Livewire && Livewire.hook) {
                                        Livewire.hook('message.processed', function() {
                                            var current = quill.root.innerHTML;
                                            var fromServer = hiddenInput.value || '';
                                            if (current !== fromServer) {
                                                quill.root.innerHTML = fromServer;
                                            }
                                        });
                                    }
                                }

                                // load CSS + JS and initialize
                                loadCssOnce(QUILL_CSS);
                                loadScript(QUILL_JS, function() {
                                    if (window.Livewire) {
                                        document.addEventListener('livewire:load', initQuill);
                                        initQuill();
                                    } else {
                                        initQuill();
                                    }
                                });
                            })();
                        </script>
                    </div>

                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-3">
                        <label class="form-label">Buying Price</label>
                        <input type="number" wire:model="buying_price" wire:change="calculateSalePrice" class="form-control" placeholder="0.00">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Price to show <span class="text-danger">*</span></label>
                        <input type="number" wire:model.live="price" wire:change="calculateSalePrice" class="form-control" placeholder="0.00">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>



                    <div class="col-md-2">
                        <label class="form-label">Discount Type</label>
                        <select wire:model.live="discount_type" class="form-select">
                            <option value="">None</option>
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage</option>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['discount_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Discount Price</label>
                        <input type="number" wire:model.live="discount_price" wire:input="calculateSalePrice" class="form-control" placeholder="0.00">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['discount_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Sale Price</label>
                        <div class="form-control bg-success text-white"><?php echo e($this->sale_price); ?></div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Product Images Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 mt-1 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Thumbnail Image <span class="text-danger">*</span></label>
                        <input type="file" wire:model="thumbnail" class="form-control">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['thumbnail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <!-- Progress Bar -->
                        <div wire:loading wire:target="thumbnail" class="mt-2">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar" style="width: 0%" x-data="{ progress: 0 }"
                                    x-on:livewire-upload-start="progress = 5"
                                    x-on:livewire-upload-progress.window="progress = $event.detail.progress"
                                    x-on:livewire-upload-finish="progress = 100" x-on:livewire-upload-error="progress = 0"
                                    :style="`width: ${progress}%`">
                                    <span x-text="progress + '%'"></span>
                                </div>
                            </div>
                            <span class="mt-1 d-block">Uploading images...</span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thumbnail): ?>
                        <div class="mt-2">
                            <img src="<?php echo e($thumbnail->temporaryUrl()); ?>" class="img-thumbnail" width="120">
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Video URL</label>
                        <input type="text" wire:model="video" class="form-control"
                            placeholder="YouTube or embed link">
                    </div>
                </div>


                <div class="mb-3">
                    <label class="form-label">Upload Images (Multiple)</label>
                    <input type="file" multiple wire:model="images" class="form-control" accept="image/*">
                </div>

                <div wire:loading wire:target="images" class="mt-2">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                            role="progressbar"
                            x-data="{ progress: 0 }"
                            x-on:livewire-upload-start="progress = 5"
                            x-on:livewire-upload-progress.window="progress = $event.detail.progress"
                            x-on:livewire-upload-finish="progress = 100"
                            x-on:livewire-upload-error="progress = 0"
                            :style="`width: ${progress}%`">
                            <span x-text="progress + '%'"></span>
                        </div>
                    </div>
                    <span class="mt-1 d-block">Uploading images...</span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($uploadedImages) > 0): ?>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $uploadedImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_object($img) && method_exists($img, 'temporaryUrl')): ?>
                    <div class="position-relative" style="width: 100px; height: 100px;">
                        <img src="<?php echo e($img->temporaryUrl()); ?>"
                            class="img-thumbnail h-100 w-100 object-fit-cover">
                        <button type="button"
                            wire:click="removeImage(<?php echo e($index); ?>)"
                            class="btn btn-danger btn-sm position-absolute top-0 end-0"
                            style="padding: 2px 6px; font-size: 10px;">
                            &times;
                        </button>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>



        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantSystemEnabled): ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Product Variations</h5>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$showVariantForm): ?>
                    <button type="button" wire:click="openVariantForm" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Variation
                    </button>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php echo $__env->make('livewire.admin.products.partials.variant-generator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($variants) > 0): ?>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">SKU</th>
                                <th width="20%">Attributes</th>
                                <th width="12%">Varient Price</th>
                                <th width="10%">Stock</th>
                                <th width="16%">Image</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <input type="text" wire:model="variants.<?php echo e($index); ?>.sku"
                                        class="form-control form-control-sm" style="min-width:110px;">
                                    <div class="form-check mt-1">
                                        <input type="checkbox" class="form-check-input"
                                            wire:model="variants.<?php echo e($index); ?>.is_active"
                                            id="vact<?php echo e($index); ?>">
                                        <label class="form-check-label small" for="vact<?php echo e($index); ?>">Active</label>
                                    </div>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variant['values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $attr = \App\Models\Attribute::find($value['attribute_id']);
                                    $val = \App\Models\AttributeValue::find(
                                    $value['attribute_value_id'],
                                    );
                                    ?>
                                    <span class="badge bg-light text-dark me-1 mb-1">
                                        <?php echo e($attr->name); ?>: <?php echo e($val->value); ?>

                                    </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                        wire:model="variants.<?php echo e($index); ?>.variant_price"
                                        class="form-control form-control-sm" style="min-width:90px;"
                                        placeholder="auto">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($variant['variant_price'] ?? null) === null || ($variant['variant_price'] ?? null) === ''): ?>
                                    <small class="text-muted">auto: ৳<?php echo e(number_format($sale_price ?? $price ?? 0, 2)); ?></small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <input type="number" step="1" min="0"
                                        wire:model="variants.<?php echo e($index); ?>.stock"
                                        class="form-control form-control-sm" style="min-width:70px;">
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($variant['image']) || !empty($variant['new_image_file'])): ?>
                                    <div class="d-flex align-items-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($variant['new_image_file'])): ?>
                                        <img src="<?php echo e($variant['new_image_file']->temporaryUrl()); ?>"
                                            class="img-thumbnail me-2" width="50"
                                            height="50">
                                        <?php elseif(!empty($variant['image'])): ?>
                                        <img src="<?php echo e(asset('storage/' . $variant['image'])); ?>"
                                            class="img-thumbnail me-2" width="50"
                                            height="50">
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php else: ?>
                                    <span class="text-muted small">No image</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <button type="button"
                                        wire:click="removeVariant(<?php echo e($index); ?>)"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to remove this variant?')">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                    <button type="button" wire:click="toggleSlabs(<?php echo e($index); ?>)"
                                        class="btn btn-outline-secondary btn-sm mt-1" title="Wholesale slabs">
                                        Slabs (<?php echo e(count($variantSlabs[$variant['id'] ?? 'idx_'.$index] ?? [])); ?>)
                                    </button>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slabOpen === (string) ($variant['id'] ?? 'idx_'.$index)): ?>
                            <tr>
                                <td colspan="6" class="bg-light">
                                    <small class="fw-bold">Wholesale slabs — <?php echo e($variant['sku']); ?></small>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variantSlabs[$variant['id'] ?? 'idx_'.$index] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $si => $slab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="d-flex gap-2 align-items-center mt-1">
                                        <span class="small">min qty</span>
                                        <input type="number" min="2" wire:model="variantSlabs.<?php echo e($variant['id'] ?? 'idx_'.$index); ?>.<?php echo e($si); ?>.min_qty"
                                            class="form-control form-control-sm" style="max-width:90px;">
                                        <span class="small">৳</span>
                                        <input type="number" step="0.01" min="0" wire:model="variantSlabs.<?php echo e($variant['id'] ?? 'idx_'.$index); ?>.<?php echo e($si); ?>.price"
                                            class="form-control form-control-sm" style="max-width:110px;">
                                        <button type="button" wire:click="removeSlabRow(<?php echo e($index); ?>, <?php echo e($si); ?>)"
                                            class="btn btn-sm btn-link text-danger"><i class="fas fa-times"></i></button>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <button type="button" wire:click="addSlabRow(<?php echo e($index); ?>)"
                                        class="btn btn-sm btn-outline-primary mt-1">+ Slab</button>
                                </td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No variations added yet. Click "Add Variation" to create product variants.
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showVariantForm): ?>
                <div class="border rounded p-3 mt-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Create New Variation</h6>
                        <button type="button" wire:click="cancelVariantForm"
                            class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                    </div>

                    <!-- Error Messages -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($variantErrors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variantErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Attributes</label>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $productAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($attribute->id, $selectedAttributes)): ?>
                            <button type="button"
                                wire:click="addAttributeToVariant(<?php echo e($attribute->id); ?>)"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> <?php echo e($attribute->name); ?>

                            </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedAttributes) > 0): ?>
                        <div class="row g-3 mb-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $selectedAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $attrId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $attribute = $productAttributes->firstWhere('id', $attrId); ?>
                            <div class="col-md-4">
                                <div class="border rounded p-2 h-100">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-primary"><?php echo e($attribute->name); ?></strong>
                                        <button type="button"
                                            wire:click="removeAttributeFromVariant(<?php echo e($idx); ?>)"
                                            class="btn btn-sm btn-link text-danger p-0">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <select wire:model="newVariant.attributes.<?php echo e($attrId); ?>"
                                        class="form-select form-select-sm <?php $__errorArgs = ['newVariant.attributes.' . $attrId];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Select Value</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attribute->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value->id); ?>"><?php echo e($value->value); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" wire:model="newVariant.sku"
                                    class="form-control form-control-sm <?php if(isset($variantErrors['sku'])): ?> is-invalid <?php endif; ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($variantErrors['sku'])): ?>
                                <div class="invalid-feedback"><?php echo e($variantErrors['sku']); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Varient Price<span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"></span>
                                    <input type="number" step="0.01"
                                        wire:model="newVariant.variant_price"
                                        class="form-control <?php if(isset($variantErrors['variant_price'])): ?> is-invalid <?php endif; ?>">
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($variantErrors['variant_price'])): ?>
                                <div class="invalid-feedback"><?php echo e($variantErrors['variant_price']); ?>

                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Stock: <span class="text-danger">*</span></label>
                                <input type="number" wire:model="newVariant.stock"
                                    class="form-control form-control-sm <?php if(isset($variantErrors['stock'])): ?> is-invalid <?php endif; ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($variantErrors['stock'])): ?>
                                <div class="invalid-feedback"><?php echo e($variantErrors['stock']); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Variant Image</label>
                                <input type="file" wire:model="newVariantImage"
                                    class="form-control form-control-sm">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newVariantImage): ?>
                                <div class="mt-2">
                                    <img src="<?php echo e($newVariantImage->temporaryUrl()); ?>"
                                        class="img-thumbnail" width="80" height="80">
                                    <small class="d-block text-muted mt-1">Preview of selected
                                        image</small>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                        </div>


                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" wire:click="cancelVariantForm"
                                class="btn btn-secondary btn-sm">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="button" wire:click="addVariant"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-check me-1"></i> Create Variation
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Please select at least one attribute to create a variation.
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo $__env->make('livewire.admin.products.partials.swatch-image-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('livewire.admin.products.partials.image-options-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Stock <span class="text-danger">*</span></label>
                        <input type="number" wire:model="stock" class="form-control" placeholder="0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">Is Featured </label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="isFeaturedSwitch"
                                wire:model="is_featured">
                            <label class="form-check-label" for="isFeaturedSwitch">
                                <?php echo e($is_featured ? 'Featured' : 'Not Featured'); ?>

                            </label>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['is_featured'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">Status <span class="text-danger">*</span></label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch"
                                wire:model="status">
                            <label class="form-check-label" for="statusSwitch">
                                <?php echo e($status ? 'Active' : 'Inactive'); ?>

                            </label>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-semibold text-primary mb-3">🚀 Pre-Order Settings</h6>

                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="isPreorderSwitch"
                        wire:model.live="is_preorder">
                    <label class="form-check-label" for="isPreorderSwitch">
                        <?php echo e($is_preorder ? 'Pre-Order Enabled' : 'Pre-Order Disabled'); ?>

                    </label>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_preorder): ?>
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">
                            Pre-Order Start Date <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local"
                            wire:model="preorder_start_date"
                            class="form-control <?php $__errorArgs = ['preorder_start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['preorder_start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pre-Order End Date</label>
                        <input type="datetime-local"
                            wire:model="preorder_end_date"
                            class="form-control <?php $__errorArgs = ['preorder_end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['preorder_end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="form-text">Leave empty for no end date</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Expected Release Date</label>
                        <input type="datetime-local"
                            wire:model="expected_release_date"
                            class="form-control <?php $__errorArgs = ['expected_release_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['expected_release_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="form-text">Shown to customers as estimated ship date</div>
                    </div>

                </div>

                <div class="alert alert-info d-flex align-items-start gap-2 mt-3 mb-0 py-2 px-3">
                    <i class="bx bx-info-circle fs-5 flex-shrink-0 mt-1"></i>
                    <div class="small">
                        This product will appear in the <strong>Pre-Order</strong> section.
                        Customers can reserve it before the official release date.
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Meta Title</label>
                        <div class="">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-title"></i></span>
                                <input type="text" class="form-control" wire:model="meta_title" placeholder="SEO Title">
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['meta_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>


                    <div class="col-md-4" x-data="{ desc: <?php if ((object) ('meta_description') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('meta_description'->value()); ?>')<?php echo e('meta_description'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('meta_description'); ?>')<?php endif; ?>.live, limit: 160 }">
                        <label class="form-label">Meta Description</label>
                        <div class="">
                            <textarea class="form-control" wire:model="meta_description" rows="3" placeholder="SEO Description"></textarea>
                            <div class="form-text d-flex justify-content-between">
                                <span>Recommended: 155-160 characters</span>
                                <span :class="desc.length > limit ? 'text-danger fw-bold' : (desc.length > 150 ? 'text-warning' : 'text-muted')">
                                    <span x-text="desc ? desc.length : 0"></span> / <span x-text="limit"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Extra Meta Tags / Custom HTML</label>
                        <div class="">
                            <textarea class="form-control" wire:model="meta_tags" rows="2" placeholder='<meta name="robots" content="noindex">'></textarea>
                            <small class="text-muted">Advanced: Use this to inject specific tags for this products only.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php echo \App\Support\Hooks::renderSlot('admin.product.form.sections', ['productId' => null, 'mode' => 'create']); ?>

        <!-- Submit Button -->
        <div class="text-end">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Save Product
            </button>
        </div>
    </form>
</div><?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\products\product-create.blade.php ENDPATH**/ ?>