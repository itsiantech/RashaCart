<form action="{{ route('profile.update', Auth::user()->id ) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Name -->
        <div class="mb-3 col-md-6">
            <label for="firstName" class="form-label">Your Name</label>
            <input
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                id="firstName"
                name="name"
                value="{{ old('name', $user->name) }}"
                autofocus
            />
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3 col-md-6">
            <label for="email" class="form-label">E-mail</label>
            <input
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                placeholder="john.doe@example.com"
            />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Phone -->
        <div class="mb-3 col-md-6">
            <label for="phoneNumber" class="form-label">Phone Number</label>
            <div class="input-group input-group-merge">                        
                <input
                    type="text"
                    id="phoneNumber"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $user->phone) }}"
                />
            </div>
            @error('phone')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Address -->
        <div class="mb-3 col-md-6">
            <label for="address" class="form-label">Address</label>
            <input
                type="text"
                class="form-control @error('address') is-invalid @enderror"
                id="address"
                name="address"
                value="{{ old('address', $user->address) }}"
                placeholder="Address"
            />
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="profile_image" class="form-label">Profile Picture</label>
            <input
                type="file"
                class="form-control @error('profile_image') is-invalid @enderror"
                id="profile_image"
                name="profile_image"                           
              
            />
            @error('profile_image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mt-2">
        <button type="submit" class="btn btn-primary me-2">Save changes</button>
    </div>
</form>