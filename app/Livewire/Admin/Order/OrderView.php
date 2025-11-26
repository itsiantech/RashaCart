<?php

namespace App\Livewire\Admin\Order;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderView extends Component
{
    public $order;
    public $searchProduct = '';
    public $searchResults = [];
    public $selectedProduct = null;
    public $selectedVariant = null;
    public $newItemQuantity = 1;

    protected $listeners = ['orderUpdated' => '$refresh'];

    public function mount($orderId)
    {
        $this->loadOrder($orderId);
    }

    public function loadOrder($orderId)
    {
        $this->order = Order::with(['items.product', 'items.variant'])->findOrFail($orderId);
    }

    public function updatedSearchProduct()
    {
        if ($this->searchProduct) {
            $this->searchResults = Product::where('name', 'like', "%{$this->searchProduct}%")
                ->orWhere('short_description', 'like', "%{$this->searchProduct}%")
                ->take(5)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function selectProduct($productId)
    {
        $this->selectedProduct = Product::with('variants')->find($productId);
        $this->selectedVariant = null;
        $this->searchResults = [];
        $this->searchProduct = $this->selectedProduct->name;
    }

    public function addItem()
    {
        if (!$this->selectedProduct) return;

        $variantId = $this->selectedVariant ? $this->selectedVariant : null;

        $this->order->items()->create([
            'product_id' => $this->selectedProduct->id,
            'product_variant_id' => $variantId,
            'quantity' => $this->newItemQuantity,
            'unit_price' => $variantId 
                ? ProductVariant::find($variantId)->price 
                : $this->selectedProduct->price,
        ]);

        // Reset fields
        $this->selectedProduct = null;
        $this->selectedVariant = null;
        $this->searchProduct = '';
        $this->newItemQuantity = 1;
        $this->loadOrder($this->order->id);
        $this->dispatch('success', message: 'Added Success!');
    }

    public function increaseQuantity($itemId)
    {
        $item = OrderItem::find($itemId);
        if ($item) {
            $item->increment('quantity');
            $this->loadOrder($this->order->id);
             $this->dispatch('success', message: 'Quantity Update!');
        }
    }

    public function decreaseQuantity($itemId)
    {
        $item = OrderItem::find($itemId);
        if ($item && $item->quantity > 1) {
            $item->decrement('quantity');
            $this->loadOrder($this->order->id);
            $this->dispatch('success', message: 'Quantity Update!');
        }
    }

    public function removeItem($itemId)
    {
        $item = OrderItem::find($itemId);
        if ($item) {
            $item->delete();
            $this->loadOrder($this->order->id);
            $this->dispatch('success', message: 'Item Deleted!');
        }
    }

    public function updateStatus($status)
    {
        $this->order->update(['status' => $status]);
        $this->loadOrder($this->order->id);
       $this->dispatch('success', message: 'Order status updated!');
    }

  
    public function render()
    {
        return view('livewire.admin.order.order-view')->layout('livewire.admin.layouts.app');
    }
}
