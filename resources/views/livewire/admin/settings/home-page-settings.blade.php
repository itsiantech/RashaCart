<div>
    <div class="content-header mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Home Page Settings</h4>
                <p class="text-muted mb-0">Manage categories that appear on the homepage</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Homepage Sections</h5>
                    <small class="text-muted">Show / hide these sections and change their titles</small>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveSectionSettings">
                        <div class="table-responsive">
                            <table class="table align-middle mb-3">
                                <thead>
                                    <tr>
                                        <th style="width: 90px;">Show</th>
                                        <th style="width: 220px;">Section</th>
                                        <th>Section Title</th>
                                        <th style="width: 160px;">Show Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       id="switchShopByCategory"
                                                       wire:model="shop_by_category_enabled">
                                            </div>
                                        </td>
                                        <td>
                                            <label for="switchShopByCategory" class="mb-0 fw-semibold">Shop by Category</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" maxlength="100"
                                                   wire:model="shop_by_category_title"
                                                   placeholder="Shop by Category">
                                            @error('shop_by_category_title')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="2" max="12"
                                                   style="max-width: 110px;"
                                                   wire:model="shop_by_category_limit">
                                            @error('shop_by_category_limit')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       id="switchTopBrands"
                                                       wire:model="top_brands_enabled">
                                            </div>
                                        </td>
                                        <td>
                                            <label for="switchTopBrands" class="mb-0 fw-semibold">Top Brands</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" maxlength="100"
                                                   wire:model="top_brands_title"
                                                   placeholder="Top Brands">
                                            @error('top_brands_title')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="2" max="12"
                                                   style="max-width: 110px;"
                                                   wire:model="top_brands_limit">
                                            @error('top_brands_limit')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       id="switchFlashSale"
                                                       wire:model="flash_sale_enabled">
                                            </div>
                                        </td>
                                        <td>
                                            <label for="switchFlashSale" class="mb-0 fw-semibold">Flash Sale</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" maxlength="100"
                                                   wire:model="flash_sale_title"
                                                   placeholder="Flash Sale">
                                            @error('flash_sale_title')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="1" max="12"
                                                   style="max-width: 110px;"
                                                   wire:model="flash_sale_limit">
                                            @error('flash_sale_limit')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i> Save Section Settings
                            </button>
                            <small class="text-muted">Flash Sale section only appears when a campaign is live.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add Category to Homepage</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="addCategory">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Category</label>
                                <select class="form-select" wire:model.live="selected_category_id" required>
                                    <option value="">-- Choose a Category --</option>
                                    @foreach($allCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('selected_category_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Product Limit</label>
                                <input type="number" class="form-control" wire:model.live="product_limit" min="1" max="50" required>
                                @error('product_limit')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bx bx-plus me-1"></i> Add
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recommended For You Section</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveRecommendedSettings">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Section Title</label>
                                <input type="text" class="form-control" wire:model.live="recommended_title" placeholder="Recommended For You" required>
                                @error('recommended_title')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Product Limit</label>
                                <input type="number" class="form-control" wire:model.live="recommended_product_limit" min="1" max="100" required>
                                @error('recommended_product_limit')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bx bx-save me-1"></i> Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Homepage Categories</h5>
                </div>
                <div class="card-body">
                    @if($homepageCategories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Order</th>
                                        <th>Category</th>
                                        <th style="width: 120px;">Product Limit</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($homepageCategories as $item)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $item->sort_order }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->category->image)
                                                        <img src="{{ asset('storage/' . $item->category->image) }}"
                                                             alt="{{ $item->category->name }}"
                                                             class="rounded me-2"
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px;">
                                                            <i class="bx bx-category-alt text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold">{{ $item->category->name }}</div>
                                                        <small class="text-muted">{{ $item->category->slug }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number"
                                                       class="form-control form-control-sm"
                                                       value="{{ $item->product_limit }}"
                                                       wire:change="updateProductLimit({{ $item->id }}, $event.target.value)"
                                                       min="1" max="50">
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           wire:change="toggleActive({{ $item->id }})"
                                                           {{ $item->is_active ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button wire:click="moveUp({{ $item->id }})"
                                                            class="btn btn-outline-primary"
                                                            title="Move Up">
                                                        <i class="bx bx-chevron-up"></i>
                                                    </button>
                                                    <button wire:click="moveDown({{ $item->id }})"
                                                            class="btn btn-outline-primary"
                                                            title="Move Down">
                                                        <i class="bx bx-chevron-down"></i>
                                                    </button>
                                                    <button wire:click="delete({{ $item->id }})"
                                                            class="btn btn-outline-danger"
                                                            title="Remove">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-category-alt display-4 text-muted"></i>
                            <p class="text-muted mt-3">No categories added to homepage yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
