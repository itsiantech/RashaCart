<div class="pg-admin">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.pages.index') }}" wire:navigate>Pages</a>
            </li>
            <li class="breadcrumb-item active">{{ $pageId ? 'Edit' : 'Add Page' }}</li>
        </ol>
    </nav>

    <form wire:submit.prevent="save">
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ $pageId ? 'Edit Page' : 'Add Page' }}</h5>
                        <div class="d-flex gap-2">
                            @if ($pageId && $status)
                                <a href="{{ route('public.page', $slug) }}" target="_blank"
                                    class="btn btn-outline-info btn-sm d-inline-flex align-items-center">
                                    <i class="bx bx-show me-1"></i> View
                                </a>
                            @endif
                            <a href="{{ route('admin.pages.index') }}" wire:navigate
                                class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                                <i class="bx bx-arrow-back me-1"></i> Go back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($errors->has('blocks') || $errors->has('blockUploads'))
                            <div class="alert alert-danger py-2">
                                {{ $errors->first('blocks') ?: $errors->first('blockUploads') }}
                            </div>
                        @endif

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="title">Title <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" wire:model.live.debounce.400ms="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror" placeholder="Page title">
                                @error('title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="slug">Slug <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" wire:model.blur="slug" id="slug"
                                    class="form-control @error('slug') is-invalid @enderror" placeholder="page-slug">
                                @error('slug')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Public link: {{ url('/page/' . ($slug ?: 'your-slug')) }}</small>
                            </div>
                        </div>

                        <div class="mb-2 d-flex align-items-center justify-content-between">
                            <label class="form-label mb-0">Page Description</label>
                            <small class="text-muted"><i class="bx bx-move-vertical"></i> Drag the bottom-right corner to resize</small>
                        </div>

                        <div wire:ignore class="pg-editor" data-quill data-description="1"
                            data-placeholder="Write the page description…">
                            <div class="pg-editor-body">{!! $description !!}</div>
                        </div>
                        @error('description')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                        <div>
                            <h5 class="mb-0">Page Layout</h5>
                            <small class="text-muted">Add sections, then drag the handle to arrange them.</small>
                        </div>
                        <div class="pg-addbar">
                            <button type="button" wire:click="addBlock('text')" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-text me-1"></i> Text
                            </button>
                            <button type="button" wire:click="addBlock('image')" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-image me-1"></i> Image
                            </button>
                            <button type="button" wire:click="addBlock('columns')" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-columns me-1"></i> Columns
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (count($blocks) === 0)
                            <div class="pg-empty">
                                <i class="bx bx-layout bx-lg d-block mb-2"></i>
                                <div class="fw-semibold mb-1">No sections yet</div>
                                <small>Add a Text, Image or Columns section above. Drag sections by their handle to change the order.</small>
                            </div>
                        @else
                            <div class="pg-blocks" data-block-list>
                                @foreach ($blocks as $i => $block)
                                    @php
                                        $blockId = $block['id'];
                                        $type = $block['type'] ?? 'text';
                                        $layout = (int) ($block['layout'] ?? 2);
                                        $colCount = count($block['columns'] ?? []);
                                    @endphp
                                    <div class="pg-block" data-block-id="{{ $blockId }}" wire:key="blk-{{ $blockId }}">
                                        <div class="pg-block-head">
                                            <span class="pg-drag" title="Drag to reorder"><i class="bx bx-grid-vertical"></i></span>
                                            <span class="pg-block-badge">
                                                @if ($type === 'text')
                                                    Text section
                                                @elseif ($type === 'image')
                                                    Image section
                                                @else
                                                    Columns section ({{ $colCount }})
                                                @endif
                                            </span>
                                            <div class="ms-auto d-flex gap-1">
                                                <button type="button" wire:click="moveBlock('{{ $blockId }}', -1)"
                                                    class="btn btn-sm btn-icon btn-outline-secondary" title="Move up">
                                                    <i class="bx bx-chevron-up"></i>
                                                </button>
                                                <button type="button" wire:click="moveBlock('{{ $blockId }}', 1)"
                                                    class="btn btn-sm btn-icon btn-outline-secondary" title="Move down">
                                                    <i class="bx bx-chevron-down"></i>
                                                </button>
                                                <button type="button" wire:click="removeBlock('{{ $blockId }}')"
                                                    wire:confirm="Remove this section?"
                                                    class="btn btn-sm btn-icon btn-outline-danger" title="Remove section">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="pg-block-body">
                                            @if ($type === 'text')
                                                <div wire:ignore class="pg-editor" data-quill
                                                    data-block-id="{{ $blockId }}" data-column=""
                                                    data-placeholder="Text for this section…">
                                                    <div class="pg-editor-body">{!! $block['content'] ?? '' !!}</div>
                                                </div>
                                            @elseif ($type === 'image')
                                                @if (! empty($block['image']))
                                                    <div class="pg-media mb-2">
                                                        <img src="{{ asset('storage/' . $block['image']) }}" alt="">
                                                        <button type="button" class="pg-media-remove"
                                                            wire:click="clearBlockImage('{{ $blockId }}')" title="Remove image">
                                                            <i class="bx bx-x"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                                <input type="file" wire:model="blockUploads.{{ $blockId }}" accept="image/*"
                                                    class="form-control form-control-sm">
                                                <div wire:loading wire:target="blockUploads.{{ $blockId }}" class="small text-primary mt-1">
                                                    Uploading…
                                                </div>
                                                <input type="text" wire:model.blur="blocks.{{ $i }}.caption"
                                                    class="form-control form-control-sm mt-2" placeholder="Image caption (optional)">
                                            @else
                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <small class="text-muted">Layout:</small>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" wire:click="setLayout('{{ $blockId }}', 2)"
                                                            class="btn {{ $layout === 2 ? 'btn-primary' : 'btn-outline-primary' }}">2 Columns</button>
                                                        <button type="button" wire:click="setLayout('{{ $blockId }}', 3)"
                                                            class="btn {{ $layout === 3 ? 'btn-primary' : 'btn-outline-primary' }}">3 Columns</button>
                                                    </div>
                                                </div>

                                                <div class="row g-3">
                                                    @foreach ($block['columns'] ?? [] as $ci => $column)
                                                        @php $colKey = $blockId . '_c' . $ci; @endphp
                                                        <div class="col-md-{{ $layout === 3 ? 4 : 6 }}"
                                                            wire:key="col-{{ $colKey }}">
                                                            <div class="pg-col">
                                                                <select wire:change="setColumnType('{{ $blockId }}', {{ $ci }}, $event.target.value)"
                                                                    class="form-select form-select-sm mb-2">
                                                                    <option value="text" @selected(($column['type'] ?? 'text') === 'text')>Text</option>
                                                                    <option value="image" @selected(($column['type'] ?? 'text') === 'image')>Image</option>
                                                                </select>

                                                                @if (($column['type'] ?? 'text') === 'image')
                                                                    @if (! empty($column['image']))
                                                                        <div class="pg-media mb-2">
                                                                            <img src="{{ asset('storage/' . $column['image']) }}" alt="">
                                                                            <button type="button" class="pg-media-remove"
                                                                                wire:click="clearBlockImage('{{ $blockId }}', {{ $ci }})"
                                                                                title="Remove image">
                                                                                <i class="bx bx-x"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" wire:model="blockUploads.{{ $colKey }}" accept="image/*"
                                                                        class="form-control form-control-sm">
                                                                    <div wire:loading wire:target="blockUploads.{{ $colKey }}"
                                                                        class="small text-primary mt-1">Uploading…</div>
                                                                    <input type="text" wire:model.blur="blocks.{{ $i }}.columns.{{ $ci }}.caption"
                                                                        class="form-control form-control-sm mt-2" placeholder="Caption (optional)">
                                                                @else
                                                                    <div wire:ignore class="pg-editor pg-editor-sm" data-quill
                                                                        data-block-id="{{ $blockId }}" data-column="{{ $ci }}"
                                                                        data-placeholder="Column text…">
                                                                        <div class="pg-editor-body">{!! $column['content'] ?? '' !!}</div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Feature Image</h6>
                    </div>
                    <div class="card-body">
                        @if ($feature_image)
                            <img src="{{ $feature_image->temporaryUrl() }}" alt="Preview"
                                class="img-fluid rounded mb-2" style="max-height:220px;object-fit:cover;">
                        @elseif ($existing_feature_image)
                            <img src="{{ asset('storage/' . $existing_feature_image) }}" alt="Preview"
                                class="img-fluid rounded mb-2" style="max-height:220px;object-fit:cover;">
                        @endif
                        <input type="file" wire:model="feature_image" accept="image/*"
                            class="form-control @error('feature_image') is-invalid @enderror">
                        @error('feature_image')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <div wire:loading wire:target="feature_image" class="small text-primary mt-1">Uploading…</div>
                        @if ($feature_image || $existing_feature_image)
                            <button type="button" wire:click="clearFeatureImage"
                                class="btn btn-sm btn-outline-danger mt-2">
                                <i class="bx bx-trash me-1"></i> Remove image
                            </button>
                        @endif
                        <small class="text-muted d-block mt-2">Shown at the top of the public page.</small>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">SEO</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="seo_title">SEO Title</label>
                            <input type="text" wire:model.blur="seo_title" id="seo_title"
                                class="form-control @error('seo_title') is-invalid @enderror"
                                placeholder="Shown in search / social previews">
                            @error('seo_title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label" for="meta_title">Meta Title</label>
                            <input type="text" wire:model.blur="meta_title" id="meta_title"
                                class="form-control @error('meta_title') is-invalid @enderror"
                                placeholder="Browser tab title">
                            @error('meta_title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="status"
                                wire:model="status">
                            <label class="form-check-label" for="status">Active (visible on the website)</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled"
                            wire:target="save,feature_image,blockUploads">
                            <span wire:loading.remove wire:target="save">
                                <i class="bx bx-save me-1"></i> {{ $pageId ? 'Update Page' : 'Save Page' }}
                            </span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        .pg-admin .card { border-radius: .75rem; }
        .pg-admin .pg-addbar .btn { white-space: nowrap; }

        .pg-editor {
            display: flex;
            flex-direction: column;
            height: 340px;
            min-height: 240px;
            max-height: 80vh;
            resize: vertical;
            overflow: hidden;
            border: 1px solid #dfe3e8;
            border-radius: .65rem;
            background: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
            transition: border-color .15s, box-shadow .15s;
        }
        .pg-editor.pg-editor-sm { height: 180px; min-height: 140px; }
        .pg-editor:focus-within { border-color: #696cff; box-shadow: 0 0 0 .2rem rgba(105, 108, 255, .12); }
        .pg-editor .ql-toolbar.ql-snow {
            border: 0;
            border-bottom: 1px solid #eef0f3;
            background: #f8f9fc;
            padding: 6px 10px;
        }
        .pg-editor .ql-container.ql-snow { border: 0; flex: 1; min-height: 0; overflow-y: auto; font-size: 15px; font-family: inherit; }
        .pg-editor .ql-editor { min-height: 100%; line-height: 1.75; padding: 12px 14px; }
        .pg-editor .ql-editor.ql-blank::before { color: #a1a7b3; font-style: normal; }
        .pg-editor .ql-snow .ql-tooltip { z-index: 5; }

        .pg-blocks { display: flex; flex-direction: column; gap: 12px; }
        .pg-block { border: 1px solid #e7e7f1; border-radius: .7rem; background: #fff; overflow: hidden; }
        .pg-block-head { display: flex; align-items: center; gap: 8px; padding: 7px 12px; background: #f8f9fc; border-bottom: 1px solid #eef0f5; }
        .pg-block-body { padding: 12px; }
        .pg-block-badge { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #697a8d; }
        .pg-drag { cursor: grab; color: #9aa0ac; font-size: 1.2rem; line-height: 1; padding: 2px 4px; }
        .pg-drag:active { cursor: grabbing; }
        .pg-ghost { outline: 2px dashed #696cff; outline-offset: -2px; background: #f4f4ff; opacity: .85; }
        .pg-empty { border: 1px dashed #d9dce6; border-radius: .7rem; padding: 30px 16px; text-align: center; color: #8a91a0; background: #fbfbfe; }
        .pg-media { position: relative; display: inline-block; max-width: 100%; }
        .pg-media img { max-height: 220px; max-width: 100%; border-radius: .5rem; display: block; }
        .pg-media-remove {
            position: absolute; top: 6px; right: 6px; width: 26px; height: 26px; border: 0;
            border-radius: 50%; background: rgba(0, 0, 0, .65); color: #fff; line-height: 1;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .pg-media-remove:hover { background: #ff3e1d; }
        .pg-col { border: 1px solid #eef0f5; border-radius: .6rem; padding: 10px; background: #fdfdff; height: 100%; }
        .pg-admin .btn-icon { width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    </style>

    @script
    <script>
        const sortableUrl = @js(asset('assets/admin/vendor/libs/sortable/Sortable.min.js'));
        const QUILL_CSS = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
        const QUILL_JS = 'https://cdn.quilljs.com/1.3.6/quill.min.js';
        const pgRoot = $wire.$el;

        const ensureCss = (href) => {
            if (document.querySelector('link[href="' + href + '"]')) return;
            const l = document.createElement('link');
            l.rel = 'stylesheet';
            l.href = href;
            document.head.appendChild(l);
        };

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

        const loadQuill = (cb) => {
            ensureCss(QUILL_CSS);
            if (window.Quill) return cb();
            const existing = document.querySelector('script[data-quill-lib]');
            if (existing) {
                existing.addEventListener('load', cb);
                return;
            }
            const s = document.createElement('script');
            s.src = QUILL_JS;
            s.dataset.quillLib = '1';
            s.onload = cb;
            s.onerror = () => console.warn('Quill failed to load:', QUILL_JS);
            document.head.appendChild(s);
        };

        const debounce = (fn, ms) => {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => fn(...args), ms);
            };
        };

        const initQuills = () => {
            if (!window.Quill) return;

            pgRoot.querySelectorAll('[data-quill]').forEach((el) => {
                if (el.__quill) return;

                const container = el.querySelector('.pg-editor-body');
                if (!container) return;

                const quill = new Quill(container, {
                    theme: 'snow',
                    placeholder: el.dataset.placeholder || 'Write something…',
                    modules: {
                        toolbar: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ align: [] }],
                            ['blockquote', 'link', 'image'],
                            ['clean'],
                        ],
                    },
                });

                el.__quill = quill;

                const push = debounce(() => {
                    const html = quill.root.innerHTML;

                    if (el.dataset.description) {
                        $wire.setDescription(html);
                        return;
                    }

                    const column = el.dataset.column === '' ? null : parseInt(el.dataset.column, 10);
                    $wire.setBlockContent(el.dataset.blockId, column, html);
                }, 500);

                quill.on('text-change', push);
            });
        };

        const initSortables = () => {
            if (!window.Sortable) return;

            pgRoot.querySelectorAll('[data-block-list]').forEach((list) => {
                if (Sortable.get(list)) return;

                new Sortable(list, {
                    handle: '.pg-drag',
                    draggable: '.pg-block',
                    animation: 150,
                    ghostClass: 'pg-ghost',
                    onEnd: (evt) => {
                        if (evt.oldIndex === evt.newIndex) return;
                        const ids = Array.from(list.querySelectorAll(':scope > .pg-block'))
                            .map((node) => node.dataset.blockId);
                        $wire.reorderBlocks(ids);
                    },
                });
            });
        };

        const bootPageBuilder = () => {
            initQuills();
            initSortables();
            Livewire.hook('morphed', () => {
                initQuills();
                initSortables();
            });
        };

        loadSortable(() => loadQuill(bootPageBuilder));
    </script>
    @endscript
</div>
