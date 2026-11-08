<?php

namespace Addons\PartialPayment\Livewire\Admin;

use Addons\PartialPayment\Models\ProductRule;
use Addons\PartialPayment\Services\PlanCalculator;
use App\Models\Product;
use Livewire\Component;

class ProductRuleForm extends Component
{
    public ?int $productId = null;

    public string $mode = 'none';

    public $value = '';

    public function mount($productId = null): void
    {
        $this->productId = $productId ? (int) $productId : null;

        $rule = ProductRule::forProduct($this->productId);

        if ($rule !== null) {
            $this->mode = $rule->advance_type;
            $this->value = (string) (float) $rule->advance_value;
        }
    }

    public function save(): void
    {
        if (! $this->productId) {
            $this->dispatch('error', message: 'প্রথমে প্রোডাক্টটি সেভ করুন, তারপর অ্যাডভান্স সেট করুন।');

            return;
        }

        if ($this->mode === 'none') {
            ProductRule::where('product_id', $this->productId)->delete();
            $this->dispatch('success', message: 'অ্যাডভান্স লাগবে না — সাধারণ ক্যাশ অন ডেলিভারি।');

            return;
        }

        $this->validate([
            'value' => 'required|numeric|min:0.01'.($this->mode === 'percent' ? '|max:99' : ''),
        ]);

        ProductRule::updateOrCreate(
            ['product_id' => $this->productId],
            ['advance_type' => $this->mode, 'advance_value' => round((float) $this->value, 2)]
        );

        $this->dispatch('success', message: 'অ্যাডভান্স সেভ হয়েছে।');
    }

    public function updatedMode(): void
    {
        $this->resetValidation('value');
    }

    /** Example line: what a Tk 1,960 + Tk 60 delivery order would look like. */
    public function example(): array
    {
        if ($this->mode === 'none' || ! is_numeric($this->value) || (float) $this->value <= 0) {
            return [];
        }

        $product = Product::find($this->productId);
        $price = $product ? (float) $product->price_info['final'] : 0.0;
        $shipping = (float) setting('dc_inside', 60);

        if ($price <= 0) {
            return [];
        }

        $rule = new ProductRule(['advance_type' => $this->mode, 'advance_value' => (float) $this->value]);

        $plan = PlanCalculator::build($rule, $price, $shipping);

        return $plan === [] ? [] : array_merge($plan, ['price' => $price, 'shipping' => $shipping]);
    }

    public function render()
    {
        return view('partial-payment::livewire.product-rule', [
            'product' => $this->productId ? Product::find($this->productId) : null,
            'example' => $this->example(),
        ]);
    }
}
