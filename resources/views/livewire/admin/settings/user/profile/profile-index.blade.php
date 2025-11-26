<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">Profile Details</h5>
            <!-- Account -->
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                    <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/admin/img/avatars/user-avater.png') }}"
                        alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                    <div class="button-wrapper">
                        <h2>{{ Auth::user()->name }}</h2>
                        <p class="text-muted mb-0">
                            Role: {{ Auth::user()->getRoleNames()->join(', ') }}
                        </p>

                        <p class="text-muted mb-0">{{ Auth::user()->address }}</p>
                    </div>
                </div>
            </div>

            <hr class="my-0" />
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <form wire:submit.prevent="updateProfile" enctype="multipart/form-data">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Your Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model.defer="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model.defer="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" class="form-control @error('profile_image') is-invalid @enderror"
                                    wire:model="profile_image">
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </form>

                    </div>
                    <div class="col-12 col-md-6">
                        <section>
                            <h5 class="card-title text-primary">Update Password</h5>
                            <p class="text-dark mb-2">
                                Ensure your account is using a long, random password to stay secure.
                            </p>

                            <form wire:submit.prevent="updatePassword">
                                <!-- Current Password -->
                                <input class="form-control mb-2" type="password" wire:model.defer="current_password"
                                    placeholder="Current Password">
                                @error('current_password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <!-- New Password -->
                                <input class="form-control mb-2" type="password" wire:model.defer="password"
                                    placeholder="New Password">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <!-- Confirm New Password -->
                                <input class="form-control mb-2" type="password"
                                    wire:model.defer="password_confirmation" placeholder="Confirm New Password">
                                @error('password_confirmation')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <!-- Submit Button -->
                                <button class="btn btn-primary w-100" type="submit">Update Password</button>
                            </form>


                        </section>

                    </div>
                </div>
            </div>
            <!-- /Account -->
        </div>

        @if (!Auth::user()->hasRole('super-admin'))
            <div class="card">
                <h5 class="card-header">Delete Account</h5>
                <div class="card-body">

                    <div class="mb-3 col-12 mb-0">
                        <div class="alert alert-warning">
                            <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
                            <p class="mb-0">Once you delete your account, there is no going back. Please be certain.
                            </p>
                        </div>
                    </div>
                    <form wire:submit.prevent="deleteAccount">
                        <input type="checkbox" wire:model="accountActivation">
                        <label>I confirm my account deactivation</label>

                        <input type="password" wire:model.defer="delete_password" placeholder="Enter your password">
                        @error('delete_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <button type="submit" class="btn btn-danger">Deactivate Account</button>
                    </form>


                </div>
            </div>
        @endif

    </div>
</div>
