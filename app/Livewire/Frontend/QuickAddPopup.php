<?php

namespace App\Livewire\Frontend;

use App\Models\Product;
use App\Services\CombinationException;
use App\Services\CombinationResolver;
use App\Services\PurchaseActions;
use App\Services\VariantPickerData;
use App\Traits\HandlesCartActions;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickAddPopup extends Component
{
    use HandlesCartActions;

    public bool $open = false;

    public ?int $productId = null;

    public string $productName = '';

    public string $productSku = '';

    public float $unitPrice = 0.0;

    public array $pickerData = [];

    public array $picks = [];

    public string $pickerError = '';

    public int $pickerStock = 0;

    // HandlesCartActions::buildPurchaseItem() reads these.
    public ?int $selectedVariant = null;

    public ?string $selectedColor = null;

    public ?string $selectedSize = null;

    public ?int $selectedOption = null;

    #[On('quick-add-open')]
    public function openQuickAdd(int $productId): void
    {
        if (! PurchaseActions::addToCartEnabled() || ! PurchaseActions::variantPopupEnabled()) {
            return;
        }

        $product = Product::with(['variants.variantAttributeValues.attribute', 'variants.variantAttributeValues.attributeValue', 'variants.inventories', 'imageOptions', 'activeFlashSaleItem'])
            ->find($productId);

        if (! $product) {
            $this->dispatch('error', message: 'পণ্যটি পাওয়া যায়নি।');

            return;
        }

        $this->resetState();

        $this->productId = (int) $product->id;
        $this->productName = (string) $product->name;
        $this->productSku = (string) ($product->sku ?? '');
        $this->unitPrice = (float) $product->price_info['final'];
        $this->pickerData = VariantPickerData::for($product);
        $this->quantity = 1;
        $this->open = true;

        $this->js("document.body.classList.add('op-open'); document.body.style.overflow = 'hidden'");
    }

    public function selectPick(string $attribute, string $value): void
    {
        $this->picks[$attribute] = $value;
        $this->pickerError = '';
        $this->refreshPicker();
    }

    public function selectPickerOption($optionId): void
    {
        $this->selectedOption = $optionId ? (int) $optionId : null;
        $this->pickerError = '';
        $this->refreshPicker();
    }

    public function bumpQty(): void
    {
        $next = max(1, (int) $this->quantity) + 1;
        $this->quantity = $this->pickerStock > 0 ? min($next, $this->pickerStock) : $next;
        $this->refreshPicker();
    }

    public function dropQty(): void
    {
        $this->quantity = max(1, (int) $this->quantity - 1);
        $this->refreshPicker();
    }

    public function close(): void
    {
        $this->open = false;
        $this->resetState();
        $this->js("document.body.classList.remove('op-open'); document.body.style.overflow = ''");
    }

    public function confirmQuickAdd(): void
    {
        if (! $this->open) {
            return;
        }

        $this->pickerError = '';

        $product = Product::with('activeFlashSaleItem')->find($this->productId);

        if (! $product) {
            $this->pickerError = 'পণ্যটি পাওয়া যায়নি।';

            return;
        }

        try {
            $combo = CombinationResolver::resolve($product, $this->matchedVariantId(), $this->selectedOption, max(1, (int) $this->quantity));
        } catch (CombinationException $e) {
            $this->pickerError = $e->getMessage();

            return;
        }

        $this->selectedVariant = $combo->variant?->id;
        $this->selectedOption = $combo->option?->id;

        if (! $this->selectionComplete()) {
            $this->pickerError = 'সব অপশন বেছে নিন।';

            return;
        }

        if ($this->addToCart($product->id)) {
            $this->close();
        }
    }

    /** "Size: M · Color: Coffee" summary line. */
    public function pickedLabel(): string
    {
        $parts = [];

        foreach ($this->pickerData['groups'] ?? [] as $group) {
            $value = $this->picks[$group['name']] ?? null;

            if ($value !== null && $value !== '') {
                $parts[] = $group['name'].': '.$value;
            }
        }

        $option = collect($this->pickerData['options'] ?? [])->firstWhere('id', (int) $this->selectedOption);

        if ($option) {
            $parts[] = $option['label'];
        }

        return implode(' · ', $parts);
    }

    protected function matchedVariantId(): ?int
    {
        if (empty($this->pickerData['groups'])) {
            return null;
        }

        return VariantPickerData::match($this->pickerData, $this->picks)['id'] ?? null;
    }

    protected function selectionComplete(): bool
    {
        foreach ($this->pickerData['groups'] ?? [] as $group) {
            $value = $this->picks[$group['name']] ?? null;

            if (! empty($group['required']) && ($value === null || $value === '')) {
                return false;
            }
        }

        if (! empty($this->pickerData['option_required']) && ! $this->selectedOption) {
            return false;
        }

        return true;
    }

    protected function refreshPicker(): void
    {
        $product = Product::with('activeFlashSaleItem')->find($this->productId);

        if (! $product) {
            return;
        }

        try {
            $combo = CombinationResolver::resolve($product, $this->matchedVariantId(), $this->selectedOption, max(1, (int) $this->quantity));
        } catch (CombinationException $e) {
            $this->unitPrice = (float) $product->price_info['final'];
            $this->pickerStock = 0;

            return;
        }

        $this->selectedOption = $combo->option?->id;
        $this->unitPrice = (float) $combo->unitPrice;
        $this->pickerStock = (int) $combo->available;
    }

    protected function resetState(): void
    {
        $this->picks = [];
        $this->pickerData = [];
        $this->pickerError = '';
        $this->pickerStock = 0;
        $this->unitPrice = 0.0;
        $this->productId = null;
        $this->productName = '';
        $this->productSku = '';
        $this->selectedVariant = null;
        $this->selectedColor = null;
        $this->selectedSize = null;
        $this->selectedOption = null;
        $this->quantity = 1;
    }

    public function render()
    {
        $availability = [];

        foreach ($this->pickerData['groups'] ?? [] as $group) {
            foreach ($group['values'] as $value) {
                // The current pick stays clickable so it can always be changed.
                $availability[$group['name']][$value['value']] = ($this->picks[$group['name']] ?? null) === $value['value']
                    || VariantPickerData::valueAvailable($this->pickerData, $this->picks, $group['name'], $value['value']);
            }
        }

        return view('livewire.frontend.quick-add-popup', [
            'availability' => $availability,
        ]);
    }
}
