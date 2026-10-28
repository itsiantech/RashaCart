<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item active">Pages</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="card-title mb-0">All Pages</h5>
                <a href="{{ route('admin.pages.create') }}" wire:navigate
                    class="btn btn-primary btn-sm d-inline-flex align-items-center mt-2 mt-md-0">
                    <i class="bx bx-plus me-1"></i> Add Page
                </a>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Sections</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if ($pages->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bx bx-file-blank bx-lg text-muted d-block mb-2"></i>
                                <span class="text-muted">No pages yet. <a href="{{ route('admin.pages.create') }}" wire:navigate>Create your first page</a>.</span>
                            </td>
                        </tr>
                    @else
                        @foreach ($pages as $page)
                            <tr class="{{ $page->trashed() ? 'table-danger' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($page->feature_image)
                                            <img src="{{ asset('storage/' . $page->feature_image) }}" alt=""
                                                style="width:38px;height:38px;object-fit:cover;border-radius:.4rem;">
                                        @else
                                            <span class="d-inline-flex align-items-center justify-content-center text-muted"
                                                style="width:38px;height:38px;border-radius:.4rem;background:#f3f4f9;">
                                                <i class="bx bx-image-alt"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $page->title }}</div>
                                            @if ($page->meta_title)
                                                <small class="text-muted">Meta: {{ $page->meta_title }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code>/page/{{ $page->slug }}</code>
                                    @if ($page->seo_title)
                                        <div><small class="text-muted">{{ $page->seo_title }}</small></div>
                                    @endif
                                </td>
                                <td>{{ count($page->blocks ?? []) }}</td>
                                <td>
                                    <span class="badge {{ $page->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $page->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $page->updated_at?->format('d M Y, h:i A') }}</small></td>
                                <td>
                                    <div class="d-flex">
                                        @unless ($page->trashed())
                                            @if ($page->status)
                                                <a href="{{ route('public.page', $page->slug) }}" target="_blank"
                                                    class="btn btn-sm btn-info me-1" title="View page">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            @endif
                                            <a wire:navigate href="{{ route('admin.pages.edit', $page->id) }}"
                                                class="btn btn-sm btn-primary me-1" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button wire:click="toggleStatus({{ $page->id }})"
                                                class="btn btn-sm btn-warning me-1" title="Toggle status">
                                                <i class="bx bx-power-off"></i>
                                            </button>
                                            <button wire:click="delete({{ $page->id }})"
                                                wire:confirm="Move this page to trash?"
                                                class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        @else
                                            <button wire:click="restore({{ $page->id }})"
                                                class="btn btn-sm btn-success me-1" title="Restore">
                                                <i class="bx bx-undo"></i>
                                            </button>
                                            <button wire:click="forceDelete({{ $page->id }})"
                                                wire:confirm="Delete this page permanently?"
                                                class="btn btn-sm btn-danger" title="Delete permanently">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
