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
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" wire:model.live="name" class="form-control" placeholder="Product name">
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" wire:model="slug" class="form-control" placeholder="product-slug" readonly>
                        @error('slug')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select wire:model="category_id" class="form-select">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Short Description <span class="text-danger">*</span></label>
                        <textarea wire:model="short_description" class="form-control" rows="3" placeholder="Brief description"></textarea>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Long Description</label>
                        <textarea wire:model="long_description" class="form-control" rows="3" placeholder="Detailed description"></textarea>
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
                        <div class="mt-2">
                            @if ($thumbnail)
                                <img src="{{ $thumbnail->temporaryUrl() }}" width="120" class="img-thumbnail">
                            @else
                                <img src="{{ asset('storage/' . optional(App\Models\Product::find($productId))->thumbnail) }}" width="120" class="img-thumbnail">
                            @endif
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
                        @if (count($images) > 0)
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach ($images as $img)
                                    <img src="{{ $img->temporaryUrl() }}" width="100" class="img-thumbnail">
                                @endforeach
                            </div>
                        @endif
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

                    @if (count($variants) > 0)
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">SKU</th>
                                        <th width="20%">Attributes</th>
                                        <th width="12%">Variant Price</th>
                                        <th width="10%">Final Price</th>
                                        <th width="16%">Image</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($variants as $index => $variant)
                                        <tr>
                                            <td><strong>{{ $variant['sku'] }}</strong></td>
                                            <td>
                                                @foreach ($variant['values'] as $value)
                                                    @php
                                                        $attr = \App\Models\Attribute::find($value['attribute_id']);
                                                        $val = \App\Models\AttributeValue::find($value['attribute_value_id']);
                                                    @endphp
                                                    <span class="badge bg-light text-dark me-1 mb-1">
                                                        {{ $attr->name }}: {{ $val->value }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($variant['varient_price'] > 0)
                                                    <span class="text-success">+৳{{ number_format($variant['varient_price'], 2) }}</span>
                                                @elseif($variant['varient_price'] < 0)
                                                    <span class="text-danger">-৳{{ number_format(abs($variant['varient_price']), 2) }}</span>
                                                @else
                                                    <span class="text-muted">৳0.00</span>
                                                @endif
                                            </td>
                                            <td><strong>৳{{ number_format($price + $variant['varient_price'], 2) }}</strong></td>
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
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" wire:click="removeVariant({{ $index }})" class="btn btn-danger btn-sm" title="Remove">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
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
                                        <div class="col-md-4">
                                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                                            <input type="text" wire:model="newVariant.sku" class="form-control form-control-sm @if(isset($variantErrors['sku'])) is-invalid @endif">
                                            @if (isset($variantErrors['sku']))
                                                <div class="invalid-feedback">{{ $variantErrors['sku'] }}</div>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Variant Price<span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">৳</span>
                                                <input type="number" step="0.01" wire:model="newVariant.varient_price" class="form-control @if(isset($variantErrors['varient_price'])) is-invalid @endif">
                                            </div>
                                            @if (isset($variantErrors['varient_price']))
                                                <div class="invalid-feedback">{{ $variantErrors['varient_price'] }}</div>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
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
            
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update Product
            </button>
        </div>
    </form>
</div>