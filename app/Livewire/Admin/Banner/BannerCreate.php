<?php

namespace App\Livewire\Admin\Banner;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Banner;
use Storage;
use DB;
class BannerCreate extends Component
{
    use WithFileUploads;

    public $title;
    public $image;
    public $description;
    public $link;
    public $type;
    public $status = true;

    public function store()
    {
        $this->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'image|max:3072',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'type' => 'required|string',
            'status' => 'boolean',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'link' => $this->link,
            'type' => $this->type,
            'status' => $this->status,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('banners', 'public');
        }

        Banner::create($data);

        // $this->dispatch('toast', type: 'success', message: 'Banner has been created!.');
        $this->dispatch('success', message: 'Banner Created!');
        return redirect()->route('admin.banner.index');
    }

    public function render()
    {
        return view('livewire.admin.banner.banner-create')->layout('livewire.admin.layouts.app');
    }
}
