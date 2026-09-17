<div>
    <div class="container checkout-page py-4">
        <div class="co-grid">

            {{-- ═══════════════════════════════════════════════════════════
                 LEFT: Address / Delivery / Payment
            ═══════════════════════════════════════════════════════════ --}}
            <section class="co-left">
                <h2 class="co-title">ডেলিভারির ঠিকানা</h2>

                <form class="co-form" wire:submit.prevent="confirmOrder" id="orderForm">

                    {{-- Name + Phone --}}
                    <div class="co-row">
                        <div class="co-field">
                            <label>আপনার নাম*</label>
                            <input type="text" wire:model.defer="name" placeholder="আপনার নাম লিখুন" required />
                            @error('name') <span class="co-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="co-field">
                            <label>আপনার ফোন নম্বর*</label>
                            <input type="tel" wire:model.defer="phone" placeholder="আপনার ফোন নম্বর"
                                required minlength="11" maxlength="11" pattern="[0-9]{11}" />
                            @error('phone') <span class="co-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="co-field">
                        <label>সম্পূর্ণ ঠিকানা*</label>
                        <textarea wire:model.defer="shipping_address" rows="3"
                            placeholder="রাস্তা, এরিয়া, থানা, জেলা" required></textarea>
                        @error('shipping_address') <span class="co-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Delivery Zone --}}
                    <div style="box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px; padding: 7px">
                        <h3 class="co-sub">Select Delivery Option</h3>
                        <div class="co-delivery">
                            <label class="radio">
                                <input type="radio" wire:model.live="delivery_zone" value="inside" />
                                <div>
                                    <span>Inside Dhaka</span>
                                    <span>({{ setting('currency') }} {{ setting('dc_inside', 60) }})</span>
                                </div>
                            </label>
                            <label class="radio">
                                <input type="radio" wire:model.live="delivery_zone" value="outside" />
                                <div>
                                    <span>Outside Dhaka</span>
                                    <span>({{ setting('currency') }} {{ setting('dc_outside', 120) }})</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Note --}}
                    <div class="co-field" style="box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px; padding: 7px">
                        <label>আপনার অর্ডার নোট লিখুন</label>
                        <input type="text" wire:model.defer="note" placeholder="যদি কোন নির্দেশনা থাকে" />
                    </div>

                    {{-- ─────────────────────────────────────────────────
                         PAYMENT METHODS
                    ───────────────────────────────────────────────── --}}
                    <h3 class="co-sub">Payment Option</h3>

                    <div class="co-payments" id="paymentMethods">

                        {{-- Cash on Delivery --}}
                        <button type="button"
                            wire:click="selectPaymentMethod('cod')"
                            class="pay-card {{ $payment_method === 'cod' ? 'active' : '' }}">
                            <span class="pay-flag">
                                @if($payment_method === 'cod')
                                <i class="fa-solid fa-circle-check"></i>
                                @else
                                <i class="fa-regular fa-circle"></i>
                                @endif
                            </span>

                            <span class="pay-icon">
                                <img src="img/cod.png" alt="COD" onerror="this.style.display='none'" />
                            </span>
                            <span class="pay-text">Cash on Delivery</span>
                        </button>

                        {{-- SSL Commerz --}}
                        @if($sslEnabled)
                        <button type="button"
                            wire:click="selectPaymentMethod('sslcommerz')"
                            class="pay-card {{ $payment_method === 'sslcommerz' ? 'active' : '' }}">
                            <span class="pay-flag">
                                @if($payment_method === 'sslcommerz')
                                <i class="fa-solid fa-circle-check"></i>
                                @else
                                <i class="fa-regular fa-circle"></i>
                                @endif
                            </span>
                            <span class="pay-icon">
                                <img src="img/sslcommerz.png" alt="SSLCommerz" onerror="this.style.display='none'" />
                            </span>
                            <span class="pay-text">Online Payment <small>(SSL Commerz)</small></span>
                        </button>
                        @endif

                        {{-- Manual Methods: bKash / Nagad / Rocket / Bank --}}
                        @foreach($manualMethods as $method)
                        <button type="button"
                            wire:click="selectPaymentMethod('{{ $method['key'] }}')"
                            class="pay-card {{ $payment_method === $method['key'] ? 'active' : '' }}"
                            style="{{ $payment_method === $method['key'] ? 'border-color:' . $method['color'] . ';' : '' }}">
                            <span class="pay-flag">
                                @if($payment_method === $method['key'])
                                <i class="fa-solid fa-circle-check" style="color:{{ $method['color'] }}"></i>
                                @else
                                <i class="fa-regular fa-circle"></i>
                                @endif
                            </span>
                            <span class="pay-icon">
                                <img src="{{ $method['img'] }}" alt="{{ $method['label'] }}"
                                    onerror="this.style.display='none'" />
                            </span>
                            <span class="pay-text">{{ $method['label'] }}</span>
                        </button>
                        @endforeach

                    </div>{{-- .co-payments --}}

                    {{-- ─────────────────────────────────────────────────
                         PAYMENT INSTRUCTION PANEL
                         Shows when a manual method is selected
                    ───────────────────────────────────────────────── --}}
                    @if($activeManualMethod && $activeMethodDetails)
                    @php $m = $activeMethodDetails; @endphp

                    <div class="pay-instruction-panel" style="border-color: {{ $m['color'] }}20; background: {{ $m['color'] }}08;">

                        <div class="pip-header" style="color: {{ $m['color'] }}">
                            <i class="fa-solid fa-circle-info"></i>
                            <strong>{{ $m['label'] }} পেমেন্টের নির্দেশনা</strong>
                        </div>

                        @if($m['key'] !== 'bank')
                        {{-- Mobile Banking (bKash / Nagad / Rocket) --}}
                        <div class="pip-steps">
                            <div class="pip-step">
                                <span class="pip-num" style="background:{{ $m['color'] }}">১</span>
                                <span>আপনার {{ $m['label'] }} অ্যাপ বা ডায়াল <strong>*247#</strong> / <strong>*322#</strong> খুলুন</span>
                            </div>
                            <div class="pip-step">
                                <span class="pip-num" style="background:{{ $m['color'] }}">২</span>
                                <span>
                                    <strong>Send Money</strong> (Personal) বা <strong>Payment</strong> (Merchant) সিলেক্ট করুন
                                </span>
                            </div>
                            <div class="pip-step">
                                <span class="pip-num" style="background:{{ $m['color'] }}">৩</span>
                                <span>
                                    নিচের নম্বরে পরিমাণ
                                    <strong>৳ {{ number_format($total) }}</strong> পাঠান:
                                </span>
                            </div>
                        </div>

                        <div class="pip-number-box" style="border-color:{{ $m['color'] }}40; background:{{ $m['color'] }}12;">
                            <span class="pip-label">
                                {{ ucfirst($m['account_type'] ?? 'personal') }} Number
                            </span>
                            <div class="pip-number">
                                {{ $m['number'] }}
                                <button type="button" class="pip-copy"
                                    onclick="navigator.clipboard.writeText('{{ $m['number'] }}'); this.innerHTML='<i class=\'fa-solid fa-check\'></i>'; setTimeout(()=>this.innerHTML='<i class=\'fa-regular fa-copy\'></i>',1500)"
                                    title="Copy number">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="pip-step mt-2">
                            <span class="pip-num" style="background:{{ $m['color'] }}">৪</span>
                            <span>পেমেন্ট সম্পন্ন হলে নিচে <strong>Transaction ID</strong> লিখুন</span>
                        </div>

                        @else
                        {{-- Bank Transfer --}}
                        <div class="pip-bank-details">
                            <div class="pip-bank-row">
                                <span class="pip-bank-label">Bank Name</span>
                                <strong>{{ $m['bank_name'] }}</strong>
                            </div>
                            <div class="pip-bank-row">
                                <span class="pip-bank-label">Account Name</span>
                                <strong>{{ $m['account_name'] }}</strong>
                            </div>
                            <div class="pip-bank-row">
                                <span class="pip-bank-label">Account No.</span>
                                <strong>
                                    {{ $m['account_number'] }}
                                    <button type="button" class="pip-copy"
                                        onclick="navigator.clipboard.writeText('{{ $m['account_number'] }}'); this.innerHTML='<i class=\'fa-solid fa-check\'></i>'; setTimeout(()=>this.innerHTML='<i class=\'fa-regular fa-copy\'></i>',1500)">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </strong>
                            </div>
                            @if($m['routing'])
                            <div class="pip-bank-row">
                                <span class="pip-bank-label">Routing No.</span>
                                <strong>{{ $m['routing'] }}</strong>
                            </div>
                            @endif
                            @if($m['branch'])
                            <div class="pip-bank-row">
                                <span class="pip-bank-label">Branch</span>
                                <strong>{{ $m['branch'] }}</strong>
                            </div>
                            @endif
                            <div class="pip-bank-row">
                                <span class="pip-bank-label">Amount</span>
                                <strong style="color:{{ $m['color'] }}">৳ {{ number_format($total) }}</strong>
                            </div>
                        </div>
                        <p class="pip-note">ট্রান্সফার সম্পন্ন হলে নিচে Transaction ID / Reference নম্বর লিখুন।</p>
                        @endif

                        {{-- Transaction ID input --}}
                        <div class="pip-txn">
                            <label class="pip-txn-label">
                                Transaction ID / Ref. No. <span style="color:red">*</span>
                            </label>
                            <input type="text"
                                wire:model.defer="transaction_id"
                                class="pip-txn-input {{ $errors->has('transaction_id') ? 'is-error' : '' }}"
                                placeholder="e.g. 8N7A3X2YQ1"
                                style="border-color: {{ $errors->has('transaction_id') ? '#dc3545' : $m['color'] }}40" />
                            @error('transaction_id')
                            <span class="co-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>{{-- .pay-instruction-panel --}}
                    @endif

                    {{-- SSL Commerz info --}}
                    @if($payment_method === 'sslcommerz')
                    <div class="pay-instruction-panel" style="border-color:#0a5c1a20;background:#0a5c1a08;">
                        <div class="pip-header" style="color:#0a5c1a">
                            <i class="fa-solid fa-lock"></i>
                            <strong>Secure Online Payment</strong>
                        </div>
                        <p class="pip-note">
                            অর্ডার কনফার্ম করার পর আপনাকে SSL Commerz নিরাপদ পেমেন্ট পেজে নিয়ে যাওয়া হবে।
                            Credit/Debit Card, bKash, Nagad সহ সকল পদ্ধতিতে পেমেন্ট করা যাবে।
                        </p>
                    </div>
                    @endif

                </form>
            </section>

            {{-- ═══════════════════════════════════════════════════════════
                 RIGHT: Order Summary
            ═══════════════════════════════════════════════════════════ --}}
            <aside class="co-right">
                <div class="co-summary" style="box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px; padding: 7px">
                    <div class="co-summary-title">
                        <i class="fa-solid fa-bag-shopping"></i>
                        অর্ডার আইটেমস ({{ count($cartItems) }} Items)
                    </div>

                    <div class="co-items">
                        <livewire:frontend.components.cart-items :cartItems="$cartItems" />
                    </div>

                    <div class="summery-icon">
                        <i class="fa-solid fa-table-list mt-1" style="color: orangered;"></i>
                        <span>Summery</span>
                    </div>

                    <dicv class="co-lines">
                        <div class="line">
                            <span>সাব টোটাল:</span>
                            <strong>৳ {{ number_format($subtotal) }}</strong>
                        </div>
                        <div class="line">
                            <span>ডিসকাউন্ট:</span>
                            <strong>-৳ {{ number_format($discount) }}</strong>
                        </div>
                        <div class="line">
                            <span>ডেলিভারি চার্জ:</span>
                            <strong>
                                @if($delivery_charge == 0)
                                <span class="text-success fw-semibold">ফ্রি</span>
                                @else
                                ৳ {{ number_format($delivery_charge) }}
                                @endif
                            </strong>
                        </div>
                        <div class="line total">
                            <span>সর্বমোট:</span>
                            <strong>৳ {{ number_format($total) }}</strong>
                        </div>
                    </dicv>

                    {{-- Coupon --}}
                    <div class="co-coupon">
                        <label class="d-flex gap-1 mb-1">
                            <i class="fa-solid fa-tag mt-2" style="color: orangered;"></i>
                             আপনার কাছে কি কোন কুপন কোড আছে?
                        </label>
                        <div class="coupon-row">
                            <input type="text" wire:model.defer="coupon_code"
                                class="co-coupon-input" placeholder="Coupon code">
                            <button type="button" wire:click="applyCoupon"
                                class="btn co-apply">Apply</button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" form="orderForm"
                        class="btn mt-4 w-full co-submit"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            @if($payment_method === 'sslcommerz')
                            <i class="fa-solid fa-bag-shopping"></i> Proceed to Payment
                            @else
                            অর্ডার নিশ্চিত করুন
                            @endif
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin"></i> অপেক্ষা করুন...
                        </span>
                    </button>

                    {{-- Active payment badge --}}
                    @if($payment_method)
                    <div class="co-active-method">
                        @php
                        $labels = ['cod' => 'Cash on Delivery', 'sslcommerz' => 'SSL Commerz'];
                        foreach($manualMethods as $mm) $labels[$mm['key']] = $mm['label'];
                        $colors = ['cod' => '#333', 'sslcommerz' => '#0a5c1a'];
                        foreach($manualMethods as $mm) $colors[$mm['key']] = $mm['color'];
                        @endphp
                        <span style="color:{{ $colors[$payment_method] ?? '#333' }}">
                            <i class="fa-solid fa-shield-halved me-1"></i>
                            {{ $labels[$payment_method] ?? $payment_method }}
                        </span>
                        <span class="co-method-tag">দিয়ে পেমেন্ট করবেন</span>
                    </div>
                    @endif

                </div>
            </aside>

        </div>
    </div>

    {{-- ── Inline styles for payment instruction panel ──────────────────── --}}
    <style>
        .checkout-page {
            background: white;
            padding: 0px 10px;
        }

        .pay-instruction-panel {
            margin-top: 1rem;
            border: 1.5px solid;
            border-radius: 10px;
            padding: 1.1rem 1.2rem;
            transition: all .25s;
        }

        .pip-header {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .95rem;
            margin-bottom: .85rem;
        }

        .pip-steps {
            display: flex;
            flex-direction: column;
            gap: .55rem;
            margin-bottom: .85rem;
        }

        .pip-step {
            display: flex;
            align-items: flex-start;
            gap: .65rem;
            font-size: .88rem;
            line-height: 1.5;
        }

        .pip-num {
            min-width: 22px;
            height: 22px;
            border-radius: 50%;
            color: #fff;
            font-size: .72rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .pip-number-box {
            border: 1.5px solid;
            border-radius: 8px;
            padding: .6rem 1rem;
            margin: .5rem 0 .85rem;
            display: flex;
            flex-direction: column;
            gap: .15rem;
        }

        .pip-label {
            font-size: .75rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .pip-number {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: .05em;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .pip-copy {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: .6;
            padding: .1rem .3rem;
            font-size: .85rem;
            transition: opacity .2s;
        }

        .pip-copy:hover {
            opacity: 1;
        }

        .pip-bank-details {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            margin-bottom: .75rem;
        }

        .pip-bank-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .88rem;
            gap: 1rem;
        }

        .pip-bank-label {
            color: #666;
            min-width: 110px;
        }

        .pip-note {
            font-size: .82rem;
            color: #555;
            margin: .5rem 0 .85rem;
            line-height: 1.6;
        }

        .pip-txn {
            margin-top: .75rem;
        }

        .pip-txn-label {
            display: block;
            font-size: .85rem;
            font-weight: 600;
            margin-bottom: .35rem;
        }

        .pip-txn-input {
            width: 100%;
            padding: .55rem .8rem;
            border: 1.5px solid;
            border-radius: 7px;
            font-size: .95rem;
            outline: none;
            transition: box-shadow .2s;
        }

        .pip-txn-input:focus {
            box-shadow: 0 0 0 3px rgba(0, 0, 0, .08);
        }

        .pip-txn-input.is-error {
            border-color: #dc3545 !important;
        }

        .co-error {
            color: #dc3545;
            font-size: .8rem;
            display: block;
            margin-top: .2rem;
        }

        .co-active-method {
            text-align: center;
            font-size: .82rem;
            margin-top: .6rem;
            padding: .4rem;
            opacity: .75;
        }

        .co-method-tag {
            color: #888;
        }


        /* ══ CHECKOUT PAGE MOBILE FIX ══ */
        @media (max-width: 360px) {

            .checkout-page {
                padding: 10px 7px !important;
            }

            .co-grid {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }

            /* Stack Name + Phone vertically instead of side by side */
            .co-row {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }

            .co-field label {
                font-size: 14px !important;
            }

            .co-field input,
            .co-field textarea {
                width: 100% !important;
                box-sizing: border-box !important;
                font-size: 14px !important;
            }

            /* Delivery radio buttons — prevent overflow */
            .co-delivery {
                flex-direction: column !important;
                gap: 5px !important;
            }

            .co-delivery .radio {
                font-size: 10px !important;
                font-weight: 600 !important;
            }

            /* Payment cards */
            .co-payments {
                gap: 8px !important;
            }

            .pay-card {
                padding: 10px 8px !important;
                font-size: 13px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .pay-icon img {
                width: 32px !important;
                height: 22px !important;
            }

            /* Order summary right side */
            .co-right {
                width: 100% !important;
            }

            .co-summary {
                padding: 12px 10px !important;
            }

            /* Coupon row */
            .coupon-row {
                grid-template-columns: 1fr auto !important;
                gap: 6px !important;
            }

            /* Submit button */
            .co-submit {
                width: 100% !important;
                padding: 8px !important;
                font-size: 15px !important;
            }
        }
    </style>
</div>