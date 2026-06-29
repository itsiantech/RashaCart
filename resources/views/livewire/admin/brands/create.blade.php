<!-- resources/views/livewire/admin/brands/create.blade.php -->
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create New Brand</h2>
            <p class="text-gray-600">Add a new product brand to your store</p>
        </div>
        <a href="{{ route('admin.brand.index') }}"
            class="text-blue-600 hover:text-blue-900">
            ← Back to Brands
        </a>
    </div>

    <!-- Form -->
    <div class="max-w-4xl mx-auto">
        <form wire:submit.prevent="save" class="space-y-6 bg-white rounded-lg shadow p-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Brand Name *</label>
                <input type="text"
                    id="name"
                    wire:model.live="name"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Slug -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="slug">Slug</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="slug" wire:model.live="slug" placeholder="Auto-generated slug" readonly>
                    @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description"
                    wire:model="description"
                    rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Brief description about the brand..."></textarea>
                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Logo Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Brand Logo</label>
                <div class="mt-1 flex items-center">
                    @if($logoPreview)
                    <img src="{{ $logoPreview }}" alt="Logo preview" class="h-20 w-20 object-contain border rounded">
                    @else
                    <div class="h-20 w-20 border-2 border-dashed border-gray-300 rounded flex items-center justify-center">
                        <span class="text-gray-400 text-sm">No logo</span>
                    </div>
                    @endif
                    <div class="ml-4">
                        <input type="file"
                            id="logo"
                            wire:model="logo"
                            accept="image/*"
                            class="hidden">
                        <label for="logo"
                            class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Upload Logo
                        </label>
                        @if($logo)
                        <button type="button"
                            wire:click="$set('logo', null)"
                            class="ml-3 text-sm text-red-600 hover:text-red-900">
                            Remove
                        </button>
                        @endif
                        <p class="text-xs text-gray-500 mt-2">PNG, JPG up to 2MB</p>
                        @error('logo') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input type="checkbox"
                        id="is_active"
                        wire:model="is_active"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active (Visible to customers)
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-5 border-t border-gray-200">
                <div class="flex justify-end">
                    <a href="{{ route('admin.brand.index') }}"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Brand
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>