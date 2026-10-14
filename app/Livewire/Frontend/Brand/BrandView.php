<?php

namespace App\Livewire\Frontend\Brand;

use App\Models\Brand;
use Livewire\Component;

class BrandView extends Component
{
    public $search = '';

    public $perPage = 18;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount($slug = null)
    {
        // $slug ignored: this page always lists all active brands.
    }

    public function loadMore(): void
    {
        $this->perPage += 18;
    }

    public function updatedSearch(): void
    {
        $this->perPage = 18;
    }

    public function getBrandsProperty()
    {
        return Brand::query()
            ->where('is_active', 1)
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.trim($this->search).'%'))
            ->withCount(['products' => fn ($q) => $q->where('status', true)])
            ->orderBy('name')
            ->take($this->perPage)
            ->get();
    }

    public function getTotalCountProperty(): int
    {
        return Brand::where('is_active', 1)
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.trim($this->search).'%'))
            ->count();
    }

    public function render()
    {
        return view('livewire.frontend.brand.brand-view', [
            'brands' => $this->brands,
            'totalCount' => $this->totalCount,
        ])->layout('livewire.frontend.layouts.store');
    }
}
