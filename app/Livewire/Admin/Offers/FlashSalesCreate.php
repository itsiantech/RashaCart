<?php

namespace App\Livewire\Admin\Offers;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use Livewire\Component;

class FlashSalesCreate extends Component
{
    // Campaign fields
    public string $title       = '';
    public string $description = '';
    public string $start_at    = '';
    public string $end_at      = '';
    public bool   $is_active   = false;

    // Product picker
    public string $productSearch = '';
    public array  $selectedItems = [];

    public bool $showProductDropdown = false;
    // selectedItems format: [ product_id => ['discount_percent' => 0, 'discounted_price' => 0] ]

    protected function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at'    => 'required|date|before:end_at',
            'end_at'      => 'required|date|after:start_at',
            'is_active'   => 'boolean',
            'selectedItems'                              => 'required|array|min:1',
            'selectedItems.*.discount_percent'           => 'required|numeric|min:1|max:99',
        ];
    }

    protected $messages = [
        'selectedItems.required' => 'Please add at least one product to the flash sale.',
        'selectedItems.min'      => 'Please add at least one product to the flash sale.',
    ];

    // ─── Product Search ───────────────────────────────────────────────────────

    // Add this property

    public function getSearchResultsProperty()
    {
        if (!$this->showProductDropdown) {
            return collect();
        }

        $alreadySelected = array_keys($this->selectedItems);

        return Product::where('name', 'like', '%' . $this->productSearch . '%')
            ->whereNotIn('id', $alreadySelected)
            ->limit(8)
            ->get(['id', 'name', 'price']);
    }

    public function addProduct(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $this->selectedItems[$productId] = [
            'product_id'       => $productId,
            'name'             => $product->name,
            'original_price'   => $product->price,
            'discount_percent' => 10,
            'discounted_price' => FlashSaleItem::calculateDiscountedPrice($product->price, 10),
        ];

        $this->productSearch = '';
        // Keep dropdown open so admin can keep adding products
    }

    // Add these two methods
    public function openProductDropdown(): void
    {
        $this->showProductDropdown = true;
    }

    public function closeProductDropdown(): void
    {
        $this->showProductDropdown = false;
        $this->productSearch = '';
    }


    public function removeProduct(int $productId): void
    {
        unset($this->selectedItems[$productId]);
    }

    public function updatedSelectedItems($value, $key): void
    {
        // key format: "productId.discount_percent"
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

        $flashSale = FlashSale::create([
            'title'       => $this->title,
            'description' => $this->description,
            'start_at'    => $this->start_at,
            'end_at'      => $this->end_at,
            'is_active'   => $this->is_active,
        ]);

        foreach ($this->selectedItems as $productId => $item) {
            FlashSaleItem::create([
                'flash_sale_id'    => $flashSale->id,
                'product_id'       => $productId,
                'discount_percent' => $item['discount_percent'],
                'discounted_price' => $item['discounted_price'],
                'is_active'        => true,
            ]);
        }

        session()->flash('success', 'Flash sale created successfully.');
        $this->redirect(route('admin.flash.sales.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.offers.flash-sales-create')
            ->layout('livewire.admin.layouts.app', ['title' => 'Create Flash Sale']);
    }
}
