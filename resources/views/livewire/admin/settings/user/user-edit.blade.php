<div class="container max-w-6xl mx-auto my-4">
  <!-- Header Section -->
  <div class="mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-center px-3 px-sm-4 gap-2">
    <div>
      <h3 class="h5 text-dark">
        Edit Admin User
      </h3>
      <p class="text-muted mb-0">
        Update admin user details and role
      </p>
    </div>

    <!-- Back Button -->
    <div class="mt-3 mt-sm-0">
      <a wire:navigate href="{{ route('admin.user.index') }}" class="btn btn-link text-decoration-none d-flex align-items-center gap-1">
        <i class="fa fa-arrow-left"></i>
        <span>Back</span>
      </a>
    </div>
  </div>

  <!-- Form Section -->
  <form wire:submit.prevent="update" class="px-3 px-sm-4">
    <div class="row g-3">
      <div class="col-12 col-sm-6">
        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input 
          type="text" 
          id="name" 
          class="form-control @error('name') is-invalid @enderror" 
          placeholder="Enter full name" 
          wire:model="name" 
          required
        >
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-12 col-sm-6">
        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
        <input 
          type="email" 
          id="email" 
          class="form-control @error('email') is-invalid @enderror" 
          placeholder="Enter email address" 
          wire:model="email" 
          required
        >
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-12 col-sm-6">
        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
        <input 
          type="text" 
          id="phone" 
          class="form-control @error('phone') is-invalid @enderror" 
          placeholder="Enter phone number" 
          wire:model="phone" 
          required
        >
        @error('phone')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Password Fields -->
    <div class="row g-3 mt-2">
      <div class="col-12 col-sm-6">
        <label for="password" class="form-label">New Password (Leave blank to keep current)</label>
        <input 
          type="password" 
          id="password" 
          class="form-control @error('password') is-invalid @enderror" 
          placeholder="Enter new password" 
          wire:model="password"
        >
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-12 col-sm-6">
        <label for="password_confirmation" class="form-label">Confirm New Password</label>
        <input 
          type="password" 
          id="password_confirmation" 
          class="form-control @error('password_confirmation') is-invalid @enderror" 
          placeholder="Confirm new password" 
          wire:model="password_confirmation"
        >
        @error('password_confirmation')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Roles Selection -->
    <div class="mt-3">
      <label class="form-label">Role <span class="text-danger">*</span></label>
      <div>
        @foreach($roles as $role)
          <div class="form-check form-check-inline">
            <input 
              class="form-check-input" 
              type="radio" 
              id="role_{{ $role->id }}" 
              wire:model="selectedRole" 
              value="{{ $role->name }}"
              required
            >
            <label class="form-check-label" for="role_{{ $role->id }}">
              {{ $role->name }}
            </label>
          </div>
        @endforeach
      </div>
      @error('selectedRole')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    <!-- Submit Button -->
    <div class="pt-4">
      <button 
        type="submit" 
        class="btn btn-primary w-100 w-sm-auto"
        wire:loading.attr="disabled"
      >
        Update Admin
      </button>
    </div>
  </form>
</div>
