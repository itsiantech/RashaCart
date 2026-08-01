<?php

namespace App\Livewire\Frontend\Category;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Traits\HandlesCartActions;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryProduct extends Component
{
    public $category = null;

    public $selectedPrice = null;

    public $meta_title;

    public $meta_description;

    public $meta_keywords;

    public $meta_tags;

    public $relatedProducts;

    use HandlesCartActions;
    use WithPagination;

    protected $queryString = [
        'selectedPrice' => ['except' => null],
    ];

    public function mount($category = null)
    {
        if ($category) {
            $this->category = Category::where('slug', $category)->first();

            $this->meta_title = $this->category->meta_title;
            $this->meta_description = $this->category->meta_description;
            $this->meta_keywords = $this->category->meta_keywords;
            $this->meta_tags = $this->category->meta_tags;
        }

        // Load products
        $this->relatedProducts = Product::where('status', true)
            ->latest()
            ->take(5)
            ->get();

        // Attach total_sold
        $totalSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_id')
            ->pluck('total_sold', 'product_id');

        $this->relatedProducts = $this->relatedProducts->map(function ($product) use ($totalSold) {
            $product->total_sold = $totalSold[$product->id] ?? 0;

            return $product;
        });
    }

    public function togglePrice($price)
    {
        $this->selectedPrice = ($this->selectedPrice === $price) ? null : $price;
    }

    public function getMetaData()
    {
        if ($this->category) {
            $this->meta_title = $this->category->meta_title ?? $this->category->name;
            $this->meta_description = $this->category->meta_description ?? '';
            $this->meta_keywords = $this->category->meta_keywords ?? '';
            $this->meta_tags = $this->category->meta_tags ?? '';
        }
    }

    public function getProductsProperty()
    {
        $query = Product::query();

        // Category filter
        if ($this->category) {
            $query->where('category_id', $this->category->id);
        }

        if ($this->selectedPrice) {
            [$min, $max] = explode('-', $this->selectedPrice);
            $query->whereBetween('price', [(int) $min, (int) $max]);
        }

        return $query->paginate(20);
    }

    public function getCategoriesProperty()
    {
        return Category::with('allChildren')->where('status', 1)->get();
    }

    public function render()
    {
        return view('livewire.frontend.category.category-product', [
            'products' => $this->products,
            'categories' => $this->categories,
        ])->layout('livewire.frontend.layouts.app')
            ->layoutData([
                'meta' => [
                    'title' => $this->meta_title ?? '',
                    'description' => $this->meta_description ?? '',
                    'keywords' => $this->meta_keywords ?? '',
                    'tags' => $this->meta_tags ?? '',
                ],
            ]);
    }
}
