<div>
    <div class="ck-page"
        x-data="{
            deliveryTouched: false,
            paymentTouched: false,
            get s1() {
                const w = this.$wire;
                return String(w.name ?? '').trim().length > 0
                    && /^[0-9]{11}$/.test(String(w.phone ?? '').trim())
                    && String(w.shipping_address ?? '').trim().length > 0;
            },
            get s2() { return this.deliveryTouched && !!this.$wire.delivery_zone; },
            get s3() { return this.paymentTouched && !!this.$wire.payment_method; },
            get current() { return !this.s1 ? 1 : (!this.s2 ? 2 : (!this.s3 ? 3 : 0)); }
        }">

        {{-- ═══════════ STEP INDICATOR (fills up as the user progresses) ═══════════ --}}
        <div class="ck-steps">
            <div class="ck-step" :class="{ 'done': s1, 'current': current === 1 }">
                <span class="ck-step-dot">1</span>
                <span class="ck-step-label">বিস্তারিত তথ্য</span>
            </div>
            <div class="ck-step-line" :class="{ 'done': s1 }"></div>
            <div class="ck-step" :class="{ 'done': s2, 'current': current === 2 }">
                <span class="ck-step-dot">2</span>
                <span class="ck-step-label">ডেলিভারি</span>
            </div>
            <div class="ck-step-line" :class="{ 'done': s2 }"></div>
            <div class="ck-step" :class="{ 'done': s3, 'current': current === 3 }">
                <span class="ck-step-dot">3</span>
                <span class="ck-step-label">পেমেন্ট</span>
            </div>
        </div>

        <div class="ck-grid">

            {{-- ═══════════════════════════════════════════════════════════
                 LEFT: Address / Delivery / Note / Payment
            ═══════════════════════════════════════════════════════════ --}}
            <section class="ck-left">
                <form wire:submit.prevent="confirmOrder" id="orderForm">

                    {{-- ───────── Delivery Address ───────── --}}
                    <div class="ck-card">
                        <h2 class="ck-title">
                            <i class="fa-solid fa-user"></i>
                            ডেলিভারির ঠিকানা
                        </h2>

                        <div class="ck-field">
                            <i class="fa-regular fa-user ck-field-icon"></i>
                            <div class="ck-field-body">
                                <label for="ck-name">আপনার নাম *</label>
                                <input id="ck-name" type="text" wire:model.defer="name"
                                    placeholder="আপনার নাম লিখুন" required />
                            </div>
                        </div>
                        @error('name') <span class="ck-error">{{ $message }}</span> @enderror

                        <div class="ck-field">
                            <i class="fa-solid fa-phone-volume ck-field-icon"></i>
                            <div class="ck-field-body">
                                <label for="ck-phone">আপনার ফোন নম্বর *</label>
                                <input id="ck-phone" type="tel" wire:model.defer="phone"
                                    placeholder="আপনার ফোন নম্বর লিখুন"
                                    required minlength="11" maxlength="11" pattern="[0-9]{11}" />
                            </div>
                        </div>
                        @error('phone') <span class="ck-error">{{ $message }}</span> @enderror

                        <div class="ck-field ck-field-top">
                            <i class="fa-solid fa-location-dot ck-field-icon"></i>
                            <div class="ck-field-body">
                                <label for="ck-address">সম্পূর্ণ ঠিকানা *</label>
                                <textarea id="ck-address" wire:model.defer="shipping_address" rows="2"
                                    placeholder="রাস্তা, এরিয়া, থানা, জেলা লিখুন" required></textarea>
                            </div>
                        </div>
                        @error('shipping_address') <span class="ck-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- ───────── Delivery Option ───────── --}}
                    <div class="ck-card">
                        <h2 class="ck-title">
                            <i class="fa-solid fa-truck-fast"></i>
                            ডেলিভারি অপশন
                        </h2>

                        <div class="ck-zones">
                            <label class="ck-zone {{ $delivery_zone === 'inside' ? 'active' : '' }}"
                                @click="deliveryTouched = true">
                                <input type="radio" wire:model.live="delivery_zone" value="inside" />
                                <span class="ck-radio"></span>
                                <span class="ck-zone-icon"><i class="fa-solid fa-house"></i></span>
                                <span class="ck-zone-text">
                                    <strong>Inside Dhaka</strong>
                                    <small>{{ setting('currency', '৳') }} {{ setting('dc_inside', 60) }}</small>
                                </span>
                            </label>

                            <label class="ck-zone {{ $delivery_zone === 'outside' ? 'active' : '' }}"
                                @click="deliveryTouched = true">
                                <input type="radio" wire:model.live="delivery_zone" value="outside" />
                                <span class="ck-radio"></span>
                                <span class="ck-zone-icon"><i class="fa-regular fa-building"></i></span>
                                <span class="ck-zone-text">
                                    <strong>Outside Dhaka</strong>
                                    <small>{{ setting('currency', '৳') }} {{ setting('dc_outside', 120) }}</small>
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- ───────── Order Note ───────── --}}
                    <div class="ck-card">
                        <h2 class="ck-title">
                            <i class="fa-solid fa-pen-to-square"></i>
                            আপনার অর্ডার নোট লিখুন
                        </h2>
                        <div class="ck-field ck-field-plain">
                            <div class="ck-field-body">
                                <input type="text" wire:model.defer="note"
                                    placeholder="যদি কোন নির্দেশনা থাকে..." />
                            </div>
                        </div>
                    </div>

                    {{-- ───────── Payment Option ───────── --}}
                    <div class="ck-card">
                        <h2 class="ck-title">
                            <i class="fa-solid fa-wallet"></i>
                            পেমেন্ট অপশন
                        </h2>

                        <div class="ck-payments" id="paymentMethods">

                            {{-- Cash on Delivery --}}
                            <button type="button" wire:click="selectPaymentMethod('cod')"
                                @click="paymentTouched = true"
                                class="ck-pay {{ $payment_method === 'cod' ? 'active' : '' }}">
                                <span class="ck-pay-flag">
                                    @if($payment_method === 'cod')
                                    <i class="fa-solid fa-circle-check"></i>
                                    @else
                                    <i class="fa-regular fa-circle"></i>
                                    @endif
                                </span>
                                <span class="ck-pay-icon"><i class="fa-solid fa-money-bill-wave"></i></span>
                                <span class="ck-pay-text">
                                    <strong>Cash on Delivery</strong>
                                    <small>পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন</small>
                                </span>
                            </button>

                            {{-- SSL Commerz --}}
                            @if($sslEnabled)
                            <button type="button" wire:click="selectPaymentMethod('sslcommerz')"
                                @click="paymentTouched = true"
                                class="ck-pay {{ $payment_method === 'sslcommerz' ? 'active' : '' }}"
                                style="{{ $payment_method === 'sslcommerz' ? '--pay-color:#0a5c1a;' : '' }}">
                                <span class="ck-pay-flag">
                                    @if($payment_method === 'sslcommerz')
                                    <i class="fa-solid fa-circle-check"></i>
                                    @else
                                    <i class="fa-regular fa-circle"></i>
                                    @endif
                                </span>
                                <span class="ck-pay-icon"><i class="fa-solid fa-credit-card"></i></span>
                                <span class="ck-pay-text">
                                    <strong>Online Payment <small class="d-inline">(SSL Commerz)</small></strong>
                                    <small>কার্ড বা মোবাইল ব্যাংকিং দিয়ে পেমেন্ট করুন</small>
                                </span>
                            </button>
                            @endif

                            {{-- Manual Methods: bKash / Nagad / Rocket / Bank --}}
                            @foreach($manualMethods as $method)
                            <button type="button" wire:click="selectPaymentMethod('{{ $method['key'] }}')"
                                @click="paymentTouched = true"
                                class="ck-pay {{ $payment_method === $method['key'] ? 'active' : '' }}"
                                style="--pay-color: {{ $method['color'] }};">
                                <span class="ck-pay-flag">
                                    @if($payment_method === $method['key'])
                                    <i class="fa-solid fa-circle-check"></i>
                                    @else
                                    <i class="fa-regular fa-circle"></i>
                                    @endif
                                </span>
                                <span class="ck-pay-icon">
                                    <img src="{{ $method['img'] }}" alt="{{ $method['label'] }}"
                                        onerror="this.style.display='none'" />
                                </span>
                                <span class="ck-pay-text">
                                    <strong>{{ $method['label'] }}</strong>
                                    <small>
                                        @if($method['key'] === 'bank')
                                        ব্যাংক ট্রান্সফার করে Reference নম্বর দিন
                                        @else
                                        টাকা পাঠিয়ে Transaction ID দিন
                                        @endif
                                    </small>
                                </span>
                            </button>
                            @endforeach

                        </div>{{-- .ck-payments --}}

                        {{-- ─────────── PAYMENT INSTRUCTION PANEL ─────────── --}}
                        @if($activeManualMethod && $activeMethodDetails)
                        @php $m = $activeMethodDetails; @endphp

                        <div class="pay-instruction-panel"
                            style="border-color: {{ $m['color'] }}40; background: {{ $m['color'] }}0d;">

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
                                    <span><strong>Send Money</strong> (Personal) বা <strong>Payment</strong> (Merchant) সিলেক্ট করুন</span>
                                </div>
                                <div class="pip-step">
                                    <span class="pip-num" style="background:{{ $m['color'] }}">৩</span>
                                    <span>নিচের নম্বরে পরিমাণ <strong>৳ {{ number_format($total) }}</strong> পাঠান:</span>
                                </div>
                            </div>

                            <div class="pip-number-box" style="border-color:{{ $m['color'] }}50; background:{{ $m['color'] }}14;">
                                <span class="pip-label">{{ ucfirst($m['account_type'] ?? 'personal') }} Number</span>
                                <div class="pip-number">
                                    {{ $m['number'] }}
                                    <button type="button" class="pip-copy"
                                        onclick="navigator.clipboard.writeText('{{ $m['number'] }}'); this.innerHTML='<i class=\'fa-solid fa-check\'></i>'; setTimeout(()=>this.innerHTML='<i class=\'fa-regular fa-copy\'></i>',1500)"
                                        title="Copy number">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="pip-step">
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
                                <label class="pip-txn-label" for="ck-txn">
                                    Transaction ID / Ref. No. <span style="color:#dc3545">*</span>
                                </label>
                                <input id="ck-txn" type="text" wire:model.defer="transaction_id"
                                    class="pip-txn-input {{ $errors->has('transaction_id') ? 'is-error' : '' }}"
                                    placeholder="e.g. 8N7A3X2YQ1"
                                    style="border-color: {{ $errors->has('transaction_id') ? '#dc3545' : $m['color'] . '66' }}" />
                                @error('transaction_id')
                                <span class="ck-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>{{-- .pay-instruction-panel --}}
                        @endif

                        {{-- SSL Commerz info --}}
                        @if($payment_method === 'sslcommerz')
                        <div class="pay-instruction-panel" style="border-color:#0a5c1a40;background:#0a5c1a0d;">
                            <div class="pip-header" style="color:#0a5c1a">
                                <i class="fa-solid fa-lock"></i>
                                <strong>Secure Online Payment</strong>
                            </div>
                            <p class="pip-note mb-0">
                                অর্ডার কনফার্ম করার পর আপনাকে SSL Commerz নিরাপদ পেমেন্ট পেজে নিয়ে যাওয়া হবে।
                                Credit/Debit Card, bKash, Nagad সহ সকল পদ্ধতিতে পেমেন্ট করা যাবে।
                            </p>
                        </div>
                        @endif
                    </div>

                </form>
            </section>

            {{-- ═══════════════════════════════════════════════════════════
                 RIGHT: Order Summary
            ═══════════════════════════════════════════════════════════ --}}
            <aside class="ck-right">
                <div class="ck-card ck-summary">
                    <h2 class="ck-title">
                        <i class="fa-solid fa-bag-shopping"></i>
                        অর্ডার আইটেমস ({{ count($cartItems) }} Items)
                    </h2>

                    <div class="ck-items">
                        <livewire:frontend.components.cart-items :cartItems="$cartItems" />
                    </div>

                    <h2 class="ck-title ck-title-sm">
                        <i class="fa-solid fa-table-list"></i>
                        Summary
                    </h2>

                    <div class="ck-lines">
                        <div class="ck-line">
                            <span>সাব টোটাল:</span>
                            <strong>৳ {{ number_format($subtotal) }}</strong>
                        </div>
                        <div class="ck-line">
                            <span>ডিসকাউন্ট:</span>
                            <strong>-৳ {{ number_format($discount) }}</strong>
                        </div>
                        <div class="ck-line">
                            <span>ডেলিভারি চার্জ:</span>
                            <strong>
                                @if($delivery_charge == 0)
                                <span class="ck-free">ফ্রি</span>
                                @else
                                ৳ {{ number_format($delivery_charge) }}
                                @endif
                            </strong>
                        </div>
                        <div class="ck-line ck-total">
                            <span>সর্বমোট:</span>
                            <strong>৳ {{ number_format($total) }}</strong>
                        </div>
                    </div>

                    {{-- Coupon --}}
                    <div class="ck-coupon">
                        <label for="ck-coupon-input">
                            <i class="fa-solid fa-tag"></i>
                            আপনার কাছে কি কোন কুপন কোড আছে?
                        </label>
                        <div class="ck-coupon-row">
                            <input id="ck-coupon-input" type="text" wire:model.defer="coupon_code"
                                placeholder="Coupon code">
                            <button type="button" wire:click="applyCoupon" class="ck-apply">Apply</button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" form="orderForm" class="ck-submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            @if($payment_method === 'sslcommerz')
                            <i class="fa-solid fa-lock"></i> Proceed to Payment
                            @else
                            <i class="fa-solid fa-lock"></i> অর্ডার কনফার্ম করুন
                            @endif
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin"></i> অপেক্ষা করুন...
                        </span>
                    </button>

                    {{-- Active payment badge --}}
                    @if($payment_method)
                    <div class="ck-active-method">
                        @php
                        $labels = ['cod' => 'Cash on Delivery', 'sslcommerz' => 'SSL Commerz'];
                        $colors = ['cod' => '#333', 'sslcommerz' => '#0a5c1a'];
                        foreach($manualMethods as $mm) {
                        $labels[$mm['key']] = $mm['label'];
                        $colors[$mm['key']] = $mm['color'];
                        }
                        @endphp
                        <span style="color:{{ $colors[$payment_method] ?? '#333' }}">
                            <i class="fa-solid fa-shield-halved me-1"></i>
                            {{ $labels[$payment_method] ?? $payment_method }}
                        </span>
                        <span class="ck-muted">দিয়ে পেমেন্ট করবেন</span>
                    </div>
                    @endif
                </div>
            </aside>

        </div>
    </div>

    <style>
        .ck-page {
            --ck-orange: #f4511e;
            --ck-orange-soft: #fff3ee;
            --ck-orange-line: #ffd2c0;
            --ck-ink: #1f2430;
            --ck-muted: #6b7280;
            --ck-line: #e6e8ee;
            --ck-bg: #f6f7fb;
            max-width: 1140px;
            margin: 0 auto;
            padding: 14px 12px 28px;
            background: var(--ck-bg);
            color: var(--ck-ink);
        }

        .ck-page * {
            box-sizing: border-box;
        }

        /* ── Step indicator ── */
        .ck-steps {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            background: #fff;
            border-radius: 14px;
            padding: 14px 18px 12px;
            margin-bottom: 14px;
            box-shadow: 0 2px 10px rgba(31, 36, 48, .06);
        }

        .ck-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            min-width: 70px;
        }

        .ck-step-dot {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            color: var(--ck-muted);
            background: #fff;
            border: 1.5px solid var(--ck-line);
        }

        .ck-step-dot {
            transition: background .25s, border-color .25s, color .25s, box-shadow .25s;
        }

        /* "you are here" hint: first incomplete step gets an orange outline */
        .ck-step.current .ck-step-dot {
            border-color: var(--ck-orange);
            color: var(--ck-orange);
        }

        .ck-step.current .ck-step-label {
            color: var(--ck-ink);
        }

        /* completed step: solid orange fill */
        .ck-step.done .ck-step-dot {
            background: var(--ck-orange);
            border-color: var(--ck-orange);
            color: #fff;
            box-shadow: 0 0 0 5px var(--ck-orange-soft);
        }

        .ck-step-label {
            font-size: 12px;
            color: var(--ck-muted);
            text-align: center;
            transition: color .25s;
        }

        .ck-step.done .ck-step-label {
            color: var(--ck-ink);
            font-weight: 600;
        }

        .ck-step-line {
            flex: 1;
            height: 1.5px;
            background: var(--ck-line);
            margin-top: 17px;
            transition: background .25s;
        }

        .ck-step-line.done {
            background: var(--ck-orange);
        }

        /* ── Layout ── */
        .ck-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            align-items: start;
        }

        @media (min-width: 992px) {
            .ck-grid {
                grid-template-columns: minmax(0, 1fr) 400px;
                gap: 20px;
            }

            .ck-right {
                position: sticky;
                top: 16px;
            }

            .ck-page {
                padding: 20px 16px 40px;
            }
        }

        /* ── Cards ── */
        .ck-card {
            background: #fff;
            border-radius: 14px;
            padding: 7px 12px;
            margin-bottom: 12px;
            box-shadow: 0 2px 10px rgba(31, 36, 48, .06);
        }

        .ck-right .ck-card {
            margin-bottom: 0;
        }

        .ck-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 14px;
            color: var(--ck-ink);
        }

        .ck-title i {
            color: var(--ck-orange);
            font-size: 16px;
        }

        .ck-title-sm {
            font-size: 15px;
            margin: 16px 0 10px;
        }

        /* ── Fields ── */
        .ck-field {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid var(--ck-line);
            border-radius: 10px;
            padding: 4px 14px;
            margin-bottom: 12px;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
        }

        .ck-field:focus-within {
            border-color: var(--ck-orange);
            box-shadow: 0 0 0 3px var(--ck-orange-soft);
        }

        .ck-field-top {
            align-items: flex-start;
        }

        .ck-field-top .ck-field-icon {
            margin-top: 14px;
        }

        .ck-field-plain {
            padding: 10px 14px;
        }

        .ck-field-icon {
            font-size: 17px;
            color: #4b5563;
            width: 20px;
            text-align: center;
        }

        .ck-field-body {
            flex: 1;
            min-width: 0;
        }

        .ck-field-body label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 1px;
        }

        .ck-field-body input,
        .ck-field-body textarea {
            width: 100%;
            border: 0;
            outline: 0;
            padding: 2px 0;
            font-size: 14px;
            background: transparent;
            color: var(--ck-ink);
            resize: vertical;
        }

        .ck-field-body input::placeholder,
        .ck-field-body textarea::placeholder {
            color: #9aa1ad;
        }

        .ck-error {
            display: block;
            color: #dc3545;
            font-size: 12.5px;
            margin: -6px 0 10px 4px;
        }

        /* ── Delivery zones ── */
        .ck-zones {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .ck-zone {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1.5px solid var(--ck-line);
            background: #fafbfd;
            border-radius: 12px;
            padding: 12px 10px 12px 34px;
            cursor: pointer;
            margin: 0;
            transition: border-color .15s, background .15s;
        }

        .ck-zone input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .ck-radio {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1.5px solid #b8bdc8;
            background: #fff;
        }

        .ck-zone.active {
            border-color: var(--ck-orange);
            background: var(--ck-orange-soft);
        }

        .ck-zone.active .ck-radio {
            border-color: var(--ck-orange);
            background: radial-gradient(circle, var(--ck-orange) 0 45%, #fff 50%);
        }

        .ck-zone-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #4b5563;
            box-shadow: 0 1px 4px rgba(31, 36, 48, .1);
        }

        .ck-zone.active .ck-zone-icon {
            color: var(--ck-orange);
        }

        .ck-zone-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
            line-height: 1.35;
        }

        .ck-zone-text strong {
            font-size: 13px;
            font-weight: 600;
        }

        .ck-zone-text small {
            font-size: 13px;
            color: var(--ck-ink);
        }

        /* ── Payment cards ── */
        .ck-payments {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ck-pay {
            --pay-color: var(--ck-orange);
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            text-align: left;
            background: #fff;
            border: 1.5px solid var(--ck-line);
            border-radius: 12px;
            padding: 12px 14px;
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }

        .ck-pay.active {
            border-color: var(--pay-color);
            background: color-mix(in srgb, var(--pay-color) 6%, #fff);
        }

        .ck-pay-flag {
            font-size: 19px;
            color: #b8bdc8;
            display: flex;
        }

        .ck-pay.active .ck-pay-flag {
            color: var(--pay-color);
        }

        .ck-pay-icon {
            width: 46px;
            height: 34px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #f3f4f8;
            font-size: 20px;
            color: var(--ck-orange);
            overflow: hidden;
        }

        .ck-pay.active .ck-pay-icon {
            color: var(--pay-color);
        }

        .ck-pay-icon img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .ck-pay-text {
            display: flex;
            flex-direction: column;
            line-height: 1.4;
            min-width: 0;
        }

        .ck-pay-text strong {
            font-size: 14px;
            font-weight: 600;
        }

        .ck-pay-text small {
            font-size: 12px;
            color: var(--ck-muted);
        }

        /* ── Payment instruction panel ── */
        .pay-instruction-panel {
            margin-top: 14px;
            border: 1.5px solid;
            border-radius: 12px;
            padding: 14px;
        }

        .pip-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            margin-bottom: 12px;
        }

        .pip-steps {
            display: flex;
            flex-direction: column;
            gap: 9px;
            margin-bottom: 12px;
        }

        .pip-step {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13.5px;
            line-height: 1.5;
        }

        .pip-num {
            min-width: 22px;
            height: 22px;
            border-radius: 50%;
            color: #fff;
            font-size: 11.5px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1px;
        }

        .pip-number-box {
            border: 1.5px solid;
            border-radius: 10px;
            padding: 9px 14px;
            margin: 8px 0 12px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .pip-label {
            font-size: 12px;
            color: #666;
        }

        .pip-number {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: .05em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pip-copy {
            background: none;
            border: 0;
            cursor: pointer;
            color: inherit;
            opacity: .6;
            padding: 2px 5px;
            font-size: 14px;
        }

        .pip-copy:hover {
            opacity: 1;
        }

        .pip-bank-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 10px;
        }

        .pip-bank-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13.5px;
            gap: 12px;
        }

        .pip-bank-label {
            color: #666;
            min-width: 100px;
        }

        .pip-note {
            font-size: 13px;
            color: #555;
            margin: 8px 0 12px;
            line-height: 1.6;
        }

        .pip-txn {
            margin-top: 12px;
        }

        .pip-txn-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .pip-txn-input {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid;
            border-radius: 8px;
            font-size: 14.5px;
            outline: 0;
            background: #fff;
        }

        .pip-txn-input:focus {
            box-shadow: 0 0 0 3px rgba(0, 0, 0, .07);
        }

        .pip-txn-input.is-error {
            border-color: #dc3545 !important;
        }

        /* ── Summary ── */
        .ck-lines {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .ck-line {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .ck-line span {
            color: var(--ck-muted);
        }

        .ck-free {
            color: #198754;
            font-weight: 600;
        }

        .ck-total {
            border-top: 1px dashed var(--ck-line);
            padding-top: 11px;
            margin-top: 3px;
            font-size: 16px;
        }

        .ck-total span {
            color: var(--ck-ink);
            font-weight: 700;
        }

        .ck-total strong {
            color: var(--ck-orange);
            font-size: 18px;
        }

        .ck-coupon {
            margin-top: 16px;
        }

        .ck-coupon label {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 13.5px;
            margin-bottom: 8px;
        }

        .ck-coupon label i {
            color: var(--ck-orange);
        }

        .ck-coupon-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
        }

        .ck-coupon-row input {
            border: 1px solid var(--ck-line);
            border-radius: 10px;
            padding: 4px 12px;
            font-size: 14px;
            outline: 0;
            min-width: 0;
        }

        .ck-coupon-row input:focus {
            border-color: var(--ck-orange);
            box-shadow: 0 0 0 3px var(--ck-orange-soft);
        }

        .ck-apply {
            border: 1.5px solid var(--ck-orange);
            background: #fff;
            color: var(--ck-orange);
            font-weight: 600;
            border-radius: 10px;
            padding: 0 18px;
            cursor: pointer;
        }

        .ck-apply:hover {
            background: var(--ck-orange-soft);
        }

        .ck-submit {
            width: 100%;
            margin-top: 18px;
            border: 0;
            border-radius: 10px;
            padding: 13px 16px;
            font-size: 16px;
            font-weight: 600;
            color: var(--cart-btn-text, #fff);
            background: var(--cart-btn-bg, var(--ck-orange));
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .15s;
            margin-bottom: 20px;
        }

        .ck-submit:hover {
            filter: brightness(.92);
        }

        .ck-submit:disabled {
            opacity: .7;
            cursor: not-allowed;
        }

        .ck-submit:focus-visible,
        .ck-pay:focus-visible,
        .ck-apply:focus-visible {
            outline: 3px solid var(--ck-orange-line);
            outline-offset: 2px;
        }

        .ck-active-method {
            text-align: center;
            font-size: 12.5px;
            margin-top: 10px;
            opacity: .8;
        }

        .ck-muted {
            color: #888;
        }

        /* ── Small phones ── */
        @media (max-width: 360px) {
            .ck-page {
                padding: 10px 8px 24px;
            }

            .ck-card {
                padding: 7px 10px;
            }

            .ck-zones {
                grid-template-columns: 1fr;
            }

            .ck-step {
                min-width: 56px;
            }

            .ck-step-label {
                font-size: 11px;
            }

            .ck-pay-text small {
                font-size: 11px;
            }

            .ck-pay {
                padding: 10px;
                gap: 9px;
            }
        }
    </style>
</div>