<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Banner</a>
            </li>
            <li class="breadcrumb-item active">Index</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="col-12 col-md-4 mb-2 mb-md-0 pe-md-2">
                    <input type="text" class="form-control" wire:model.live="search"
                        placeholder="Search attributes...">
                </div>

                @can('banner.create')
                    <div class="col-12 col-md-auto ms-md-auto text-md-end">
                        <a href="{{ route('admin.banner.create') }}" wire:navigate
                            class="btn btn-primary btn-sm d-inline-flex align-items-center">
                            <i class="bx bx-plus me-1"></i> Add New
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if ($banners->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">No banners found.</td>
                        </tr>
                    @else
                        @foreach ($banners as $banner)
                            <tr>
                                <td>
                                    @if ($banner->image)
                                        <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner Image"
                                            style="max-height: 50px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td> {{ $banner->title ?? 'No data' }}</td>
                                <td>{{ $banner->description ?? 'No data' }}</td>
                                <td> {{ $banner->type }}</td>
                                <td>
                                    <span class="badge {{ $banner->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $banner->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('banner.update')
                                            <a wire:navigate href="{{ route('admin.banner.edit', $banner->id) }}"
                                                class="btn btn-sm btn-primary me-1">
                                                <i class="bx bx-edit"></i> Edit
                                            </a>
                                        @endcan
                                        @can('banner.delete')
                                            <button onclick="confirmDelete({{ $banner->id }})"
                                                class="btn btn-sm btn-danger">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
                                        @endcan
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
