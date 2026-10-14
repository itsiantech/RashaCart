<div>
    <style>
        .cat-page-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .cat-stat-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f4f5fa;
            border: 1px solid #e7e7f1;
            color: #566a7f;
            border-radius: 30px;
            padding: 4px 12px;
            font-size: .8125rem;
            font-weight: 500;
        }

        .cat-stat-chip i {
            color: #696cff;
        }

        .cat-form-card .form-label {
            font-weight: 500;
            font-size: .8125rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #697a8d;
        }

        .cat-image-preview {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #d9dee3;
        }

        /* ── Tree list ── */
        .cat-drag {
            cursor: grab;
            color: #a1acb8;
            padding: 2px 4px;
            border-radius: 6px;
            line-height: 1;
            transition: all .15s ease;
        }

        .cat-drag:hover {
            color: #696cff;
            background: #eeedff;
        }

        .cat-drag:active {
            cursor: grabbing;
        }

        .cat-sign {
            font-family: SFMono-Regular, Menlo, Consolas, monospace;
            color: #a8b1bd;
            font-size: .8rem;
            white-space: pre;
            user-select: none;
        }

        .cat-thumb {
            width: 38px;
            height: 38px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e7e7f1;
            background: #f4f5fa;
        }

        .cat-thumb-placeholder {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: #f4f5fa;
            border: 1px dashed #d9dee3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #a1acb8;
        }

        .cat-name {
            font-weight: 500;
            color: #384551;
        }

        .cat-depth-1 .cat-name {
            font-weight: 400;
        }

        .cat-depth-2 .cat-name {
            font-weight: 400;
            color: #697a8d;
        }

        .cat-featured {
            color: #ffab00;
            font-size: .8rem;
        }

        .cat-products-badge {
            background: #e7e7ff;
            color: #696cff;
            font-weight: 600;
            border-radius: 30px;
            padding: 3px 10px;
            font-size: .75rem;
        }

        .cat-status-pill {
            border: 0;
            font-size: .75rem;
            font-weight: 600;
            border-radius: 30px;
            padding: 4px 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .cat-status-pill.cat-active {
            background: #e8faf0;
            color: #2e9e63;
        }

        .cat-status-pill.cat-inactive {
            background: #f4f5fa;
            color: #a1acb8;
        }

        .cat-status-pill .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }

        .cat-row {
            transition: background .15s ease;
        }

        .cat-row:hover {
            background: #f9fafc;
        }

        .cat-ghost {
            opacity: .4;
            background: #eeedff;
        }

        .cat-chosen {
            box-shadow: 0 2px 10px rgba(105, 108, 255, .25);
        }

        tbody.cat-sort-group {
            display: table-row-group;
        }

        .cat-drag-hint {
            font-size: .78rem;
            color: #a1acb8;
        }
    </style>

    {{-- ===== Page header ===== --}}
    <div class="cat-page-header">
        <div>
            <h4 class="mb-1">Categories</h4>
            <div class="d-flex flex-wrap gap-2">
                <span class="cat-stat-chip"><i class="bx bx-category"></i> {{ $totalCount }} total</span>
                <span class="cat-stat-chip"><i class="bx bx-check-circle"></i> {{ $activeCount }} active</span>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <input type="text" class="form-control" style="min-width: 200px;" wire:model.live.debounce.300ms="search"
                placeholder="Search categories...">
            <button class="btn btn-primary d-flex align-items-center" wire:click="showAddForm">
                <i class="bx bx-plus me-1"></i> Add New
            </button>
        </div>
    </div>

    {{-- ===== Create / Edit form (same page, collapsible) ===== --}}
    @if($showForm)
    <div class="card cat-form-card mb-4 shadow-sm border-0" id="category-form-card">
        <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-bottom pb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="btn-icon rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width:34px;height:34px;background:#eeedff;color:#696cff;">
                    <i class="bx {{ $isEdit ? 'bx-edit-alt' : 'bx-plus' }}"></i>
                </span>
                <h5 class="mb-0">{{ $isEdit ? 'Edit Category' : 'Add New Category' }}</h5>
            </div>
            <button type="button" class="btn btn-sm btn-label-secondary" wire:click="cancelForm">
                <i class="bx bx-x me-1"></i> Close
            </button>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="saveCategory">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Category Name</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-category"></i></span>
                            <input type="text" class="form-control" id="name" wire:model="name"
                                placeholder="e.g. Woman">
                        </div>
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" class="form-control" id="slug" wire:model="slug" readonly
                            placeholder="Auto-generated from name">
                        @error('slug') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="parent_id">Parent Category</label>
                        <select class="form-select" id="parent_id" wire:model="parent_id">
                            <option value="">No parent — top level category</option>
                            @foreach($parentOptions as $option)
                            <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Levels: Category → Sub Category (-) → Sub Sub Category (--)</div>
                        @error('parent_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="image">Category Image</label>
                        <input type="file" class="form-control" id="image" wire:model="image">
                        @error('image') <span class="text-danger small">{{ $message }}</span> @enderror

                        @if($image && is_object($image))
                        <div class="mt-2">
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="cat-image-preview">
                        </div>
                        @elseif($isEdit && $category_id)
                        @php $currentCategory = \App\Models\Category::find($category_id); @endphp
                        @if($currentCategory && $currentCategory->image)
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <img src="{{ asset('storage/'.$currentCategory->image) }}" alt="Current image"
                                class="cat-image-preview">
                            <button type="button" class="btn btn-sm btn-label-danger" wire:click="deleteImage">
                                <i class="bx bx-trash me-1"></i> Remove
                            </button>
                        </div>
                        @endif
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input" type="checkbox" id="status" wire:model="status">
                            <label class="form-check-label" for="status">Active (visible on website)</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block">Homepage</label>
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input" type="checkbox" id="is_featured" wire:model="is_featured">
                            <label class="form-check-label" for="is_featured">Show on homepage featured section</label>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                        <button type="button" class="btn btn-outline-secondary" wire:click="cancelForm">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i> {{ $isEdit ? 'Update Category' : 'Save Category' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ===== Category tree list (always visible) ===== --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-bottom pb-3">
            <h5 class="mb-0"><i class='bx bx-sitemap me-2 text-primary'></i>Category List</h5>
            @if(trim($search) === '')
            <span class="cat-drag-hint"><i class='bx bx-move-vertical me-1'></i>Drag <i class='bx bx-menu'></i> to
                reorder — order applies to the website menu</span>
            @else
            <span class="cat-drag-hint">Search results — clear search to reorder</span>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 45%;">Category</th>
                        <th>Slug</th>
                        <th class="text-center">Products</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                @if(count($tree) === 0)
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class='bx bx-folder-open bx-lg text-muted d-block mb-2'></i>
                            <span class="text-muted">No categories found.</span>
                        </td>
                    </tr>
                </tbody>
                @else
                <tbody class="cat-sort-group" wire:key="cat-tree"
                    @if(trim($search) === '') data-sortable="1" @endif>
                    @foreach($tree as $row)
                    <tr wire:key="cat-{{ $row['id'] }}" data-id="{{ $row['id'] }}"
                        data-parent="{{ $row['parent_id'] ?? '' }}"
                        class="cat-row cat-depth-{{ $row['depth'] }}">
                        <td>
                            <div class="d-flex align-items-center gap-2" style="padding-left: {{ $row['depth'] * 26 }}px;">
                                @if(trim($search) === '')
                                <span class="cat-drag" title="Drag to reorder (same level only)"><i class='bx bx-menu'></i></span>
                                @endif
                                <span class="cat-sign">{{ $row['depth'] ? str_repeat('- ', $row['depth']) : '' }}</span>
                                @if($row['image'])
                                <img src="{{ asset('storage/'.$row['image']) }}" alt="{{ $row['name'] }}"
                                    class="cat-thumb">
                                @else
                                <span class="cat-thumb-placeholder"><i class='bx bx-image'></i></span>
                                @endif
                                <span class="cat-name">{{ $row['name'] }}</span>
                                @if(!empty($row['parent_name']))
                                <small class="text-muted">({{ $row['parent_name'] }})</small>
                                @endif
                                @if($row['is_featured'])
                                <i class='bx bxs-star cat-featured' title="Featured on homepage"></i>
                                @endif
                            </div>
                        </td>
                        <td><span class="text-muted small">{{ $row['slug'] }}</span></td>
                        <td class="text-center"><span class="cat-products-badge">{{ $row['products'] }}</span></td>
                        <td>
                            <button type="button" class="cat-status-pill {{ $row['status'] ? 'cat-active' : 'cat-inactive' }}"
                                wire:click="toggleStatus({{ $row['id'] }})"
                                title="Click to {{ $row['status'] ? 'deactivate' : 'activate' }}">
                                <span class="dot"></span> {{ $row['status'] ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-icon btn-label-primary me-1"
                                wire:click="showEditForm({{ $row['id'] }})" title="Edit">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-icon btn-label-danger"
                                wire:click="confirmDelete({{ $row['id'] }})" title="Delete">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
        </div>
    </div>

    @script
    <script>
        const loadSortable = (cb) => {
            if (window.Sortable) return cb();
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js';
            s.onload = cb;
            document.head.appendChild(s);
        };

        const initSortables = (root) => {
            if (!window.Sortable || !root) return;
            root.querySelectorAll('tbody[data-sortable]').forEach((tbody) => {
                if (tbody.dataset.sortInit) return;
                tbody.dataset.sortInit = '1';
                new Sortable(tbody, {
                    handle: '.cat-drag',
                    animation: 150,
                    ghostClass: 'cat-ghost',
                    chosenClass: 'cat-chosen',
                    // Tree list: only allow reordering among same-parent rows
                    onMove: (evt) => (evt.dragged.dataset.parent || '') === (evt.related.dataset.parent || ''),
                    onEnd: (evt) => {
                        if (evt.oldIndex === evt.newIndex) return;
                        const parent = evt.item.dataset.parent || '';
                        const ids = Array.from(tbody.querySelectorAll(':scope > tr'))
                            .filter((tr) => (tr.dataset.parent || '') === parent)
                            .map((tr) => tr.dataset.id);
                        $wire.reorder(parent, ids);
                    }
                });
            });
        };

        loadSortable(() => {
            initSortables(this.$el);
            Livewire.hook('morphed', ({ el }) => initSortables(el));
        });

        window.addEventListener('scroll-to-form', () => {
            const card = document.getElementById('category-form-card');
            if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    </script>
    @endscript
</div>
