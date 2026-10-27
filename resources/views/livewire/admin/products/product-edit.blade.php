<div>
    <form wire:submit.prevent="update">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Edit Product</h5>
                    <div>
                        <a wire:navigate href="{{ route('admin.product.stock.manage', $productId) }}" class="btn btn-sm btn-info me-2">
                            <i class="fas fa-boxes"></i> Manage Stock
                        </a>
                        <a wire:navigate href="{{ route('admin.product.index') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-left-arrow-alt"></i> Back
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" wire:model.live="name" class="form-control" placeholder="Product name">
                        @error('name')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" wire:model="slug" class="form-control" placeholder="product-slug" readonly>
                        @error('slug')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select wire:model="category_id" class="form-select">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->depth ? str_repeat('— ', $category->depth) : '' }}{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select wire:model="brand_id" class="form-select">
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            Additional Categories <small class="text-muted">(optional — একাধিক টিক দেওয়া যাবে)</small>
                        </label>
                        <div class="border rounded p-3" style="max-height: 240px; overflow-y: auto;">
                            @foreach ($categories as $category)
                            <div class="form-check" style="margin-left: {{ $category->depth * 20 }}px;">
                                <input class="form-check-input" type="checkbox"
                                       id="additional-category-{{ $category->id }}"
                                       value="{{ $category->id }}"
                                       wire:model="additional_category_ids">
                                <label class="form-check-label" for="additional-category-{{ $category->id }}">
                                    {{ $category->name }}
                                    @if ((int) $category->id === (int) $category_id)
                                    <span class="badge bg-secondary">Primary</span>
                                    @endif
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">উপরে select করা Primary category ছাড়া যেসব category-তে product দেখাতে চান, সেগুলো টিক দিন।</small>
                        @error('additional_category_ids.*')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Short Description <small class="text-muted">(optional)</small></label>
                        <textarea wire:model="short_description" class="form-control" rows="3" placeholder="Brief description"></textarea>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Long Description</label>

                        <div wire:ignore class="lx-desc-editor">
                            <div id="quill-editor">{!! $long_description !!}</div>
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
                        @error('long_description')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror

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

                                    if (editorEl.__quill) return;

                                    // Capture original content BEFORE Quill transforms the DOM
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

                                    // Always read clean content from quill.root, never from editorEl
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
                                    // initialize after Livewire is ready
                                    if (window.Livewire) {
                                        document.addEventListener('livewire:load', initQuill);
                                        // also init in case Livewire already loaded
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
                        <input type="number" wire:model="buying_price" class="form-control" placeholder="0.00">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" wire:model="price" wire:change="calculateSalePrice" class="form-control" placeholder="0.00">
                        @error('price')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Discount Type</label>
                        <select wire:model.live="discount_type" class="form-select">
                            <option value="">None</option>
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage</option>
                        </select>
                        @error('discount_type')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Discount Price</label>
                        <input type="number" wire:model.live="discount_price" wire:input="calculateSalePrice" class="form-control" placeholder="0.00">
                        @error('discount_price')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Sale Price</label>
                        <div class="form-control bg-success text-white">{{ number_format($sale_price, 2) }}</div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label>Change Thumbnail</label>
                        <input type="file" wire:model="thumbnail" class="form-control">
                        @error('thumbnail')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        <div class="mt-2">
                            @if ($thumbnail)
                            <img src="{{ $thumbnail->temporaryUrl() }}" width="120" class="img-thumbnail">
                            @else
                            <img src="{{ asset('storage/' . optional(App\Models\Product::find($productId))->thumbnail) }}" width="120" class="img-thumbnail">
                            @endif
                        </div>
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
                    </div>
                    <div class="col-md-6">
                        <label>Video URL</label>
                        <input type="text" wire:model="video" class="form-control" placeholder="YouTube video URL">
                    </div>

                    <!-- Images Section -->
                    <div class="mt-3">
                        <label>Current Gallery Image</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($existingImages as $index => $img)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $img) }}" width="100" class="img-thumbnail">
                                <button type="button" wire:click="deleteImage({{ $index }})" class="btn btn-danger btn-sm position-absolute top-0 end-0">
                                    &times;
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-3">
                        <label>Add More Images</label>
                        <input type="file" multiple wire:model="images" class="form-control">
                        @error('images')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        @if (count($images) > 0)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach ($images as $img)
                            <img src="{{ $img->temporaryUrl() }}" width="100" class="img-thumbnail">
                            @endforeach
                        </div>
                        @endif

                        <div wire:loading wire:target="images" class="mt-2">
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Variants Section -->
        @if ($variantSystemEnabled)
        <div class="card mb-4 mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Product Variations</h5>
                    @if (!$showVariantForm)
                    <button type="button" wire:click="$set('showVariantForm', true)" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Variation
                    </button>
                    @endif
                </div>

                @include('livewire.admin.products.partials.variant-generator')

                @if (count($variants) > 0)
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">SKU</th>
                                <th width="10%">Attributes</th>
                                <th width="10%">Stock</th>
                                <th width="12%">Variant Price</th>
                                <th width="16%">Image</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($variants as $index => $variant)
                            <tr>
                                <td>
                                    <input type="text" wire:model="variants.{{ $index }}.sku"
                                        class="form-control form-control-sm" style="min-width:110px;">
                                    <div class="form-check mt-1">
                                        <input type="checkbox" class="form-check-input"
                                            wire:model="variants.{{ $index }}.is_active"
                                            id="veact{{ $index }}">
                                        <label class="form-check-label small" for="veact{{ $index }}">Active</label>
                                    </div>
                                </td>
                                <td>
                                    @foreach ($variant['values'] as $value)
                                    @php
                                    $attr = \App\Models\Attribute::find($value['attribute_id']);
                                    $val = \App\Models\AttributeValue::find($value['attribute_value_id']);
                                    @endphp
                                    @if ($attr && $val)
                                    <span class="badge bg-light text-dark me-1 mb-1">
                                        {{ $attr->name }}: {{ $val->value }}
                                    </span>
                                    @endif
                                    @endforeach
                                </td>
                                <td>
                                    <input type="number" step="1" min="0"
                                        wire:model="variants.{{ $index }}.stock"
                                        class="form-control form-control-sm" style="min-width:70px;">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                        wire:model="variants.{{ $index }}.variant_price"
                                        class="form-control form-control-sm" style="min-width:90px;"
                                        placeholder="auto">
                                    @if (($variant['variant_price'] ?? null) === null || ($variant['variant_price'] ?? null) === '')
                                    <small class="text-muted">auto: ৳{{ number_format($sale_price ?? $price ?? 0, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($variant['image']) || !empty($variant['new_image_file']))
                                    <div class="d-flex align-items-center">
                                        @if (!empty($variant['new_image_file']))
                                        <img src="{{ $variant['new_image_file']->temporaryUrl() }}" class="img-thumbnail me-2" width="50" height="50">
                                        @elseif(!empty($variant['image']))
                                        <img src="{{ asset('storage/' . $variant['image']) }}" class="img-thumbnail me-2" width="50" height="50">
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-muted small">No image</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if(isset($variant['_delete']) && $variant['_delete'])
                                        <span class="badge bg-danger">Marked for deletion</span>
                                        <button type="button" wire:click="removeVariant({{ $index }})" class="btn btn-success btn-sm" title="Restore">
                                            <i class="fas fa-undo"></i>Restore
                                        </button>
                                        @else
                                        <button type="button" wire:click="removeVariant({{ $index }})" class="btn btn-danger btn-sm" title="Remove">
                                            <i class="fas fa-trash"></i>Remove
                                        </button>
                                        <button type="button" wire:click="toggleSlabs({{ $index }})" class="btn btn-outline-secondary btn-sm" title="Wholesale slabs">
                                            Slabs ({{ count($variantSlabs[$variant['id'] ?? 'idx_'.$index] ?? []) }})
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @if ($slabOpen === (string) ($variant['id'] ?? 'idx_'.$index))
                            <tr>
                                <td colspan="6" class="bg-light">
                                    <small class="fw-bold">Wholesale slabs — {{ $variant['sku'] }}</small>
                                    @foreach ($variantSlabs[$variant['id'] ?? 'idx_'.$index] ?? [] as $si => $slab)
                                    <div class="d-flex gap-2 align-items-center mt-1">
                                        <span class="small">min qty</span>
                                        <input type="number" min="2" wire:model="variantSlabs.{{ $variant['id'] ?? 'idx_'.$index }}.{{ $si }}.min_qty"
                                            class="form-control form-control-sm" style="max-width:90px;">
                                        <span class="small">৳</span>
                                        <input type="number" step="0.01" min="0" wire:model="variantSlabs.{{ $variant['id'] ?? 'idx_'.$index }}.{{ $si }}.price"
                                            class="form-control form-control-sm" style="max-width:110px;">
                                        <button type="button" wire:click="removeSlabRow({{ $index }}, {{ $si }})"
                                            class="btn btn-sm btn-link text-danger"><i class="fas fa-times"></i></button>
                                    </div>
                                    @endforeach
                                    <button type="button" wire:click="addSlabRow({{ $index }})"
                                        class="btn btn-sm btn-outline-primary mt-1">+ Slab</button>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No variations added yet. Click "Add Variation" to create product variants.
                </div>
                @endif

                @if ($showVariantForm)
                <div class="border rounded p-3 mt-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Create New Variation</h6>
                        <button type="button" wire:click="cancelVariantForm" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                    </div>

                    <!-- Error Messages -->
                    @if (!empty($variantErrors))
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($variantErrors as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Attributes</label>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($productAttributes as $attribute)
                            @if (!in_array($attribute->id, $selectedAttributes))
                            <button type="button" wire:click="addAttributeToVariant({{ $attribute->id }})" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> {{ $attribute->name }}
                            </button>
                            @endif
                            @endforeach
                        </div>

                        @if (count($selectedAttributes) > 0)
                        <div class="row g-3 mb-3">
                            @foreach ($selectedAttributes as $idx => $attrId)
                            @php $attribute = $productAttributes->firstWhere('id', $attrId); @endphp
                            <div class="col-md-4">
                                <div class="border rounded p-2 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-primary">{{ $attribute->name }}</strong>
                                        <button type="button" wire:click="removeAttributeFromVariant({{ $idx }})" class="btn btn-sm btn-link text-danger p-0">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <select wire:model="newVariant.attributes.{{ $attrId }}" class="form-select form-select-sm">
                                        <option value="">Select Value</option>
                                        @foreach ($attribute->values as $value)
                                        <option value="{{ $value->id }}">{{ $value->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" wire:model="newVariant.sku" class="form-control form-control-sm @if(isset($variantErrors['sku'])) is-invalid @endif">
                                @if (isset($variantErrors['sku']))
                                <div class="invalid-feedback">{{ $variantErrors['sku'] }}</div>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Stock: <span class="text-danger">*</span></label>
                                <input type="number" wire:model="newVariant.stock"
                                    class="form-control form-control-sm @if (isset($variantErrors['stock'])) is-invalid @endif">
                                @if (isset($variantErrors['stock']))
                                <div class="invalid-feedback">{{ $variantErrors['stock'] }}</div>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Variant Price<span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" wire:model="newVariant.variant_price" class="form-control @if(isset($variantErrors['variant_price'])) is-invalid @endif">
                                </div>
                                @if (isset($variantErrors['variant_price']))
                                <div class="invalid-feedback">{{ $variantErrors['variant_price'] }}</div>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Variant Image</label>
                                <input type="file" wire:model="newVariantImage" class="form-control form-control-sm">
                                @if ($newVariantImage)
                                <div class="mt-2">
                                    <img src="{{ $newVariantImage->temporaryUrl() }}" class="img-thumbnail" width="80" height="80">
                                    <small class="d-block text-muted mt-1">Preview of selected image</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" wire:click="cancelVariantForm" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="button" wire:click="addVariant" class="btn btn-success btn-sm">
                                <i class="fas fa-check me-1"></i> Create Variation
                            </button>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Please select at least one attribute to create a variation.
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        @include('livewire.admin.products.partials.swatch-image-manager')

        @include('livewire.admin.products.partials.image-options-manager')

        <!-- Status & Featured Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label d-block">Is Featured</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="isFeaturedSwitch" wire:model="is_featured">
                            <label class="form-check-label" for="isFeaturedSwitch">
                                {{ $is_featured ? 'Featured' : 'Not Featured' }}
                            </label>
                        </div>
                        @error('is_featured')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">Status <span class="text-danger">*</span></label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" wire:model="status">
                            <label class="form-check-label" for="statusSwitch">
                                {{ $status ? 'Active' : 'Inactive' }}
                            </label>
                        </div>
                        @error('status')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Pre-Order Settings --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-semibold text-primary mb-3">🚀 Pre-Order Settings</h6>

                {{-- Toggle --}}
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="isPreorderSwitch"
                        wire:model.live="is_preorder">
                    <label class="form-check-label" for="isPreorderSwitch">
                        {{ $is_preorder ? 'Pre-Order Enabled' : 'Pre-Order Disabled' }}
                    </label>
                </div>

                @if($is_preorder)
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">
                            Pre-Order Start Date <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local"
                            wire:model="preorder_start_date"
                            class="form-control @error('preorder_start_date') is-invalid @enderror">
                        @error('preorder_start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pre-Order End Date</label>
                        <input type="datetime-local"
                            wire:model="preorder_end_date"
                            class="form-control @error('preorder_end_date') is-invalid @enderror">
                        @error('preorder_end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave empty for no end date</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Expected Release Date</label>
                        <input type="datetime-local"
                            wire:model="expected_release_date"
                            class="form-control @error('expected_release_date') is-invalid @enderror">
                        @error('expected_release_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Shown to customers as estimated ship date</div>
                    </div>

                </div>

                <div class="alert alert-info d-flex align-items-start gap-2 mt-3 mb-0 py-2 px-3">
                    <i class="bx bx-info-circle fs-5 flex-shrink-0 mt-1"></i>
                    <div class="small">
                        This product is listed in the <strong>Pre-Order</strong> section.
                        Customers can reserve it before the official release date.
                    </div>
                </div>
                @endif

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
                            @error('meta_title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>


                    <div class="col-md-4" x-data="{ desc: @entangle('meta_description').live, limit: 160 }">
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

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update Product
            </button>
        </div>
    </form>
</div>