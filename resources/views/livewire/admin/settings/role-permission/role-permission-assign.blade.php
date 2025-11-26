<div>
  <div class="bg-white dark:bg-gray-900 shadow rounded p-4 mt-2 mb-5">
    @if (session()->has('message'))
      <div class="alert alert-success mb-4">
        {{ session('message') }}
      </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="h5 text-dark dark:text-light mb-0">
        Edit Role's Permission
      </h3>
      <a href="{{ route('admin.rolepermission.index') }}" class="btn btn-danger btn-sm">
        <i class="bx bx-arrow-back"></i> Back
      </a>
    </div>

    <form wire:submit.prevent="updatePermission">
      <div class="mb-4">
        <div class="fw-semibold text-dark dark:text-light">
          Role Name: 
          <span class="text-success dark:text-success fw-semibold">
            {{ $currentRole->name }}
          </span>
        </div>
      </div>

      <hr class="mb-4">

      @foreach($permission_groups as $group)
        @php
          $permissions = \App\Models\User::getpermissionByGroupName($group->group_name);
        @endphp

        <div class="row mb-4">
          <div class="col-md-3 d-flex align-items-center">
            <div class="form-check">
              <input 
                type="checkbox"
                wire:click="toggleGroupPermissions('{{ $group->group_name }}')"
                @checked($this->groupChecked($group->group_name))
                class="form-check-input"
                id="group_{{ Str::slug($group->group_name) }}"
              >
              <label class="form-check-label fw-semibold text-dark dark:text-light" for="group_{{ Str::slug($group->group_name) }}">
                {{ $group->group_name }}
              </label>
            </div>
          </div>

          <div class="col-md-9 d-flex flex-wrap gap-3">
            @foreach($permissions as $permission)
              <div class="form-check me-3 mb-2">
                <input 
                  type="checkbox"
                  wire:model="selectedPermissions"
                  value="{{ $permission->name }}"
                  id="perm_{{ $permission->id }}"
                  class="form-check-input"
                >
                <label class="form-check-label text-dark dark:text-light" for="perm_{{ $permission->id }}">
                  {{ $permission->name }}
                </label>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach

      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary btn-sm mt-2">
          Assign permission
        </button>
      </div>
    </form>
  </div>
</div>
