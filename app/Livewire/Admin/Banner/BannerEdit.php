<?php

namespace App\Livewire\Admin\Banner;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerEdit extends Component
{
    use WithFileUploads;
    public $bannerId;
    public $title;
    public $image;
    public $oldImage;
    public $description;
    public $link;
    public $type;
    public $status = true;
    public function mount($id)
    {
        $banner = Banner::findOrFail($id);
        $this->bannerId = $banner->id;
        $this->title = $banner->title;
        $this->oldImage = $banner->image;
        $this->oldImage = $banner->image;
        $this->description = $banner->description;
        $this->link = $banner->link;
        $this->type = $banner->type;
        $this->status = (bool) $banner->status;
    }
    public function update()
    {
        if (!auth()->user()->can('banner.update')) {
            $this->dispatch('toast', type: 'error', message: 'You do not have permission to update banners!');
            return;
        }
        $this->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:1028',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'type' => 'required|string',
            'status' => 'boolean',
        ]);

        $banner = Banner::findOrFail($this->bannerId);
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'link' => $this->link,
            'type' => $this->type,
            'status' => $this->status,
        ];

        if ($this->image) {
            if ($this->oldImage) {
                Storage::disk('public')->delete($this->oldImage);
            }
            $data['image'] = $this->image->store('banners', 'public');
        }

        $banner->update($data);

        // $this->dispatch('toast', type: 'success', message: 'Banner has been updated successfully.');
       $this->dispatch('success', message: 'Banner Updated!');
        return redirect()->route('admin.banner.index');
    }
    public function render()
    {
        return view('livewire.admin.banner.banner-edit')->layout('livewire.admin.layouts.app');
    }
}
