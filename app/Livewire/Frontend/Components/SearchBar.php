<?php

namespace App\Livewire\Frontend\Components;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class SearchBar extends Component
{
    public $q = ''; // 🔥 Add this

    public function search()
    {
        if (! $this->q) {
            return;
        }

        $query = trim($this->q);

        try {

            $category = Category::where('name', $query)->first();

            if ($category) {
                return redirect()->route('frontend.category.products.view', $category->slug);
            }

            $product = Product::where('name', $query)->first();

            if ($product) {
                return redirect()->route('frontend.product.single.view', $product->slug);
            }

            $brand = Brand::where('name', $query)->first();

            if ($brand) {
                return redirect()->route('frontend.brands.products.view', $brand->slug);
            }

            $category = Category::where('name', 'LIKE', "%{$query}%")->first();

            if ($category) {
                return redirect()->route('frontend.category.productsc.view', $category->slug);
            }

            $product = Product::where('name', 'LIKE', "%{$query}%")->first();

            if ($product) {
                return redirect()->route('frontend.product.single.view', $product->slug);
            }

            $brand = Brand::where('name', 'LIKE', "%{$query}%")->first();

            if ($brand) {
                return redirect()->route('frontend.brand.products.view', $brand->slug);
            }

            // If nothing found
            session()->flash('search_error', 'No results found');
        } catch (\Exception $e) {
            report($e);
            session()->flash('search_error', 'Something went wrong');
        }
        $this->dispatch('search-updated');
    }

    public function render()
    {
        $results = Product::with('category', 'brand')
            ->where('name', 'LIKE', '%'.$this->q.'%')
            ->take(10)
            ->get();

        return view('livewire.frontend.components.search-bar', compact('results'));
    }
}
