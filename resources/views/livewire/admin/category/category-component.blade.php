<div>
    <div class="col-xxl">

        <!-- Category Form -->
        @if($showForm)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ $isEdit ? 'Edit Category' : 'Add New Category' }}</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveCategory">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="name">Category Name</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-category"></i></span>
                                <input type="text" class="form-control" id="name" wire:model="name" placeholder="Enter category name">
                            </div>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="slug">Slug</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="slug" wire:model="slug" placeholder="Auto-generated slug" readonly>
                            @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="parent_id">Parent Category</label>
                        <div class="col-sm-10">
                            <select class="form-select" id="parent_id" wire:model="parent_id">
                                <option value="">Select parent category (optional)</option>
                                @foreach($mainCategories as $category)
                                @if($isEdit && $category->id == $category_id)
                                @continue
                                @endif
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="image">Category Image</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" id="image" wire:model="image">
                            @error('image') <span class="text-danger">{{ $message }}</span> @enderror

                            @if($isEdit && !$image && $category_id)
                            @php $category = \App\Models\Category::find($category_id); @endphp
                            @if($category->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$category->image) }}" alt="Current image" style="max-height: 100px;">
                                <button type="button" class="btn btn-sm btn-danger ms-2" wire:click="$set('image', 'delete')">
                                    Remove Image
                                </button>
                            </div>
                            @endif
                            @endif

                            @if($image && !is_string($image))
                            <div class="mt-2">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview" style="max-height: 100px;">
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="status"
                                    wire:model="status"
                                    @if($status) checked @endif>
                                <label class="form-check-label" for="status">
                                    {{ $status ? 'Active' : 'Inactive' }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Show Homepage?</label>
                        <div class="col-sm-10">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="is_featured"
                                    wire:model.live="is_featured"
                                    @if($is_featured) checked @endif>
                                <label class="form-check-label" for="is_featured">
                                    {{ $is_featured ? 'Yes' : 'No' }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary me-2">
                                {{ $isEdit ? 'Update' : 'Save' }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary" wire:click="$set('showForm', false)">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @else


        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <!-- Title Column -->
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h5 class="mb-0">Categories & Sub-Category List</h5>
                    </div>

                    <!-- Search and Add Button Column -->
                    <div class="col-md-6 col-12">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <!-- Search Input -->
                            <div class="flex-grow-1" style="min-width: 150px;">
                                <input
                                    type="text"
                                    class="form-control"
                                    wire:model.live="search"
                                    placeholder="Search categories...">
                            </div>
                            <!-- Add New Button -->
                            <!-- Add New Button -->
                            <div class="flex-shrink-0">
                                <button
                                    class="btn btn-primary d-flex align-items-center"
                                    wire:click="showAddForm"
                                    style="white-space: nowrap;">
                                    <i class="bx bx-plus me-1"></i>
                                    <span>Add New</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if($categories->isEmpty())

                        <tr>
                            <td colspan="5" class="text-center">No categories found.</td>
                        </tr>
                        @else
                        @foreach($categories as $category)
                        <!-- Main Category -->
                        <tr>
                            <td>
                                <i class="bx bx-category me-3"></i>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>{{ $category->slug }}</td>
                            <td>
                                @if($category->image)
                                <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" style="max-height: 50px;">
                                @else
                                <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td>
                                @if($category->status)
                                <span class="badge bg-label-success">Active</span>
                                @else
                                <span class="badge bg-label-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary me-1" wire:click="showEditForm({{ $category->id }})">
                                    <i class="bx bx-edit"></i>
                                </button>

                                <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $category->id }})">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Subcategories -->
                        @foreach($category->children as $child)
                        <tr>
                            <td>
                                <i class="bx bx-subdirectory-right me-4 ms-3"></i>
                                <i class="bx bx-category-alt me-3"></i>
                                {{ $child->name }}
                            </td>
                            <td>{{ $child->slug }}</td>
                            <td>
                                @if($child->image)
                                <img src="{{ asset('storage/'.$child->image) }}" alt="{{ $child->name }}" style="max-height: 50px;">
                                @else
                                <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td>
                                @if($child->status)
                                <span class="badge bg-label-success">Active</span>
                                @else
                                <span class="badge bg-label-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary me-1" wire:click="showEditForm({{ $child->id }})">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $child->id }})">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @endforeach
                        @endif
                    </tbody>

                </table>

            </div>
        </div>

        @endif
    </div>


</div>