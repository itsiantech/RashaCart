<?php

namespace App\Livewire\Admin\Offers;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use Livewire\Component;

class FlashSalesStockManage extends Component
{
    public FlashSale $flashSale;

    public int $flashSaleId;

    public array $stocks = [];

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function mount(int $id): void
    {
        $this->flashSale = FlashSale::with('items.product')->findOrFail($id);
        $this->flashSaleId = $id;

        foreach ($this->flashSale->items as $item) {
            $this->stocks[$item->id] = $item->product->stock ?? 0;
        }
    }

    public function updateStock(int $itemId): void
    {
        $item = FlashSaleItem::with('product')->findOrFail($itemId);
        $newStock = (int) ($this->stocks[$itemId] ?? 0);

        $item->product->update(['stock' => $newStock]);
        session()->flash('success', "Stock updated for {$item->product->name}.");
    }

    public function confirmRemoveItem(int $itemId): void
    {
        $item = FlashSaleItem::with('product')->findOrFail($itemId);
        $this->deleteItemId = $itemId;
        $this->deleteItemName = $item->product->name;
        $this->dispatch('show-delete-modal');
    }

    public function removeItem(): void
    {
        if ($this->deleteItemId) {
            FlashSaleItem::findOrFail($this->deleteItemId)->delete();
            // Reload flash sale items
            $this->flashSale = FlashSale::with('items.product')->findOrFail($this->flashSaleId);
            foreach ($this->flashSale->items as $item) {
                $this->stocks[$item->id] = $item->product->stock ?? 0;
            }
            session()->flash('success', "Product \"{$this->deleteItemName}\" removed from flash sale.");
        }
        $this->deleteItemId = null;
        $this->deleteItemName = '';
        $this->dispatch('hide-delete-modal');
    }

    public function cancelRemove(): void
    {
        $this->deleteItemId = null;
        $this->deleteItemName = '';
        $this->dispatch('hide-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.offers.flash-sales-stock-manage')
            ->layout('livewire.admin.layouts.app', ['title' => 'Manage Stock']);
    }
}
