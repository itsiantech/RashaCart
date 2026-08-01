<?php

namespace App\Livewire\Admin\BestOffer;

use App\Models\BestOffer;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class BestOfferIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $product_id = '';

    public $discount_type = 'percent';

    public $discount_value = '';

    public $days = 7;

    public $is_active = true;

    public $showForm = false;

    public $deleteId = null;

    protected $listeners = ['deleteConfirmed'];

    protected function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0',
            'days' => 'required|integer|min:1|max:365',
        ];
    }

    public function updatedDiscountValue()
    {
        $this->calculateOfferPrice();
    }

    public function updatedDiscountType()
    {
        $this->calculateOfferPrice();
    }

    public function updatedProductId()
    {
        $this->calculateOfferPrice();
    }

    public function calculateOfferPrice()
    {
        // Just for preview — actual save happens in store()
    }

    public function getOfferPricePreviewProperty()
    {
        if (! $this->product_id || ! $this->discount_value) {
            return null;
        }

        $product = Product::find($this->product_id);
        if (! $product) {
            return null;
        }

        $base = $product->sale_price ?? $product->price;

        if ($this->discount_type === 'percent') {
            return max(0, $base - ($base * $this->discount_value / 100));
        }

        return max(0, $base - $this->discount_value);
    }

    public function store()
    {
        $this->validate();

        $product = Product::findOrFail($this->product_id);
        $base = $product->sale_price ?? $product->price;

        $offerPrice = $this->discount_type === 'percent'
            ? max(0, $base - ($base * $this->discount_value / 100))
            : max(0, $base - $this->discount_value);

        BestOffer::create([
            'product_id' => $this->product_id,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'offer_price' => $offerPrice,
            'expires_at' => now()->addDays((int) $this->days),
            'is_active' => true,
        ]);

        $this->reset(['product_id', 'discount_type', 'discount_value', 'days', 'showForm']);
        $this->discount_type = 'percent';
        $this->days = 7;
        $this->dispatch('success', message: 'Best offer added successfully!');
    }

    public function toggleActive($id)
    {
        $offer = BestOffer::find($id);
        if ($offer) {
            $offer->update(['is_active' => ! $offer->is_active]);
            $this->dispatch('success', message: 'Status updated.');
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->js("window.dispatchEvent(new CustomEvent('showDeleteConfirmation'))");
    }

    public function deleteConfirmed()
    {
        if (! $this->deleteId) {
            return;
        }
        BestOffer::find($this->deleteId)?->delete();
        $this->deleteId = null;
        $this->dispatch('success', message: 'Offer deleted.');
    }

    public function render()
    {
        $offers = BestOffer::with('product')
            ->when(
                $this->search,
                fn ($q) => $q->whereHas(
                    'product',
                    fn ($pq) => $pq->where('name', 'like', '%'.$this->search.'%')
                )
            )
            ->latest()
            ->paginate(10);

        $products = Product::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'sale_price']);

        return view('livewire.admin.best-offer.best-offer-index', compact('offers', 'products'))
            ->layout('livewire.admin.layouts.app');
    }
}
