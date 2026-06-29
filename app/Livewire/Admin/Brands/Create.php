<?php

namespace App\Livewire\Admin\Brands;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Brand;
use Illuminate\Support\Str;

class Create extends Component
{
    use WithFileUploads;

    public $slug;
    public $name;
    public $description;
    public $logo;
    public $is_active = true;
    public $logoPreview;

    protected $rules = [
        'name' => 'required|min:2|max:255|unique:brands,name',
        'description' => 'nullable|string|max:1000',
        'logo' => 'nullable|image|max:2048',
        'is_active' => 'boolean',
    ];

    public function updatedLogo()
    {
        $this->validate([
            'logo' => 'image|max:20048',
        ]);
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            $logoPath = null;
            if ($this->logo && !is_string($this->logo)) {
                $logoPath = $this->logo->store('brands', 'public');
            }

            Brand::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'logo' => $logoPath,
                'is_active' => $this->is_active,
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Brand created successfully!'
            ]);
            
            return $this->redirect(route('admin.brand.index'), navigate: true);
            
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to create brand: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.brands.create')->layout('livewire.admin.layouts.app');
    }
}
