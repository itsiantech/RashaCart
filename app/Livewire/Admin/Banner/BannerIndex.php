<?php

namespace App\Livewire\Admin\Banner;

use Livewire\Component;
use App\Models\Banner;
use Storage;

class BannerIndex extends Component
{
    protected $listeners = ['delete' => 'delete'];
    public $banners;
    public function render()
    {
        $this->banners = Banner::latest()->get();
        return view('livewire.admin.banner.banner-index')->layout('livewire.admin.layouts.app');
    }

    public function delete($id)
    {
        if (!auth()->user()->can('banner.delete')) {
            $this->dispatch('toast', type: 'error', message: 'You do not have permission to delete banners!');
            return;
        }
        $banner = Banner::findOrFail($id);
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();

       $this->dispatch('success', message: 'Banner Deleted!');
        return;
    }
}
