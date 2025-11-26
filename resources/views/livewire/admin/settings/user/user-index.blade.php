<div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <nav aria-label="breadcrumb" class="mb-2 mb-md-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Admin</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Index</li>
            </ol>
        </nav>

        <div>

            <a wire:navigate href="{{ route('admin.user.create') }}" class="btn btn-danger btn-sm">
                <i class="bx bx-plus"></i> Add New
            </a>

        </div>
    </div>
    <div class="card py-3">


        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 px-3 px-sm-4 mb-4">

            <div class="d-flex flex-column flex-sm-row align-items-center gap-3 w-100 w-sm-auto">
                <form class="flex-grow-1 flex-sm-grow-0" role="search" autocomplete="off">
                    <div class="position-relative">
                        <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="text" placeholder="Search..." wire:model.live.debounce.300ms="search"
                            class="form-control ps-5" style="max-width: 300px;">
                    </div>
                </form>
            </div>
        </div>

        <!-- admins Table -->
        <div class="table-responsive px-3 px-sm-4">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">Name</th>
                        <th class="text-center text-nowrap">Contact</th>
                        <th class="text-center text-nowrap">Status</th>
                        <th class="text-center text-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td class="text-nowrap">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle overflow-hidden" style="width: 40px; height: 40px;">
                                       <img src="{{ $admin->image 
                                            ? asset('storage/' . $customer->image) 
                                            : asset('assets/admin/img/avatars/user-avater.png') }}" alt="customer image"
                                            class="img-fluid">
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $admin->name }}</div>
                                        <small class="text-muted">
                                            @foreach ($admin->roles as $role)
                                                {{ $role->name }}
                                            @endforeach
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-wrap">
                                <div>Email: {{ $admin->email }}</div>
                                <div>Phone: {{ $admin->phone }}</div>
                            </td>
                            <td class="text-center">
                                @if (!$admin->hasRole('super-admin'))
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="statusSwitch{{ $admin->id }}" @checked($admin->status === 1)
                                            wire:click="toggleStatus({{ $admin->id }})">
                                    </div>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if (!$admin->hasRole('super-admin'))
                                        <a href="{{ route('admin.user.edit', $admin->id) }}"
                                            class="btn btn-sm btn-primary" aria-label="Edit admin" wire:navigate>
                                            <i class="bx bx-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" aria-label="Delete admin"
                                            onclick="if(confirm('Are you sure you want to delete this admin?')) { @this.deleteadmin({{ $admin->id }}) }">
                                           <i class="bx bx-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No admins found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4 mb-3">
                {{ $admins->links() }}
            </div>
        </div>

    </div>
</div>
