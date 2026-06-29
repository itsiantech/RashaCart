<?php

namespace App\Livewire\Admin\Offers;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use Livewire\Component;

class FlashSalesView extends Component
{
    public FlashSale $flashSale;
    public int $flashSaleId;
 
    public function mount(int $id): void
    {
        $this->flashSale   = FlashSale::with('items.product')->findOrFail($id);
        $this->flashSaleId = $id;
    }
 
    public function toggleItemActive(int $itemId): void
    {
        $item = FlashSaleItem::findOrFail($itemId);
        $item->update(['is_active' => ! $item->is_active]);
        $this->flashSale = FlashSale::with('items.product')->findOrFail($this->flashSaleId);
        session()->flash('success', 'Item status updated.');
    }
 
    public function render()
    {
        return view('livewire.admin.offers.flash-sales-view')
            ->layout('livewire.admin.layouts.app', ['title' => 'View Flash Sale']);
    }

}
