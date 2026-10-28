<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class PageIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.pages.page-index', [
            'pages' => Page::withTrashed()->latest()->get(),
        ])->layout('livewire.admin.layouts.app');
    }

    public function toggleStatus($id)
    {
        $page = Page::findOrFail($id);
        $page->status = ! $page->status;
        $page->save();

        $this->dispatch('success', message: 'Status updated!');
    }

    public function delete($id)
    {
        Page::findOrFail($id)->delete();

        $this->dispatch('success', message: 'Page moved to trash!');
    }

    public function restore($id)
    {
        Page::withTrashed()->findOrFail($id)->restore();

        $this->dispatch('success', message: 'Page restored!');
    }

    public function forceDelete($id)
    {
        $page = Page::withTrashed()->findOrFail($id);

        $this->deleteImages($page);

        $page->forceDelete();

        $this->dispatch('success', message: 'Page permanently deleted!');
    }

    private function deleteImages(Page $page): void
    {
        $disk = Storage::disk('public');

        if ($page->feature_image) {
            $disk->delete($page->feature_image);
        }

        foreach ($page->blocks ?? [] as $block) {
            if (! empty($block['image'])) {
                $disk->delete($block['image']);
            }

            foreach ($block['columns'] ?? [] as $column) {
                if (! empty($column['image'])) {
                    $disk->delete($column['image']);
                }
            }
        }
    }
}
