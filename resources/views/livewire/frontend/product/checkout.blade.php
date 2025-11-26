<div>
    <div class="container py-4">
        <div class="co-grid">
            <!-- Left: Address / Delivery / Payment -->
            <section class="co-left">
                <h2 class="co-title">ডেলিভারির ঠিকানা</h2>
                <form class="co-form" wire:submit.prevent="confirmOrder">
                    <div class="co-row">
                        
                        <div class="co-field">
                            <label>আপনার নাম*</label>
                            <input type="text" wire:model.defer="name" placeholder="আপনার নাম লিখুন" required />
                            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                       <div class="co-field">
                            <label>আপনার ফোন নম্বর*</label>
                            <input type="tel"
                                wire:model.defer="phone"
                                placeholder="আপনার ফোন নম্বর"
                                required
                                minlength="11"
                                maxlength="11"
                                pattern="[0-9]{11}" />

                            @error('phone')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                                                
                    </div>

                    <div class="co-field">
                        <label>সম্পূর্ণ ঠিকানা*</label>
                        <textarea wire:model.defer="shipping_address" rows="3" placeholder="রাস্তা, এরিয়া, থানা, জেলা" required></textarea>
                        @error('shipping_address') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <h3 class="co-sub">Select Delivery Option</h3>
                    <div class="co-delivery">
                        <label class="radio">
                            <input type="radio" wire:model.live="delivery_zone" value="inside" />
                            <span>Inside Dhaka ({{Setting('currency')}} {{ setting('dc_inside')}})</span>
                        </label>
                        <label class="radio">
                            <input type="radio" wire:model.live="delivery_zone" value="outside" />
                            <span>Outside Dhaka ({{Setting('currency')}} {{setting('dc_outside')}})</span>
                        </label>
                    </div>

                    <div class="co-field">
                        <label>আপনার অর্ডার নোট লিখুন</label>
                        <input type="text" wire:model.defer="note" placeholder="যদি কোন নির্দেশনা থাকে" />
                    </div>

                    <h3 class="co-sub">Payment Option</h3>
                    <div class="co-payments" id="paymentMethods">
                        <button type="button" class="pay-card active" wire:click="$set('payment_method', 'cod')"
                            data-method="cod">
                            <span class="pay-flag"><i class="fa-solid fa-circle-check"></i></span>
                            <span class="pay-icon"><img src="img/cod.png" alt="COD"
                                    onerror="this.style.display='none'" /></span>
                            <span class="pay-text">Cash on Delivery</span>
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4" wire:loading.attr="disabled">
                        <span wire:loading.remove>অর্ডার নিশ্চিত করুন</span>
                        <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> অপেক্ষা করুন...</span>
                    </button>
                </form>
            </section>

            <!-- Right: Summary -->
            <aside class="co-right">
                <div class="co-summary">
                    <div class="co-summary-title">অর্ডার আইটেমস ({{ count($cartItems) }} Items)</div>
                    <div class="co-items">
                        <livewire:frontend.components.cart-items :cartItems="$cartItems" />
                        
                    </div>

                    <div class="co-lines">
                        <div class="line"><span>সাব টোটাল:</span><strong>৳ {{ number_format($subtotal) }}</strong></div>
                        <div class="line"><span>ডিসকাউন্ট:</span><strong>-৳ {{ number_format($discount) }}</strong></div>
                        <div class="line"><span>ডেলিভারি চার্জ:</span><strong>৳ {{ number_format($delivery_charge) }}</strong></div>
                        <div class="line total"><span>সর্বমোট:</span><strong>৳ {{ number_format($total) }}</strong></div>
                    </div>
                    <div class="co-coupon">
                        <label>আপনার কাছে কি কোন কুপন কোড আছে?</label>
                        <div class="coupon-row">
                            <input type="text" wire:model.defer="coupon_code" class="co-coupon-input" placeholder="Coupon code">
                            <button wire:click="applyCoupon" class="btn btn-secondary co-apply">Apply</button>
                        </div>
                    </div>

                    
                </div>
            </aside>
        </div>
    </div>
</div>
