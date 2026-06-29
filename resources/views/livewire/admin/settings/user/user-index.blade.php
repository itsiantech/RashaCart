<div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <nav aria-label="breadcrumb" class="mb-2 mb-md-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Staff Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">All Staff</li>
            </ol>
        </nav>

        <div>
            <a wire:navigate href="{{ route('admin.user.create') }}" class="btn btn-danger btn-sm">
                <i class="bx bx-plus"></i> Add New Staff
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
                        <input type="text" placeholder="Search staff by name, email or phone..."
                            wire:model.live.debounce.300ms="search"
                            class="form-control ps-5" style="max-width: 300px;">
                    </div>
                </form>
            </div>
        </div>

        <!-- Staff Table -->
        <div class="table-responsive px-3 px-sm-4">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">Name</th>
                        <th class="text-center text-nowrap">Contact</th>
                        <th class="text-center text-nowrap">Role</th>
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
                                            ? asset('storage/' . $admin->image) 
                                            : asset('assets/admin/img/avatars/user-avater.png') }}"
                                        alt="{{ $admin->name }}"
                                        class="img-fluid w-100 h-100 object-fit-cover">
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $admin->name }}</div>
                                    <small class="text-muted">ID: {{ $admin->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center text-wrap">
                            <div><i class="bx bx-envelope me-1"></i>{{ $admin->email }}</div>
                            @if($admin->phone)
                            <div><i class="bx bx-phone me-1"></i>{{ $admin->phone }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            @forelse ($admin->roles as $role)
                            <span class="badge 
                                        @if($role->name == 'admin') bg-danger
                                        @elseif($role->name == 'manager') bg-warning text-dark
                                        @else bg-primary @endif">
                                {{ ucfirst($role->name) }}
                            </span>
                            @empty
                            <span class="badge bg-secondary">No Role</span>
                            @endforelse
                        </td>
                        <td class="text-center">
                            @if (!$admin->hasRole('admin'))
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="statusSwitch{{ $admin->id }}" @checked($admin->status === 1)
                                wire:click="toggleStatus({{ $admin->id }})">
                                <label class="form-check-label" for="statusSwitch{{ $admin->id }}">
                                    {{ $admin->status ? 'Active' : 'Inactive' }}
                                </label>
                            </div>
                            @else
                            <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.user.edit', $admin->id) }}"
                                    class="btn btn-sm btn-primary" aria-label="Edit staff" wire:navigate>
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <button
                                    wire:click="confirmDelete({{ $admin->id }})"
                                    class="btn btn-sm btn-danger"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="confirmDelete({{ $admin->id }})">
                                        <i class="bx bx-trash"></i>
                                    </span>
                                    <span wire:loading wire:target="confirmDelete({{ $admin->id }})">
                                        <i class="bx bx-spin bx-loader"></i>
                                    </span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No staff members found.
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