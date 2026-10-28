<div class="mn-page">
    <div class="content-header mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0">Menu Settings</h4>
                <p class="text-muted mb-0">Build the storefront header menu from your pages and categories</p>
            </div>
            @if ($menuCount > 0)
                <span class="badge bg-label-success"><i class="bx bx-cloud-upload me-1"></i>Auto-saved</span>
            @endif
        </div>
    </div>

    <div class="row g-3 align-items-start">
        <div class="col-lg-5">
            <div class="card h-100 mn-source-card">
                <div class="card-header">
                    <h5 class="mb-0">Add to Menu</h5>
                    <small class="text-muted">Pick a page or category, then add it to the right side</small>
                </div>
                <div class="card-body">
                    <div class="mn-tabs">
                        <button type="button" class="mn-tab {{ $tab === 'pages' ? 'active' : '' }}" wire:click="$set('tab', 'pages')">
                            <i class="bx bx-file"></i> Pages
                        </button>
                        <button type="button" class="mn-tab {{ $tab === 'categories' ? 'active' : '' }}" wire:click="$set('tab', 'categories')">
                            <i class="bx bx-category"></i> Categories
                        </button>
                    </div>

                    <div class="mn-source-list">
                        @if ($tab === 'pages')
                            @forelse ($pages as $page)
                                <div class="mn-source">
                                    <div class="mn-source-label">
                                        <i class="bx bx-file"></i>
                                        <div>
                                            <span class="mn-source-name">{{ $page->title }}</span>
                                            <span class="mn-source-url">/page/{{ $page->slug }}</span>
                                        </div>
                                    </div>
                                    @if ($page->status && in_array((int) $page->id, $inMenuPages, true))
                                        <span class="mn-inmenu"><i class="bx bx-check"></i> In menu</span>
                                    @elseif (! $page->status)
                                        <span class="mn-inmenu is-off">Inactive</span>
                                    @else
                                        <button type="button" class="mn-add" wire:click="addPage({{ $page->id }})">Add to Menu</button>
                                    @endif
                                </div>
                            @empty
                                <div class="mn-empty">
                                    <i class="bx bx-file"></i>
                                    <p class="mb-1 fw-semibold">No pages yet</p>
                                    <p class="text-muted small mb-0">Create a page first, then come back here.</p>
                                </div>
                            @endforelse
                        @else
                            @forelse ($categoryRows as $category)
                                <div class="mn-source mn-cat-depth-{{ min($category['depth'], 2) }}">
                                    <div class="mn-source-label">
                                        <i class="bx {{ $category['depth'] === 0 ? 'bx-category' : 'bx-subdirectory-right' }}"></i>
                                        <div>
                                            <span class="mn-source-name">{{ $category['name'] }}</span>
                                            <span class="mn-source-url">{{ $category['depth'] === 0 ? 'Category' : ($category['depth'] === 1 ? 'Sub category' : 'Sub sub category') }}</span>
                                        </div>
                                    </div>
                                    @if (in_array((int) $category['id'], $inMenuCategories, true))
                                        <span class="mn-inmenu"><i class="bx bx-check"></i> In menu</span>
                                    @else
                                        <button type="button" class="mn-add" wire:click="addCategory({{ $category['id'] }})">Add to Menu</button>
                                    @endif
                                </div>
                            @empty
                                <div class="mn-empty">
                                    <i class="bx bx-category"></i>
                                    <p class="mb-1 fw-semibold">No active categories</p>
                                    <p class="text-muted small mb-0">Activate a category first, then come back here.</p>
                                </div>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100 mn-menu-card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">Menu</h5>
                        <small class="text-muted">Drag the handle to reorder — drop an item on another to nest it as a dropdown.</small>
                    </div>
                    @if ($menuCount > 0)
                        <span class="badge bg-label-success"><i class="bx bx-cloud-upload me-1"></i>Auto-saved</span>
                    @endif
                </div>
                <div class="card-body">
                    <ul class="mn-list mn-root" data-menu-list data-parent="" wire:key="mn-root-list">
                        @foreach ($menuRows as $row)
                            @include('livewire.admin.settings.partial.menu-item', ['row' => $row])
                        @endforeach
                    </ul>

                    @if ($menuCount === 0)
                        <div class="mn-empty mn-empty-menu">
                            <i class="bx bx-menu-alt-left"></i>
                            <p class="mb-1 fw-semibold">The menu is empty</p>
                            <p class="text-muted small mb-0">Pick a page or category from the left and hit <b>Add to Menu</b>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .mn-page .mn-tabs { display: flex; gap: 4px; background: #f4f6fb; border-radius: 10px; padding: 4px; margin-bottom: 14px; }
        .mn-page .mn-tab {
            flex: 1; border: 0; background: transparent; border-radius: 8px;
            padding: .5rem .75rem; font-size: .86rem; font-weight: 600; color: #5d6473;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .mn-page .mn-tab i { font-size: 1rem; }
        .mn-page .mn-tab.active { background: #fff; color: #7A0000; box-shadow: 0 2px 6px rgba(15, 20, 40, .08); }

        .mn-page .mn-source-list { max-height: 560px; overflow: auto; padding-right: 2px; }
        .mn-page .mn-source {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 9px 10px; border: 1px solid #eef0f5; border-radius: 9px; margin-bottom: 8px;
        }
        .mn-page .mn-source:hover { border-color: #d9deea; background: #fbfcfe; }
        .mn-page .mn-source-label { display: flex; align-items: center; gap: 9px; min-width: 0; }
        .mn-page .mn-source-label > i { color: #8a93a5; font-size: 1rem; }
        .mn-page .mn-source-name { display: block; font-size: .88rem; font-weight: 600; color: #2b3040; line-height: 1.25; }
        .mn-page .mn-source-url { display: block; font-size: .72rem; color: #98a0b0; }
        .mn-page .mn-cat-depth-1 { margin-left: 18px; }
        .mn-page .mn-cat-depth-2 { margin-left: 36px; }
        .mn-page .mn-add {
            border: 0; background: #7A0000; color: #fff; font-size: .75rem; font-weight: 600;
            padding: .35rem .7rem; border-radius: 7px; white-space: nowrap;
        }
        .mn-page .mn-add:hover { background: #5e0000; }
        .mn-page .mn-inmenu {
            font-size: .72rem; font-weight: 600; color: #2f9e44; background: #e7f7ec;
            border-radius: 20px; padding: .22rem .6rem; display: inline-flex; align-items: center; gap: 4px;
            white-space: nowrap;
        }
        .mn-page .mn-inmenu.is-off { color: #8a93a5; background: #f0f2f6; }

        .mn-page .mn-list { list-style: none; margin: 0; padding: 0; }
        .mn-page .mn-root { min-height: 40px; }
        .mn-page .mn-item { list-style: none; }
        .mn-page .mn-row {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 10px; border: 1px solid #e9ecf3; border-radius: 9px;
            background: #fff; margin-bottom: 6px;
        }
        .mn-page .mn-row-missing { background: #fff7f7; border-color: #f3d6d6; }
        .mn-page .mn-drag { cursor: grab; color: #b3bac8; font-size: 1.05rem; line-height: 1; }
        .mn-page .mn-drag:active { cursor: grabbing; }
        .mn-page .mn-type-icon { color: #8a93a5; }
        .mn-page .mn-label { min-width: 0; margin-right: auto; }
        .mn-page .mn-name { display: block; font-size: .88rem; font-weight: 600; color: #2b3040; line-height: 1.25; }
        .mn-page .mn-url {
            display: block; font-size: .72rem; color: #98a0b0;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px;
        }
        .mn-page .mn-url.is-missing { color: #c0392b; white-space: normal; }
        .mn-page .mn-remove {
            border: 0; background: transparent; color: #c6ccd8; font-size: 1rem;
            padding: 2px 5px; border-radius: 6px; line-height: 1;
        }
        .mn-page .mn-remove:hover { color: #c0392b; background: #fdeeee; }

        .mn-page .mn-children { margin: 6px 0 6px 26px; padding-left: 6px; border-left: 2px dashed #e3e7ef; }
        .mn-page .mn-children.is-empty { min-height: 8px; margin-top: 4px; margin-bottom: 4px; }
        .mn-page.mn-dragging .mn-children.is-empty {
            min-height: 44px; margin: 8px 0 8px 26px; padding: 8px;
            border: 1px dashed #b9c2d6; border-radius: 8px; background: #f4f7ff;
        }
        .mn-page.mn-dragging .mn-children.is-empty::before {
            content: '↓ Drop here for ' attr(data-label);
            display: flex; align-items: center; justify-content: center;
            font-size: .74rem; color: #7c869c;
        }
        .mn-page.mn-dragging .mn-children:not(.is-empty) {
            border-left-color: #9fb0d0; background: #fbfcff; border-radius: 0 8px 8px 0;
        }

        .mn-page .mn-empty {
            text-align: center; color: #98a0b0; padding: 22px 10px;
            border: 1px dashed #dfe4ef; border-radius: 10px; background: #fbfcff;
        }
        .mn-page .mn-empty i { font-size: 1.6rem; display: block; margin-bottom: 6px; }
        .mn-page .mn-empty-menu { padding: 34px 10px; }
    </style>

    @script
    <script>
        (async () => {
            if (! window.Sortable) {
                await new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = @js(asset('assets/admin/vendor/libs/sortable/Sortable.min.js'));
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            }

            const host = document.querySelector('.mn-page');
            if (! host || ! window.Sortable) return;

            const depthOf = (el) => {
                let lists = 0;
                let node = el.parentElement;
                while (node && node !== host) {
                    if (node.matches && node.matches('[data-menu-list]')) lists++;
                    node = node.parentElement;
                }
                return Math.max(0, lists - 1);
            };

            const subtreeDepth = (item) => {
                const base = depthOf(item);
                let extra = 0;
                item.querySelectorAll('[data-menu-item]').forEach((child) => {
                    extra = Math.max(extra, depthOf(child) - base);
                });
                return extra;
            };

            const initLists = () => {
                if (! document.body.contains(host)) return;

                host.querySelectorAll('[data-menu-list]').forEach((list) => {
                    if (Sortable.get(list)) return;

                    Sortable.create(list, {
                        group: { name: 'mn-menu', pull: true, put: true },
                        handle: '.mn-drag',
                        draggable: '.mn-item',
                        animation: 150,
                        forceFallback: true,
                        fallbackTolerance: 3,
                        swapThreshold: 0.65,
                        onStart: () => host.classList.add('mn-dragging'),
                        onMove: (evt) => {
                            if (! evt.to.dataset.parent) return true;

                            const parentItem = evt.to.closest('[data-menu-item]');
                            if (! parentItem || ! evt.dragged.dataset.id) return true;

                            const childLevel = depthOf(parentItem) + 1;
                            return childLevel + subtreeDepth(evt.dragged) <= 2;
                        },
                        onEnd: () => {
                            host.classList.remove('mn-dragging');

                            const rows = Array.from(host.querySelectorAll('[data-menu-item]')).map((item) => {
                                const list = item.parentElement.closest('[data-menu-list]');
                                return { id: item.dataset.id, parent: list ? list.dataset.parent : '' };
                            });

                            if (rows.length) $wire.reorderMenu(rows);
                        },
                    });
                });
            };

            initLists();
            Livewire.hook('morphed', () => initLists());
        })();
    </script>
    @endscript
</div>
