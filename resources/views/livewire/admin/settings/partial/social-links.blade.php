<div>    
    <div class="card-header bg-primary text-white mb-3">Social Links</div>
    <form wire:submit.prevent="update">
        
        <div class="row mb-3">
            <!-- Facebook -->
            <div class="col-md-6">
                <label for="facebook" class="form-label">
                    <i class="bx bxl-facebook me-2"></i>Facebook URL
                </label>
                <input type="url" class="form-control" id="facebook" wire:model.defer="facebook" placeholder="https://">
                @error('facebook') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Twitter -->
            <div class="col-md-6">
                <label for="twitter" class="form-label">
                    <i class="bx bxl-twitter me-2"></i>Twitter URL
                </label>
                <input type="url" class="form-control" id="twitter" wire:model.defer="twitter" placeholder="https://">
                @error('twitter') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="row mb-3">
            <!-- LinkedIn -->
            <div class="col-md-6">
                <label for="linkedin" class="form-label">
                    <i class="bx bxl-linkedin me-2"></i>LinkedIn URL
                </label>
                <input type="url" class="form-control" id="linkedin" wire:model.defer="linkedin" placeholder="https://">
                @error('linkedin') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Instagram -->
            <div class="col-md-6">
                <label for="instagram" class="form-label">
                    <i class="bx bxl-instagram me-2"></i>Instagram URL
                </label>
                <input type="url" class="form-control" id="instagram" wire:model.defer="instagram" placeholder="https://">
                @error('instagram') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="row mb-3">
            <!-- YouTube -->
            <div class="col-md-6">
                <label for="youtube" class="form-label">
                    <i class="bx bxl-youtube me-2"></i>YouTube URL
                </label>
                <input type="url" class="form-control" id="youtube" wire:model.defer="youtube" placeholder="https://">
                @error('youtube') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Save Changes</button>           
        </div>

    </form>
</div>
