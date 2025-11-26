<div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <nav aria-label="breadcrumb" class="mb-2 mb-md-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Role Permission</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Index</li>
            </ol>
        </nav>

        <div>
            @if ($showAddRoleForm || $showEditRoleForm)
                <a wire:navigate href="{{ route('admin.rolepermission.index') }}" class="btn btn-danger btn-sm">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
            @else
                <button wire:click="addRoleForm" class="btn btn-danger btn-sm">
                    <i class="bx bx-plus"></i> Add Role
                </button>
            @endif
        </div>
    </div>


    @if ($showAddRoleForm)
        <div class="bg-white p-4 rounded shadow-sm mb-3">
            <h2 class="h5 mb-4 text-dark">Add New Role</h2>
            <form wire:submit.prevent="roleStore" class="d-flex flex-column flex-md-row align-items-center gap-3">
                <div class="flex-grow-1">
                    <input type="text" class="form-control" placeholder="Role Name" wire:model="RoleName" />
                    @error('RoleName')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Save
                </button>
            </form>
        </div>
    @elseif($showEditRoleForm)
        <div class="bg-white p-4 rounded shadow-sm mb-3">
            <h2 class="h5 mb-4 text-dark">Edit Role</h2>
            <form wire:submit.prevent="updateRole" class="d-flex flex-column flex-md-row align-items-center gap-3">
                <div class="flex-grow-1">
                    <input type="text" class="form-control" placeholder="Role Name"
                        wire:model.debounce.500ms="RoleName" />
                    @error('RoleName')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    @else
        <div class="rounded-3 border bg-white pt-3 mt-2" style="border-color: #dee2e6;">
            <div class="d-flex flex-column flex-sm-row justify-content-between col-3 px-4 mb-3">
                <form class="w-100 w-sm-auto position-relative">
                    <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ps-3 text-muted"
                        style="pointer-events:none; font-size: 1.2rem;"></i>
                    <input type="text" placeholder="Search..." wire:model.live.debounce.300ms="search"
                        class="form-control ps-5" style="min-width: 250px;" />
                </form>
            </div>

            <!-- Roles Table -->
            <div class="table-responsive px-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-nowrap">Role Name</th>
                            <th scope="col" class="text-center text-nowrap">Permissions</th>
                            <th scope="col" class="text-center text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td class="text-dark">{{ $role->name }}</td>
                                <td>
                                    @if ($role->permissions->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($role->permissions as $permission)
                                                <span class="badge bg-dark">{{ $permission->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <small class="text-muted fst-italic">No permissions</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if ($role->name !== 'super-admin')
                                            <button wire:click="editRole({{ $role->id }})"
                                                class="btn btn-sm btn-primary" aria-label="Edit Role" title="Edit Role">
                                                <i class="bx bx-pencil"></i>
                                            </button>

                                            <button class="btn btn-sm btn-danger"
                                                x-on:click.prevent="
                              if (confirm('Are you sure you want to delete this role?')) {
                                $wire.deleteRole({{ $role->id }})
                              }"
                                                aria-label="Delete Role" title="Delete Role">
                                                <i class="bx bx-trash"></i>
                                            </button>

                                            <a wire:navigate
                                                href="{{ route('admin.rolepermission.assign', ['id' => $role->id]) }}"
                                                class="btn btn-sm btn-secondary" aria-label="Edit Permissions"
                                                title="Edit Permissions">
                                                <i class="bx bx-key"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted fst-italic py-3">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3 mb-3">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    @endif



</div>
