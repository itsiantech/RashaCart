<?php

namespace App\Livewire\Admin\Offers;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use Livewire\Component;

class FlashSalesEdit extends Component
{
    public FlashSale $flashSale;

    public int $flashSaleId;

    // Campaign fields
    public string $title = '';

    public string $description = '';

    public string $start_at = '';

    public string $end_at = '';

    public bool $is_active = false;

    // Product picker
    public string $productSearch = '';

    public array $selectedItems = [];

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date|before:end_at',
            'end_at' => 'required|date|after:start_at',
            'is_active' => 'boolean',
            'selectedItems' => 'required|array|min:1',
            'selectedItems.*.discount_percent' => 'required|numeric|min:1|max:99',
        ];
    }

    public function mount(int $id): void
    {
        $this->flashSale = FlashSale::with('items.product')->findOrFail($id);
        $this->flashSaleId = $id;

        $this->title = $this->flashSale->title;
        $this->description = $this->flashSale->description ?? '';
        $this->start_at = $this->flashSale->start_at->format('Y-m-d\TH:i');
        $this->end_at = $this->flashSale->end_at->format('Y-m-d\TH:i');
        $this->is_active = $this->flashSale->is_active;

        foreach ($this->flashSale->items as $item) {
            $this->selectedItems[$item->product_id] = [
                'item_id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'original_price' => $item->product->price,
                'discount_percent' => $item->discount_percent,
                'discounted_price' => $item->discounted_price,
                'is_active' => $item->is_active,
            ];
        }
    }

    // ─── Product Search ───────────────────────────────────────────────────────

    public function getSearchResultsProperty()
    {
        if (strlen($this->productSearch) < 2) {
            return collect();
        }

        $alreadySelected = array_keys($this->selectedItems);

        return Product::where('name', 'like', '%'.$this->productSearch.'%')
            ->whereNotIn('id', $alreadySelected)
            ->limit(8)
            ->get(['id', 'name', 'price', 'thumbnail']);
    }

    public function addProduct(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $this->selectedItems[$productId] = [
            'item_id' => null, // new item, not yet saved
            'product_id' => $productId,
            'name' => $product->name,
            'original_price' => $product->price,
            'discount_percent' => 10,
            'discounted_price' => FlashSaleItem::calculateDiscountedPrice($product->price, 10),
            'is_active' => true,
        ];

        $this->productSearch = '';
    }

    public function removeProduct(int $productId): void
    {
        // If item already exists in DB, delete it
        if (! empty($this->selectedItems[$productId]['item_id'])) {
            FlashSaleItem::find($this->selectedItems[$productId]['item_id'])?->delete();
        }
        unset($this->selectedItems[$productId]);
    }

    public function toggleItemActive(int $productId): void
    {
        $this->selectedItems[$productId]['is_active'] = ! $this->selectedItems[$productId]['is_active'];
    }

    public function updatedSelectedItems($value, $key): void
    {
        [$productId, $field] = explode('.', $key);

        if ($field === 'discount_percent' && isset($this->selectedItems[$productId])) {
            $percent = (float) $value;
            $originalPrice = $this->selectedItems[$productId]['original_price'];
            $this->selectedItems[$productId]['discounted_price'] =
                FlashSaleItem::calculateDiscountedPrice($originalPrice, $percent);
        }
    }

    // ─── Save ─────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate();

        $this->flashSale->update([
            'title' => $this->title,
            'description' => $this->description,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'is_active' => $this->is_active,
        ]);

        // Overlap notice (latest-wins on storefront): warn, don't block
        if ($this->is_active) {
            $overlap = FlashSale::where('id', '!=', $this->flashSale->id)
                ->where('is_active', true)
                ->where('start_at', '<=', $this->end_at)
                ->where('end_at', '>=', $this->start_at)
                ->exists();
            if ($overlap) {
                session()->flash('warning', 'সতর্কতা: অন্য active campaign-এর সাথে date overlap আছে — homepage-এ latest-টাই দেখাবে।');
            }
        }

        foreach ($this->selectedItems as $productId => $item) {
            FlashSaleItem::updateOrCreate(
                [
                    'flash_sale_id' => $this->flashSale->id,
                    'product_id' => $productId,
                ],
                [
                    'discount_percent' => $item['discount_percent'],
                    'discounted_price' => $item['discounted_price'],
                    'is_active' => $item['is_active'],
                ]
            );
        }

        session()->flash('success', 'Flash sale updated successfully.');
        $this->redirect(route('admin.flash.sales.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.offers.flash-sales-edit')
            ->layout('livewire.admin.layouts.app', ['title' => 'Edit Flash Sale']);
    }
}
