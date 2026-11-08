<div class="op-root">
@include("livewire.frontend.partials.popup-styles")

    @if ($open)
        <div class="op-layer" wire:keydown.escape.window="close">
            <div class="op-backdrop" wire:click="close"></div>

            <div class="op-card" role="dialog" aria-modal="true" aria-labelledby="op-title">
                @if ($placed)
                    <div class="op-success">
                        <div class="op-check"><i class="fas fa-check"></i></div>
                        <h3>অর্ডার সফলভাবে জমা হয়েছে!</h3>
                        <p>ইনভয়েস নম্বর: <strong>{{ $placedInvoice }}</strong></p>
                        <p>আমাদের প্রতিনিধি শীঘ্রই কল করে অর্ডারটি কনফার্ম করবেন।</p>
                        @hook('popup.success.extra', ['invoice' => $placedInvoice, 'data' => $successData])

                        <div class="op-actions">
                            @if ($waUrl !== '')
                                <a class="op-btn op-btn--wa" href="{{ $waUrl }}" target="_blank" rel="noopener">
                                    <i class="fab fa-whatsapp"></i> WhatsApp-এ চ্যাট করুন
                                </a>
                            @endif
                            <button type="button" class="op-btn op-btn--cod" wire:click="close">ঠিক আছে</button>
                        </div>
                    </div>
                @else
                    <div class="op-head">
                        <div>
                            <h3 id="op-title">
                                {{ $showPicker ? 'আপনার পছন্দ বেছে নিন' : setting('popup_order_title', 'Order Now') }}
                            </h3>
                            <p>
                                @if ($showPicker)
                                    {{ collect($pickerData['groups'] ?? [])->pluck('name')->implode(' · ') }}{{ ! empty($pickerData['options']) ? ' · ডিজাইন' : '' }} বেছে নিন
                                @else
                                    ক্যাশ অন ডেলিভারিতে অর্ডার করুন
                                @endif
                            </p>
                        </div>
                        <button type="button" class="op-x" wire:click="close" aria-label="বন্ধ করুন">&times;</button>
                    </div>

                    <div class="op-prod">
                        <div>
                            <strong>{{ $productName }}</strong>
                            <small>
                                @if (! $showPicker && $this->pickedLabel() !== '')
                                    {{ $this->pickedLabel() }} ·
                                @endif
                                পরিমাণ: {{ $qty }}
                            </small>
                        </div>
                        <span>{{ setting('currency') }} {{ number_format($unitPrice, 2) }}</span>
                    </div>

                    @if ($showPicker)
                        <div class="op-picker">
                            <x-variant-picker
                                :groups="$pickerData['groups'] ?? []"
                                :picks="$picks"
                                :availability="$availability"
                                :options="$pickerData['options'] ?? []"
                                :option-id="$optionId"
                                :option-required="$pickerData['option_required'] ?? false"
                                select="selectPick"
                                select-option="selectPickerOption" />

                            <div class="vp-qty">
                                <span>পরিমাণ</span>
                                <div class="vp-stepper">
                                    <button type="button" wire:click="decreaseQty" @disabled($qty <= 1)>&minus;</button>
                                    <b>{{ $qty }}</b>
                                    <button type="button" wire:click="increaseQty"
                                        @disabled($pickerStock > 0 && $qty >= $pickerStock)>+</button>
                                </div>
                            </div>

                            @if ($pickerError !== '')
                                <div class="vp-note">{{ $pickerError }}</div>
                            @endif

                            <div class="op-actions">
                                <button type="button" class="op-btn op-btn--cod" wire:click="continueToForm">
                                    চালিয়ে যান <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    @else
                    <form wire:submit="confirmCod" novalidate>
                        @php
                            $stepSlot = $this->stepSlot();
                            $hookData = [
                                'component' => $this,
                                'product_id' => $productId,
                                'variant_id' => $variantId,
                                'option_id' => $optionId,
                                'qty' => $qty,
                                'zone' => $zone,
                                'unit_price' => $unitPrice,
                                'subtotal' => $unitPrice * $qty,
                                'shipping' => $this->deliveryCharge(),
                                'total' => $this->total(),
                                'currency' => setting('currency'),
                            ];
                        @endphp

                        @if ($pickerRequired)
                            <div class="op-picked">
                                <span>{{ $this->pickedLabel() }}</span>
                                <button type="button" wire:click="editPicker">পরিবর্তন</button>
                            </div>
                        @endif
                        <div class="op-hp" aria-hidden="true">
                            <label>Website</label>
                            <input type="text" tabindex="-1" autocomplete="off" wire:model="website">
                        </div>

                        @if ($stepSlot === null)
                        <div class="op-field">
                            <label for="op-name">আপনার নাম *</label>
                            <input id="op-name" type="text" wire:model="name" placeholder="আপনার নাম লিখুন"
                                autocomplete="name">
                            @error('name') <span class="op-err">{{ $message }}</span> @enderror
                        </div>

                        <div class="op-field">
                            <label for="op-phone">মোবাইল নাম্বার *</label>
                            <input id="op-phone" type="tel" wire:model="phone" placeholder="01712345678"
                                inputmode="numeric" autocomplete="tel">
                            @error('phone') <span class="op-err">{{ $message }}</span> @enderror
                        </div>

                        <div class="op-field">
                            <label for="op-address">সম্পূর্ণ ঠিকানা *</label>
                            <textarea id="op-address" wire:model="address" rows="2"
                                placeholder="বাসা/রোড, এলাকা, থানা, জেলা"></textarea>
                            @error('address') <span class="op-err">{{ $message }}</span> @enderror
                        </div>

                        <div class="op-field">
                            <label for="op-email">ইমেইল (ঐচ্ছিক)</label>
                            <input id="op-email" type="email" wire:model="email" placeholder="you@example.com"
                                autocomplete="email">
                            @error('email') <span class="op-err">{{ $message }}</span> @enderror
                        </div>

                        <div class="op-field">
                            <label>ডেলিভারি এরিয়া *</label>
                            <div class="op-zones">
                                <label class="op-zone {{ $zone === 'inside' ? 'is-active' : '' }}">
                                    <input type="radio" wire:model.live="zone" value="inside">
                                    <span>ঢাকার ভিতরে</span>
                                    <strong>{{ setting('currency') }} {{ number_format((float) setting('dc_inside', 60), 0) }}</strong>
                                </label>
                                <label class="op-zone {{ $zone === 'outside' ? 'is-active' : '' }}">
                                    <input type="radio" wire:model.live="zone" value="outside">
                                    <span>ঢাকার বাইরে</span>
                                    <strong>{{ setting('currency') }} {{ number_format((float) setting('dc_outside', 120), 0) }}</strong>
                                </label>
                            </div>
                            @error('zone') <span class="op-err">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        <div class="op-summary">
                            <div class="op-row">
                                <span>সাবটোটাল</span>
                                <span>{{ setting('currency') }} {{ number_format($unitPrice * $qty, 2) }}</span>
                            </div>
                            <div class="op-row">
                                <span>ডেলিভারি চার্জ</span>
                                <span>{{ setting('currency') }} {{ number_format($this->deliveryCharge(), 2) }}</span>
                            </div>
                            <div class="op-row op-total">
                                <span>সর্বমোট</span>
                                <span>{{ setting('currency') }} {{ number_format($this->total(), 2) }}</span>
                            </div>
                            @hook('popup.summary.extra', $hookData)
                        </div>

                        @if ($stepSlot !== null)
                            @hook($stepSlot, $hookData)
                        @endif

                        <div class="op-actions">
                            <button type="submit" class="op-btn op-btn--cod" wire:loading.attr="disabled"
                                wire:target="confirmCod,confirmWhatsApp">
                                <span wire:loading.remove wire:target="confirmCod,confirmWhatsApp">
                                    <i class="fas fa-check"></i> {{ $primaryLabel }}
                                </span>
                                <span wire:loading wire:target="confirmCod,confirmWhatsApp">অপেক্ষা করুন...</span>
                            </button>

                            @if ($whatsappEnabled)
                                <button type="button" class="op-btn op-btn--wa" wire:click="confirmWhatsApp"
                                    wire:loading.attr="disabled" wire:target="confirmCod,confirmWhatsApp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp-এ অর্ডার
                                </button>
                            @endif

                            @hook('popup.footer', $hookData)
                        </div>
                    </form>
                    @endif
                @endif
            </div>
        </div>
    @endif
</div>

@script
    <script>
        window.addEventListener('pagehide', () => { document.body.style.overflow = ''; });
    </script>
@endscript
