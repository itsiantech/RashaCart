<?php

namespace App\Livewire\Frontend\Pages;

use App\Models\Page;
use Illuminate\Support\Str;
use Livewire\Component;

class PublicPage extends Component
{
    public $slug;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $page = Page::active()->where('slug', $this->slug)->firstOrFail();

        return view('livewire.frontend.pages.public-page', [
            'page' => $page,
            'blocks' => $page->blocks ?? [],
        ])->layout('livewire.frontend.layouts.app', [
            // Tab title uses the meta title; social previews use the SEO title.
            'meta' => ['title' => $page->meta_title ?: $page->title],
            'meta_title' => $page->seo_title ?: $page->meta_title ?: $page->title,
            'meta_description' => Str::limit(strip_tags($page->description ?? ''), 155),
            'og_image' => $page->feature_image ? asset('storage/'.$page->feature_image) : null,
        ]);
    }
}
