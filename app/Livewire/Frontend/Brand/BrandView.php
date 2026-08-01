<?php

namespace App\Livewire\Frontend\Brand;

use App\Models\Brand;
use App\Traits\HandlesCartActions;
use Livewire\Component;
use Livewire\WithPagination;

class BrandView extends Component
{
    use HandlesCartActions;
    use WithPagination;

    public $brand = null;

    public $selectedPrice = null;

    protected $queryString = [
        'selectedPrice' => ['except' => null],
    ];

    public function mount($slug = null)
    {
        if ($slug) {
            $this->brand = Brand::where('slug', $slug)->first();
        }
    }

    public function toggolePrice($price)
    {
        $this->selectedPrice = ($this->selectedPrice === $price) ? null : $price;
    }

    public function getBrandsProperty()
    {
        $query = Brand::query();

        if ($this->brand) {
            $query->where('category_id', $this->brand->id);
        }

        if ($this->selectedPrice) {
            [$min, $max] = explode('-', $this->selectedPrice);
            $query->whereBetween('price', [(int) $min, (int) $max]);
        }

        return $query->paginate(16);
    }

    public function render()
    {
        return view('livewire.frontend.brand.brand-view',
            [
                'brands' => $this->brands,
            ])->layout('livewire.frontend.layouts.app');
    }
}
