<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.banner.index') }}">Banner</a>
            </li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Edit Banner</h5>
            <div class="col-12 col-md-auto ms-md-auto text-md-end">
                <a href="{{ route('admin.banner.index') }}" wire:navigate
                    class="btn btn-primary btn-sm d-inline-flex align-items-center">
                    <i class='bx bx-arrow-back'></i> Go back
                </a>
            </div>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="update" enctype="multipart/form-data">
                @csrf
                {{-- Title --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="title">Title</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model.defer="title" id="title"
                            class="form-control @error('title') is-invalid @enderror" placeholder="Banner Title">
                        @error('title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="description">Description</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model.defer="description" id="description"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Banner description">
                        @error('description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Link --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="link">Link</label>
                    <div class="col-sm-10">
                        <input type="url" wire:model.defer="link" id="link"
                            class="form-control @error('link') is-invalid @enderror" placeholder="https://example.com">
                        @error('link')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Image --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="image">Image <span
                            class="text-danger h3">*</span></label>
                    <div class="col-sm-10">
                        <input type="file" wire:model="image" id="image"
                            class="form-control @error('image') is-invalid @enderror">
                        @error('image')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <span class="text-muted small text-success"> ( Recommended size: 1360x250px )</span>

                        {{-- Preview --}}
                        <div class="mt-2">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview" style="max-height: 100px;">
                            @elseif ($oldImage)
                                <img src="{{ asset('storage/' . $oldImage) }}" alt="Banner Image"
                                    style="max-height: 100px;">
                            @endif
                        </div>

                        {{-- Spinner --}}
                        <div wire:loading wire:target="image" class="mt-2">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Type --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="type">Type <span
                            class="text-danger h3">*</span></label>
                    <div class="col-sm-10">
                        <select wire:model.defer="type" id="type"
                            class="form-select @error('type') is-invalid @enderror">
                            <option value="banner">Banner</option>
                            <option value="popup">Pop Up</option>
                            <option value="ads">Advertisment</option>
                        </select>
                        @error('type')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="status">
                        Status <span class="text-danger h3">*</span>
                    </label>
                    <div class="col-sm-10 d-flex align-items-center">
                        <div class="form-check form-switch">
                            <input type="checkbox" wire:model="status" id="status" class="form-check-input">
                            <label class="form-check-label" for="status">
                                Active
                            </label>
                        </div>
                    </div>
                </div>


                {{-- Submit --}}
                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Update Banner</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
