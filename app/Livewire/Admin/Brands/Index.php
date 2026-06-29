<?php

namespace App\Livewire\Admin\Brands;

use App\Models\Brand;
use Illuminate\Container\Attributes\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public function mount()
    {
        // $this->brands = Brand::paginate($this->perPage);
        // FacadesLog::info('Brands property set in mount', ['type' => gettype($this->brands), 'class' => get_class($this->brands)]);
    }
    // Pagination & Sorting
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Selection
    public $selectedBrands = [];
    public $selectAll = false;

    // Form State
    public $showForm = false;
    public $isEdit = false;
    public $brand_id = null;

    // Form Fields
    public $name = '';
    public $slug = '';
    public $description = '';
    public $logo;
    public $website = '';
    public $is_active = true;
    public $meta_title = '';
    public $meta_description = '';
    public $sort_order = 0;
    public $current_logo_path = null;

    // Constants
    protected const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/bmp',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    protected const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'bmp',
        'webp',
        'svg',
        'pdf',
        'doc',
        'docx'
    ];

    protected $listeners = [
        'refreshBrands' => '$refresh',
        'deleteConfirmed' => 'deleteBrands'
    ];

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'name')->ignore($this->brand_id)
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'slug')->ignore($this->brand_id)
            ],
            'description' => 'nullable|string',
            'logo' => $this->getLogoValidationRules(),
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'logo.image' => 'The logo must be an image file.',
            'logo.max' => 'The logo may not be greater than 5MB.',
            'logo.mimes' => 'The logo must be a file of type: jpg, jpeg, png, gif, bmp, webp, svg, pdf, doc, docx.',
            'website.url' => 'Please enter a valid website URL.',
        ];
    }

    private function getLogoValidationRules()
    {
        return [
            'nullable',
            function ($attribute, $value, $fail) {
                if ($value === 'delete' || $value === null) {
                    return;
                }

                if (!($value instanceof \Illuminate\Http\UploadedFile)) {
                    $fail('Invalid file uploaded.');
                }
            },
            'file',
            'max:5120', // 5MB
            'mimes:' . implode(',', self::ALLOWED_EXTENSIONS),
        ];
    }

    /**
     * Generate slug from name automatically
     */
    public function updatedName($value)
    {
        if (!$this->isEdit && !$this->slug) {
            $this->slug = Str::slug($value);
        }
    }

    /**
     * Toggle select all brands
     */
    public function updatedSelectAll($value)
    {
        $this->selectedBrands = $value
            ? $this->brands->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    /**
     * Sort table by column
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Show add brand form
     */
    public function showAddForm()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEdit = false;
    }

    /**
     * Show edit brand form
     */
    public function showEditForm($id)
    {
        $brand = Brand::findOrFail($id);

        $this->hydrateFormFromBrand($brand);

        $this->showForm = true;
        $this->isEdit = true;
    }

    /**
     * Save brand (create or update)
     */
    public function saveBrand()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $brand = $this->isEdit
                ? Brand::findOrFail($this->brand_id)
                : new Brand();

            $oldLogoPath = $brand->logo;
            $newLogoPath = $this->handleLogoUpload($oldLogoPath);

            $this->updateBrandAttributes($brand, $newLogoPath);
            $brand->save();

            $this->cleanupOldLogo($oldLogoPath, $newLogoPath);

            DB::commit();

            $this->dispatchSuccessMessage(
                $this->isEdit ? 'Brand updated successfully!' : 'Brand created successfully!'
            );

            $this->resetForm();
            $this->dispatch('refreshBrands');
        } catch (\Exception $e) {
            FacadesLog::error('Brand save failed: ' . $e->getMessage());
            DB::rollBack();
            $this->handleSaveError($e, $newLogoPath ?? null);
        }
    }


    public function deleteBrands($ids)
    {
        // If $ids is an array with a nested structure, extract it
        if (is_array($ids) && isset($ids['ids'])) {
            $ids = $ids['ids'];
        }

        // Ensure $ids is an array
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        try {
            DB::transaction(function () use ($ids) {
                $brands = Brand::whereIn('id', $ids)->get();

                foreach ($brands as $brand) {
                    $this->deleteBrandLogo($brand);
                    $brand->delete();
                }
            });

            $this->resetSelection();

            $this->dispatchSuccessMessage(
                count($ids) . ' brand(s) deleted successfully.'
            );

            $this->dispatch('refreshBrands');
        } catch (\Exception $e) {
            $this->dispatchErrorMessage('Failed to delete brand(s): ' . $e->getMessage());
        }
    }

    /**
     * Toggle brand active status
     */
    public function toggleStatus($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->update(['is_active' => !$brand->is_active]);

            $this->dispatchSuccessMessage('Brand status updated successfully.');
        } catch (\Exception $e) {
            $this->dispatchErrorMessage('Failed to update brand status.');
        }
    }

    /**
     * Cancel form and reset
     */
    public function cancelForm()
    {
        $this->resetForm();
    }

    /**
     * Render the component
     */
    public function render()
    {
        $brands = Brand::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);


        return view('livewire.admin.brands.index',['brands' => $brands,])->layout('livewire.admin.layouts.app');
    }

    /*****************************************************************
     * PRIVATE HELPER METHODS
     *****************************************************************/

    /**
     * Get filtered and sorted brands query
     */
    private function getBrandsQuery()
    {
        return Brand::withCount('products')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Hydrate form from brand model
     */
    private function hydrateFormFromBrand(Brand $brand)
    {
        $this->brand_id = $brand->id;
        $this->name = $brand->name;
        $this->slug = $brand->slug;
        $this->description = $brand->description;
        $this->is_active = $brand->is_active;
        $this->sort_order = $brand->sort_order;
        $this->current_logo_path = $brand->logo;
        $this->logo = null;
    }

    /**
     * Handle logo upload and return new path
     */
    private function handleLogoUpload($oldLogoPath)
    {
        // Delete logo if requested
        if ($this->logo === 'delete') {
            return null;
        }

        // Keep old logo if no new file uploaded
        if (!$this->logo || !($this->logo instanceof \Illuminate\Http\UploadedFile)) {
            return $oldLogoPath;
        }

        $file = $this->logo;

        // Validate file type
        $this->validateFileType($file);

        // Generate safe filename
        $filename = $this->generateSafeFilename($file);

        // Store file
        return $file->storeAs('brands', $filename, 'public');
    }

    /**
     * Validate uploaded file type
     */
    private function validateFileType($file)
    {
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            throw new \Exception("File type '{$mimeType}' is not allowed.");
        }

        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new \Exception("File extension '{$extension}' is not allowed.");
        }
    }

    /**
     * Generate safe filename
     */
    private function generateSafeFilename($file)
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension());
        $safeName = Str::slug($originalName) . '-' . uniqid() . '.' . $extension;

        return $safeName;
    }

    /**
     * Update brand attributes
     */
    private function updateBrandAttributes($brand, $logoPath)
    {
        $brand->name = $this->name;
        $brand->slug = $this->slug;
        $brand->description = $this->description;
        $brand->is_active = $this->is_active;
        $brand->sort_order = $this->sort_order;
        $brand->logo = $logoPath;
    }

    /**
     * Delete old logo if replaced
     */
    private function cleanupOldLogo($oldPath, $newPath)
    {
        if ($oldPath && $oldPath !== $newPath) {
            $this->safeDeleteFile($oldPath);
        }
    }

    /**
     * Safely delete file from storage
     */
    private function safeDeleteFile($path)
    {
        try {
            if ($path && Storage::disk('public')->exists($path)) {
                // Additional security: ensure we're only deleting from brands directory
                if (strpos($path, 'brands/') === 0) {
                    Storage::disk('public')->delete($path);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to delete file: ' . $path, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete brand logo
     */
    private function deleteBrandLogo($brand)
    {
        if ($brand->logo) {
            $this->safeDeleteFile($brand->logo);
        }
    }

    /**
     * Handle save error and cleanup
     */
    private function handleSaveError($exception, $newLogoPath = null)
    {
        // Cleanup uploaded file if transaction failed
        if ($newLogoPath && Storage::disk('public')->exists($newLogoPath)) {
            $this->safeDeleteFile($newLogoPath);
        }

        $this->dispatchErrorMessage(
            'Error saving brand: ' . $exception->getMessage()
        );
    }

    /**
     * Reset form fields
     */
    private function resetForm()
    {
        $this->reset([
            'name',
            'slug',
            'description',
            'logo',
            'website',
            'is_active',
            'meta_title',
            'meta_description',
            'sort_order',
            'brand_id',
            'isEdit',
            'showForm',
            'current_logo_path'
        ]);

        $this->is_active = true;
        $this->sort_order = 0;
        $this->logo = null;
    }

    /**
     * Reset selection state
     */
    private function resetSelection()
    {
        $this->selectedBrands = [];
        $this->selectAll = false;
    }

    /*****************************************************************
     * NOTIFICATION HELPERS
     *****************************************************************/

    private function dispatchSuccessMessage($message)
    {
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    private function dispatchErrorMessage($message)
    {
        $this->dispatch('show-toast', [
            'type' => 'error',
            'message' => $message
        ]);
    }

    private function dispatchWarningMessage($message)
    {
        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => $message
        ]);
    }
}
