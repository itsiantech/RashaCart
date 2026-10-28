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

        /* ── Nested sortable list ── */
        .cat-tree-wrap {
            overflow-x: auto;
        }

        .cat-tree-head,
        .cat-item {
            display: grid;
            grid-template-columns: minmax(240px, 45%) 1fr 100px 130px 110px;
            gap: 12px;
            align-items: center;
            min-width: 720px;
        }

        .cat-tree-head {
            padding: 12px 16px;
            border-bottom: 1px solid #e7e7f1;
            font-size: .8125rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #697a8d;
        }

        .cat-item {
            padding: 8px 16px;
            border-bottom: 1px solid #f2f3f7;
            background: #fff;
            transition: background .15s ease;
        }

        .cat-item:hover {
            background: #f9fafc;
        }

        .cat-children .cat-item {
            background: #fdfdff;
        }

        .cat-children .cat-item:hover {
            background: #f7f8fc;
        }

        .cat-node.cat-ghost .cat-item {
            background: #eeedff;
        }

        .cat-node.cat-chosen > .cat-item {
            box-shadow: 0 2px 10px rgba(105, 108, 255, .25);
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
        <div class="cat-tree-wrap">
            @if(count($forest) === 0)
            <div class="text-center py-5">
                <i class='bx bx-folder-open bx-lg text-muted d-block mb-2'></i>
                <span class="text-muted">No categories found.</span>
            </div>
            @else
            <div class="cat-tree-head">
                <span>Category</span>
                <span>Slug</span>
                <span class="text-center">Products</span>
                <span>Status</span>
                <span class="text-end">Actions</span>
            </div>
            <div class="cat-tree" wire:key="cat-tree" data-parent="" @if(trim($search) === '') data-sortable="1" @endif>
                @foreach($forest as $row)
                @include('livewire.admin.category.partial.tree-node', ['row' => $row, 'sortable' => trim($search) === ''])
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @script
    <script>
        const sortableUrl = @js(asset('assets/admin/vendor/libs/sortable/Sortable.min.js'));

        const loadSortable = (cb) => {
            if (window.Sortable) return cb();
            const existing = document.querySelector('script[data-sortable-lib]');
            if (existing) {
                existing.addEventListener('load', cb);
                return;
            }
            const s = document.createElement('script');
            s.src = sortableUrl;
            s.dataset.sortableLib = '1';
            s.onload = cb;
            s.onerror = () => console.warn('SortableJS failed to load:', sortableUrl);
            document.head.appendChild(s);
        };

        const initSortables = () => {
            if (!window.Sortable) return;
            const root = $wire.$el;
            if (!root) return;
            root.querySelectorAll('[data-sortable]').forEach((list) => {
                // Livewire morphs can drop the marker attribute, so also ask
                // Sortable itself before wiring a list twice.
                if (list.dataset.sortInit || Sortable.get(list)) return;
                list.dataset.sortInit = '1';
                new Sortable(list, {
                    handle: '.cat-drag',
                    draggable: '.cat-node',
                    animation: 150,
                    ghostClass: 'cat-ghost',
                    chosenClass: 'cat-chosen',
                    // Every level is its own list, so a dragged category takes
                    // its sub-tree along and can only be reordered among its
                    // siblings (re-parent via the edit form).
                    group: { name: 'cat-tree', pull: false, put: false },
                    onEnd: (evt) => {
                        if (evt.oldIndex === evt.newIndex) return;
                        const ids = Array.from(list.querySelectorAll(':scope > .cat-node'))
                            .map((node) => node.dataset.id);
                        $wire.reorderAll(ids);
                    },
                });
            });
        };

        loadSortable(() => {
            initSortables();
            Livewire.hook('morphed', () => initSortables());
        });

        window.addEventListener('scroll-to-form', () => {
            const card = document.getElementById('category-form-card');
            if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    </script>
    @endscript
</div>
