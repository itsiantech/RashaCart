<div class="container my-4">
  <!-- Header Section -->
  <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 px-3 px-sm-0 gap-2">
    <div>
      <h3 class="h5 text-dark dark:text-white">
        Add New Admin
      </h3>
      <p class="text-muted mb-0">
        Create a new admin user with a specific role
      </p>
    </div>

    <!-- Back Button -->
    <div class="mt-3 mt-sm-0">
      <a wire:navigate href="{{ route('admin.user.index') }}" class="text-secondary text-decoration-none d-flex align-items-center gap-1 hover-text-primary">
        <i class="bx bx-arrow-back fs-5"></i>
        <span>Back</span>
      </a>
    </div>
  </div>

  <!-- Form Section -->
  <div class="px-3 px-sm-0">
    <form wire:submit.prevent="save">
      <!-- Name & Email Fields -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6">
          <label for="name" class="form-label">Full Name</label>
          <input 
            type="text" 
            id="name" 
            wire:model="name" 
            class="form-control" 
            placeholder="Enter full name" 
            required
          />
          @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-12 col-sm-6">
          <label for="phone" class="form-label">Full Name</label>
          <input 
            type="text" 
            id="phone" 
            wire:model="phone" 
            class="form-control" 
            placeholder="017XXXXXXXX" 
            required
          />
          @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-12 col-sm-6">
          <label for="email" class="form-label">Email Address</label>
          <input 
            type="email" 
            id="email" 
            wire:model="email" 
            class="form-control" 
            placeholder="Enter email address" 
            required
          />
          @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
      </div>

      <!-- Password Fields -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6">
          <label for="password" class="form-label">Password</label>
          <input 
            type="password" 
            id="password" 
            wire:model="password" 
            class="form-control" 
            placeholder="Enter password" 
            required
          />
          {{-- @error('password') <small class="text-danger">{{ $message }}</small> @enderror --}}
        </div>

        <div class="col-12 col-sm-6">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input 
            type="password" 
            id="password_confirmation" 
            wire:model="password_confirmation" 
            class="form-control" 
            placeholder="Confirm password" 
            required
          />
        </div>
      </div>

      <!-- Roles Selection -->
      <div class="mb-3">
        <label class="form-label">Role *</label>
        <div>
          @foreach($roles as $role)
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="radio" 
                name="selectedRole" 
                id="role_{{ $role->name }}" 
                wire:model="selectedRole" 
                value="{{ $role->name }}"
              >
              <label class="form-check-label" for="role_{{ $role->name }}">
                {{ $role->name }}
              </label>
            </div>
          @endforeach
        </div>
        @error('selectedRole') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <!-- Submit Button -->
      <div>
        <button 
          type="submit" 
          class="btn btn-primary w-100 w-sm-auto d-flex justify-content-center align-items-center"
          wire:loading.attr="disabled"
          wire:target="save"
        >
          Create Admin
        </button>
      </div>
    </form>
  </div>
</div>
