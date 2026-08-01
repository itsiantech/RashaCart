<?php

namespace App\Livewire\Admin\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;

class OrderView extends Component
{
    public $orderId;

    public $order;

    public $searchProduct = '';

    public $searchResults = [];

    public $selectedProduct = null;

    public $selectedVariant = null;

    public $newItemQuantity = 1;

    protected $listeners = ['orderUpdated' => '$refresh'];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->loadOrder();
    }

    public function loadOrder()
    {
        $this->order = Order::with([
            'items.product.variants.variantAttributeValues.attribute',
            'items.product.variants.variantAttributeValues.attributeValue',
            'items.variant.variantAttributeValues.attribute',
            'items.variant.variantAttributeValues.attributeValue',
        ])->findOrFail($this->orderId);

        foreach ($this->order->items as $item) {
            $item->display_image = $this->resolveItemImage($item);
        }
    }

    private function resolveItemImage($item): string
    {
        $fallback = $item->product?->thumbnail
            ? asset('storage/'.$item->product->thumbnail)
            : asset('assets/admin/img/avatars/user-avater.png');

        if (empty($item->product_variant_id)) {
            return $fallback;
        }

        if ($item->variant && ! empty($item->variant->image)) {
            return asset('storage/'.$item->variant->image);
        }

        $colorValue = null;
        foreach ($item->variant?->variantAttributeValues ?? [] as $vav) {
            if (! $vav->attribute || ! $vav->attributeValue) {
                continue;
            }
            if (in_array(strtolower($vav->attribute->name ?? ''), ['color', 'colour'])) {
                $colorValue = $vav->attributeValue->value;
                break;
            }
        }

        if (! $colorValue && ! empty($item->color)) {
            $colorValue = $item->color;
        }

        if ($colorValue && $item->product) {
            $colorVariant = $item->product->variants->first(
                fn ($v) => ! empty($v->image) &&
                    $v->variantAttributeValues->contains(function ($vav) use ($colorValue) {
                        return $vav->attributeValue &&
                            in_array(strtolower($vav->attribute?->name ?? ''), ['color', 'colour']) &&
                            $vav->attributeValue->value === $colorValue;
                    })
            );

            if ($colorVariant) {
                return asset('storage/'.$colorVariant->image);
            }
        }

        if ($item->product) {
            $anyImageVariant = $item->product->variants->first(fn ($v) => ! empty($v->image));
            if ($anyImageVariant) {
                return asset('storage/'.$anyImageVariant->image);
            }
        }

        return $fallback;
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
        if (! $this->selectedProduct) {
            return;
        }

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
        $this->loadOrder();
        $this->dispatch('success', message: 'Added Success!');
    }

    public function increaseQuantity($itemId)
    {
        $item = OrderItem::find($itemId);
        if ($item) {
            $item->increment('quantity');
            $this->loadOrder();
            $this->dispatch('success', message: 'Quantity Update!');
        }
    }

    public function decreaseQuantity($itemId)
    {
        $item = OrderItem::find($itemId);
        if ($item && $item->quantity > 1) {
            $item->decrement('quantity');
            $this->loadOrder();
            $this->dispatch('success', message: 'Quantity Update!');
        }
    }

    public function removeItem($itemId)
    {
        $item = OrderItem::find($itemId);
        if ($item) {
            $item->delete();
            $this->loadOrder();
            $this->dispatch('success', message: 'Item Deleted!');
        }
    }

    public function updateStatus($status)
    {
        $this->order->update(['status' => $status]);
        $this->loadOrder();
        $this->dispatch('success', message: 'Order status updated!');
    }

    public function render()
    {
        return view('livewire.admin.order.order-view')->layout('livewire.admin.layouts.app');
    }
}
