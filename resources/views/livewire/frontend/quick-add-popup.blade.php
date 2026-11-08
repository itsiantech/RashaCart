<div>
    @if ($open)
        @include('livewire.frontend.partials.popup-styles')
        <div class="op-layer" role="dialog" aria-modal="true" aria-labelledby="quick-add-title"
            wire:keydown.escape.window="close">
            <div class="op-backdrop" wire:click="close"></div>
            <div class="op-card">
                <div class="op-head">
                    <div>
                        <h3 id="quick-add-title">আপনার পছন্দ বেছে নিন</h3>
                        <p>
                            {{ collect($pickerData['groups'] ?? [])->pluck('name')->implode(' · ') }}{{ ! empty($pickerData['options']) ? ' · ডিজাইন' : '' }}
                            বেছে নিয়ে কার্টে যোগ করুন
                        </p>
                    </div>
                    <button type="button" class="op-x" wire:click="close" aria-label="বন্ধ করুন">&times;</button>
                </div>

                <div class="op-prod">
                    <div>
                        <strong>{{ $productName }}</strong>
                        <small>{{ $productSku !== '' ? $productSku : 'পরিমাণ: ' . $quantity }}</small>
                    </div>
                    <span>{{ setting('currency') }} {{ number_format($unitPrice, 2) }}</span>
                </div>

                <x-variant-picker
                    :groups="$pickerData['groups'] ?? []"
                    :picks="$picks"
                    :availability="$availability"
                    :options="$pickerData['options'] ?? []"
                    :option-id="$selectedOption"
                    :option-required="$pickerData['option_required'] ?? false"
                    select="selectPick"
                    select-option="selectPickerOption" />

                <div class="vp-qty">
                    <span>পরিমাণ</span>
                    <div class="vp-stepper">
                        <button type="button" wire:click="dropQty" @disabled((int) $quantity <= 1)>&minus;</button>
                        <b>{{ $quantity }}</b>
                        <button type="button" wire:click="bumpQty"
                            @disabled((int) $quantity >= max((int) $pickerStock, 1))>+</button>
                    </div>
                </div>

                @if ($pickerError !== '')
                    <div class="vp-note">{{ $pickerError }}</div>
                @endif

                <div class="op-summary">
                    <div class="op-row op-total">
                        <span>সর্বমোট</span>
                        <span>{{ setting('currency') }} {{ number_format($unitPrice * (int) $quantity, 2) }}</span>
                    </div>
                </div>

                <div class="op-actions">
                    <button type="button" class="op-btn op-btn--cod" wire:click="confirmQuickAdd"
                        wire:loading.attr="disabled" wire:target="confirmQuickAdd">
                        <span wire:loading.remove wire:target="confirmQuickAdd">
                            <i class="fas fa-shopping-cart"></i> {{ \App\Services\PurchaseActions::addToCartLabel() }}
                        </span>
                        <span wire:loading wire:target="confirmQuickAdd">যোগ হচ্ছে...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
