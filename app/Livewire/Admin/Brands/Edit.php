<?php

namespace App\Livewire\Admin\Brands;

use App\Models\Brand;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Brand $brand;

    public $name;

    public $description;

    public $logo;

    public $is_active;

    public $logoPreview;

    public $oldLogo;

    protected $rules = [
        'name' => 'required|min:2|max:255|unique:brands,name,{brand->id}',
        'description' => 'nullable|string|max:1000',
        'logo' => 'nullable|image|max:2048',
        'is_active' => 'boolean',
    ];

    public function mount(Brand $brand)
    {
        $this->brand = $brand;
        $this->name = $brand->name;
        $this->description = $brand->description;
        $this->is_active = $brand->is_active;
        $this->oldLogo = $brand->logo;
        $this->logoPreview = $brand->logo_url;
    }

    public function updatedLogo()
    {
        $this->validate([
            'logo' => 'image|max:2048',
        ]);

        if ($this->logo) {
            $this->logoPreview = $this->logo->temporaryUrl();
        }
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:2|max:255|unique:brands,name,'.$this->brand->id,
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        try {
            $data = [
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'is_active' => $this->is_active,
            ];

            if ($this->logo) {
                // Delete old logo if exists
                if ($this->oldLogo && file_exists(storage_path('app/public/'.$this->oldLogo))) {
                    unlink(storage_path('app/public/'.$this->oldLogo));
                }
                $data['logo'] = $this->logo->store('brands', 'public');
            }

            $this->brand->update($data);

            session()->flash('success', 'Brand updated successfully.');

            return redirect()->route('admin.brand.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update brand: '.$e->getMessage());
        }
    }

    public function removeLogo()
    {
        if ($this->oldLogo && file_exists(storage_path('app/public/'.$this->oldLogo))) {
            unlink(storage_path('app/public/'.$this->oldLogo));
        }

        $this->brand->update(['logo' => null]);
        $this->logoPreview = null;
        $this->oldLogo = null;
        $this->logo = null;

        session()->flash('info', 'Logo removed successfully.');
    }

    public function render()
    {
        return view('livewire.admin.brand.edit');
    }
}
