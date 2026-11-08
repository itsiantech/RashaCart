<?php

namespace App\Livewire\Frontend;

use App\Models\Product;
use App\Services\CombinationException;
use App\Services\CombinationResolver;
use App\Services\OrderService;
use App\Services\PhoneNormalizer;
use App\Services\PurchaseActions;
use App\Services\VariantPickerData;
use App\Support\Hooks;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderPopup extends Component
{
    public bool $open = false;

    public bool $placed = false;

    public ?int $productId = null;

    public string $productName = '';

    public float $unitPrice = 0.0;

    public int $qty = 1;

    public ?int $variantId = null;

    public ?int $optionId = null;

    /** Variant picker step: shown while size/color/design are not fully chosen. */
    public array $pickerData = [];

    public array $picks = [];

    public bool $pickerRequired = false;

    public bool $showPicker = false;

    public string $pickerError = '';

    public int $pickerStock = 0;

    public string $name = '';

    public string $phone = '';

    public string $address = '';

    public string $email = '';

    public string $zone = 'inside';

    /** Honeypot: real users never fill this. */
    public string $website = '';

    /**
     * Wizard step. Core only knows "details"; an add-on can append steps
     * through the popup.steps filter and bind fields into $addon.
     */
    public string $step = 'details';

    /** Input bag owned by add-ons (trx id, payment method, …). */
    public array $addon = [];

    /** Enriched by the popup.success_data filter after an order lands. */
    public array $successData = [];

    public string $placedInvoice = '';

    public string $waUrl = '';

    #[On('order-popup-open')]
    public function openPopup(int $productId, $variantId = null, $optionId = null, $qty = 1): void
    {
        if (! PurchaseActions::popupOrderEnabled()) {
            return;
        }

        try {
            $product = Product::with(['activeFlashSaleItem', 'imageOptions'])->findOrFail($productId);
        } catch (\Throwable $e) {
            $this->dispatch('error', message: 'পণ্যটি পাওয়া যায়নি। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।');

            return;
        }

        $this->resetPopupState();
        $this->resetValidation();

        $this->productId = (int) $product->id;
        $this->productName = (string) $product->name;
        $this->qty = max(1, (int) $qty);
        $this->pickerData = VariantPickerData::for($product);
        $this->picks = $variantId ? VariantPickerData::pickedFromVariant($this->pickerData, (int) $variantId) : [];
        $this->variantId = $variantId ? (int) $variantId : null;
        $this->optionId = $optionId ? (int) $optionId : null;
        $this->pickerRequired = PurchaseActions::variantPopupEnabled()
            && (! empty($this->pickerData['groups']) || ! empty($this->pickerData['options']));

        if (! $this->pickerRequired) {
            try {
                $combo = CombinationResolver::resolve($product, $this->variantId, $this->optionId, $this->qty);
                $this->unitPrice = (float) $combo->unitPrice;
                $this->variantId = $combo->variant?->id;
                $this->optionId = $combo->option?->id;
            } catch (CombinationException $e) {
                $this->dispatch('error', message: $e->getMessage());

                return;
            }
        } elseif ($this->selectionComplete()) {
            $this->refreshPicker();
        } else {
            $this->unitPrice = (float) $product->price_info['final'];
        }

        $this->showPicker = $this->pickerRequired && ! $this->selectionComplete();

        $user = auth()->user();

        $this->zone = (string) setting('popup_order_default_zone', 'inside') === 'outside' ? 'outside' : 'inside';
        $this->name = (string) ($user->name ?? '');
        $this->phone = (string) ($user->phone ?? '');
        $this->address = (string) ($user->address ?? '');
        $this->email = (string) ($user->email ?? '');
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
        $this->optionId = $optionId ? (int) $optionId : null;
        $this->pickerError = '';
        $this->refreshPicker();
    }

    public function increaseQty(): void
    {
        $next = $this->qty + 1;
        $this->qty = $this->pickerStock > 0 ? min($next, $this->pickerStock) : $next;
        $this->refreshPicker();
    }

    public function decreaseQty(): void
    {
        $this->qty = max(1, $this->qty - 1);
        $this->refreshPicker();
    }

    public function continueToForm(): void
    {
        if (! $this->open || $this->placed) {
            return;
        }

        $this->pickerError = '';

        $product = Product::with('activeFlashSaleItem')->find($this->productId);

        if (! $product) {
            $this->pickerError = 'পণ্যটি পাওয়া যায়নি।';

            return;
        }

        try {
            $combo = CombinationResolver::resolve($product, $this->matchedVariantId(), $this->optionId, $this->qty);
        } catch (CombinationException $e) {
            $this->pickerError = $e->getMessage();

            return;
        }

        $this->variantId = $combo->variant?->id;
        $this->optionId = $combo->option?->id;
        $this->unitPrice = (float) $combo->unitPrice;
        $this->pickerStock = (int) $combo->available;
        $this->qty = max(1, min($this->qty, $this->pickerStock));
        $this->showPicker = false;
    }

    public function editPicker(): void
    {
        $this->showPicker = true;
        $this->pickerError = '';
    }

    /** Steps offered by add-ons, in registration order. Core supplies "details". */
    public function steps(): array
    {
        return Hooks::filter('popup.steps', [
            ['key' => 'details', 'label' => 'তথ্য দিন'],
        ], $this);
    }

    public function gotoStep(string $key): void
    {
        $allowed = array_column($this->steps(), 'key');

        if (! in_array($key, $allowed, true)) {
            return;
        }

        // Leaving the details step means the customer's own fields must already
        // be valid, otherwise the payment pane would hide the errors it causes.
        if ($key !== 'details') {
            $this->validate($this->coreRules());
        }

        $this->step = $key;
    }

    /** Slot name an add-on owns for the current step, when it declares one. */
    public function stepSlot(): ?string
    {
        foreach ($this->steps() as $step) {
            if (($step['key'] ?? null) === $this->step && ! empty($step['slot'])) {
                // A step naming a slot nobody registered would hide the core
                // fields without replacing them, so it is ignored.
                return Hooks::hasSlot((string) $step['slot']) ? (string) $step['slot'] : null;
            }
        }

        return null;
    }

    public function primaryButtonLabel(): string
    {
        return (string) Hooks::filter('popup.primary_button_label', 'অর্ডার কনফার্ম করুন', $this);
    }

    /** The fields the core popup owns; add-ons extend this list by filter. */
    protected function coreRules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'phone' => ['required', 'string', 'max:20', function (string $attribute, $value, $fail) {
                if (! PhoneNormalizer::isValid($value)) {
                    $fail('সঠিক মোবাইল নাম্বার দিন (যেমন: 01712345678)।');
                }
            }],
            'address' => 'required|string|min:5|max:500',
            'email' => 'nullable|email|max:150',
            'zone' => 'required|in:inside,outside',
        ];
    }

    public function updatedZone(): void
    {
        $this->resetValidation('zone');
    }

    public function close(): void
    {
        $this->open = false;
        $this->resetPopupState();
        $this->resetValidation();
        $this->website = '';
        $this->js("document.body.classList.remove('op-open'); document.body.style.overflow = ''");
    }

    public function confirmCod(OrderService $orderService): void
    {
        $this->placeOrder($orderService, 'cod');
    }

    public function confirmWhatsApp(OrderService $orderService): void
    {
        $this->placeOrder($orderService, 'whatsapp');
    }

    protected function placeOrder(OrderService $orderService, string $mode): void
    {
        if (! $this->open || $this->placed || $this->showPicker) {
            return;
        }

        // An add-on can require the customer to visit a step first: the filter
        // returns that step's key and the submit becomes a "continue" instead.
        $gate = Hooks::filter('popup.submit_gate', null, $this);

        if (is_string($gate) && $gate !== '' && $this->step !== $gate) {
            $this->gotoStep($gate);

            return;
        }

        // Bots fill the hidden field — pretend it worked, create nothing.
        if ($this->website !== '') {
            $this->placed = true;

            return;
        }

        $limitKey = 'order-popup:'.request()->ip();

        if (RateLimiter::tooManyAttempts($limitKey, 5)) {
            $this->dispatch('error', message: 'একটু অপেক্ষা করুন, কিছুক্ষণ পর আবার চেষ্টা করুন।');

            return;
        }

        $rules = (array) Hooks::filter('popup.validation_rules', $this->coreRules(), $this);

        $this->validate(
            $rules,
            (array) Hooks::filter('popup.validation_messages', [], $this),
            (array) Hooks::filter('popup.validation_attributes', [], $this)
        );

        try {
            $deliveryCharge = $this->zone === 'outside'
                ? (float) setting('dc_outside', 120)
                : (float) setting('dc_inside', 60);

            $order = $orderService->createOrder([
                'user_id' => auth()->id(),
                'invoice_no' => $orderService->generateInvoice(),
                'status' => 'pending',
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
                'channel' => $mode,
                'name' => trim($this->name),
                'phone' => PhoneNormalizer::normalize($this->phone),
                'email' => $this->email !== '' ? trim($this->email) : null,
                'shipping_address' => trim($this->address),
                'delivery_zone' => $this->zone,
                'delivery_charge' => $deliveryCharge,
                'total_amount' => 0, // recalculated from authoritative prices
                '_addons' => $this->addon,
            ], [[
                'id' => $this->productId,
                'variant_id' => $this->variantId,
                'image_option_id' => $this->optionId,
                'qty' => $this->qty,
            ]]);

            RateLimiter::hit($limitKey, 60);

            $this->placed = true;
            $this->placedInvoice = (string) $order->invoice_no;
            $this->waUrl = $this->buildWhatsAppUrl($order);
            $this->successData = (array) Hooks::filter('popup.success_data', [
                'order_id' => (int) $order->id,
                'invoice' => (string) $order->invoice_no,
                'total' => (float) $order->total_amount,
            ], $order, $this);

            if ($mode === 'whatsapp' && $this->waUrl !== '') {
                // May be blocked by the popup blocker; the success panel keeps a link.
                $this->js('window.open('.json_encode($this->waUrl).", '_blank')");
            }
        } catch (\Exception $e) {
            $this->dispatch('error', message: $e->getMessage());
        }
    }

    protected function buildWhatsAppUrl($order): string
    {
        $number = preg_replace('/\D+/', '', (string) setting('contact_phone')) ?? '';

        if ($number === '') {
            return '';
        }

        if (str_starts_with($number, '0')) {
            $number = '88'.$number;
        }

        $text = implode("\n", [
            'নতুন অর্ডার: '.$order->invoice_no,
            'পণ্য: '.$this->productName.' (x'.$this->qty.')',
            'মোট: '.setting('currency').' '.number_format((float) $order->total_amount, 2),
            'নাম: '.$order->name,
            'মোবাইল: '.$order->phone,
            'ঠিকানা: '.$order->shipping_address,
        ]);

        $text = (string) Hooks::filter('popup.whatsapp_text', $text, $order, $this);

        return 'https://wa.me/'.$number.'?text='.rawurlencode($text);
    }

    public function deliveryCharge(): float
    {
        return $this->zone === 'outside'
            ? (float) setting('dc_outside', 120)
            : (float) setting('dc_inside', 60);
    }

    public function total(): float
    {
        return ($this->unitPrice * $this->qty) + $this->deliveryCharge();
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

        $option = collect($this->pickerData['options'] ?? [])->firstWhere('id', (int) $this->optionId);

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

        if (! empty($this->pickerData['option_required']) && ! $this->optionId) {
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
            $combo = CombinationResolver::resolve($product, $this->matchedVariantId(), $this->optionId, $this->qty);
        } catch (CombinationException $e) {
            $this->variantId = null;
            $this->unitPrice = (float) $product->price_info['final'];
            $this->pickerStock = 0;

            return;
        }

        $this->variantId = $combo->variant?->id;
        $this->optionId = $combo->option?->id;
        $this->unitPrice = (float) $combo->unitPrice;
        $this->pickerStock = (int) $combo->available;
    }

    protected function resetPopupState(): void
    {
        $this->picks = [];
        $this->pickerData = [];
        $this->pickerRequired = false;
        $this->showPicker = false;
        $this->pickerError = '';
        $this->pickerStock = 0;
        $this->variantId = null;
        $this->optionId = null;
        $this->unitPrice = 0.0;
        $this->placed = false;
        $this->placedInvoice = '';
        $this->waUrl = '';
        $this->step = 'details';
        $this->addon = [];
        $this->successData = [];
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

        return view('livewire.frontend.order-popup', [
            'whatsappEnabled' => (string) setting('popup_order_whatsapp', '1') === '1',
            'availability' => $availability,
            'steps' => $this->steps(),
            'primaryLabel' => $this->primaryButtonLabel(),
        ]);
    }
}
