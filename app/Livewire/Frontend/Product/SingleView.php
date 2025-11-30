<?php

namespace App\Livewire\Frontend\Product;

use Livewire\Component;
use App\Models\Review;
use App\Traits\HandlesCartActions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
class SingleView extends Component
{
    use HandlesCartActions, WithPagination;

    public $relatedProducts;
    public $product;

    
    public $selectedVariant = null;
    public $selectedColour = null;
    public $selectedSize = null;

    // সেটিংস কন্ট্রোল করবে কোন সিস্টেম অন আছে
    public bool $enableVariant = true;
    public bool $enableColor = true;
    public bool $enableSize = true;

   
    public function mount($slug)
    {
        $allProducts = Cache::get('products', collect());

        $this->product = $allProducts->where('slug', $slug)->firstOrFail();

        $this->relatedProducts = $allProducts->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->take(5);

       
    }    
    

    public function selectVariant($variantId)
    {
        $this->selectedVariant = $variantId;
        // $this->dispatch('success', message: 'Variant selected!');
    }

    public function selectColor($color)
    {
        $this->selectedColour = $color;
    }

    public function selectSize($size)
    {
        $this->selectedSize = $size;
    }


   
    public $rating = 0;
    public $comment = '';

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:3',
    ];

    public function submitReview()
    {
        $this->validate();

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->reset('rating', 'comment');

        $this->dispatch('success', message: 'Review submitted successfully!');
    }

    public function render()
    {
        return view('livewire.frontend.product.single-view', [
            'reviews' => $this->product->reviews()
                ->with('user')
                ->latest()
                ->paginate(3)
        ])->layout('livewire.frontend.layouts.app');
    }

    
}
