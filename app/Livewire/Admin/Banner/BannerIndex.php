<?php

namespace App\Livewire\Admin\Banner;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class BannerIndex extends Component
{

    public $banners;
    public function render()
    {
        $this->banners = Banner::latest()->get();
        return view('livewire.admin.banner.banner-index')->layout('livewire.admin.layouts.app');
    }

    public function delete($id)
    {
        $banner = Banner::findOrFail($id);
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();

       $this->dispatch('success', message: 'Banner Deleted!');
    }
}
