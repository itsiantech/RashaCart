<div style="font-family: 'SolaimanLipi', 'Kalpurush', sans-serif;">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/fonts/boxicons.css') }}" />
    {{-- Hero Header Section --}}
    @if ($landingPage->hero_image)
    <section class="hero-section" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 40px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white mb-4 mb-lg-0">
                    @if ($landingPage->main_title)
                    <h1 class="display-5 fw-bold mb-2">{{ $landingPage->main_title }}</h1>
                    @endif

                    <h2 class="h3 mb-4">{{ $landingPage->title }}</h2>

                    {{-- Countdown Timer --}}
                    @if ($landingPage->countdown_end_date && $landingPage->countdown_end_date > now())
                    <div class="countdown-timer mb-4">
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="bg-white text-dark rounded p-2 text-center" style="min-width: 70px;">
                                <div id="days" class="fs-3 fw-bold">00</div>
                                <small>Days</small>
                            </div>
                            <div class="bg-white text-dark rounded p-2 text-center" style="min-width: 70px;">
                                <div id="hours" class="fs-3 fw-bold">00</div>
                                <small>Hours</small>
                            </div>
                            <div class="bg-white text-dark rounded p-2 text-center" style="min-width: 70px;">
                                <div id="minutes" class="fs-3 fw-bold">00</div>
                                <small>Minutes</small>
                            </div>
                            <div class="bg-white text-dark rounded p-2 text-center" style="min-width: 70px;">
                                <div id="seconds" class="fs-3 fw-bold">00</div>
                                <small>Seconds</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Prices --}}
                    @if ($landingPage->regular_price && $landingPage->discount_price)
                    <div class="price-section mb-4">
                        <span class="text-decoration-line-through fs-4">রেগুলার মূল্য ৳{{ number_format($landingPage->regular_price, 0) }}</span>
                        <br>
                        <span class="fs-3 fw-bold">বর্তমান ডিসকাউন্ট মূল্য ৳{{ number_format($landingPage->discount_price, 0) }} টাকা</span>
                    </div>
                    @endif

                    <a href="#checkout" class="btn btn-danger btn-lg px-4 py-3 fw-bold">
                        <i class="bx bx-cart me-2"></i> অর্ডার করতে চাই
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="hero-image-wrapper">
                        <img src="{{ asset('storage/' . $landingPage->hero_image) }}"
                            alt="{{ $landingPage->title }}"
                            class="img-fluid rounded-3 shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Benefits Section --}}
    @if ($landingPage->benefits_title && !empty($landingPage->benefits_list))
    <section class="benefits-section py-4 bg-light">
        <div class="container">
            <div class="bg-success text-white text-center py-3 rounded mb-3">
                <h3 class="fw-bold mb-0">{{ $landingPage->benefits_title }}</h3>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="benefits-list bg-white p-4 rounded shadow-sm">
                        @foreach ($landingPage->benefits_list as $benefit)
                        <div class="d-flex align-items-start mb-2">
                            <i class="{{ $landingPage->listIconClass('benefits_icon') }} text-success fs-4 me-2 mt-1"></i>
                            <p class="mb-0">{{ $benefit }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="benefits-sidebar bg-white p-4 rounded shadow-sm text-center">
                        @if ($landingPage->discount_price)
                        <div class="alert alert-success fw-bold mb-3">
                            ১ কেজি ৳{{ number_format($landingPage->discount_price, 0) }} টাকা মাত্র
                        </div>
                        @endif
                        <a href="#checkout" class="btn btn-danger btn-lg w-100 fw-bold">
                            <i class="bx bx-cart me-2"></i> অর্ডার করতে চাই
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Trust & Quality Section --}}
    @if ($landingPage->trust_title && !empty($landingPage->trust_list))
    <section class="trust-section py-4">
        <div class="container">
            <div class="bg-success text-white text-center py-3 rounded mb-3">
                <h3 class="fw-bold mb-0">{{ $landingPage->trust_title }}</h3>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="trust-list bg-white p-4 rounded shadow-sm">
                        @foreach ($landingPage->trust_list as $trust)
                        <div class="d-flex align-items-start mb-2">
                            <i class="{{ $landingPage->listIconClass('trust_icon') }} text-success fs-4 me-2 mt-1"></i>
                            <p class="mb-0">{{ $trust }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if ($landingPage->secondary_image)
                <div class="col-lg-6">
                    <div class="secondary-image-wrapper">
                        <img src="{{ asset('storage/' . $landingPage->secondary_image) }}"
                            alt="Quality Assurance"
                            class="img-fluid rounded shadow-sm">
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- Contact Banner --}}
    @if ($landingPage->contact_number)
    <section class="contact-banner py-3 bg-success">
        <div class="container">
            <div class="text-center text-white">
                <h5 class="fw-bold mb-0">
                    যে কোন প্রয়োজনে যোগাযোগ করুন {{ $landingPage->contact_number }}
                </h5>
            </div>
        </div>
    </section>
    @endif

    {{-- Checkout Section --}}
    <section id="checkout" class="checkout-section py-4 bg-light">
        <div class="container">
            <h3 class="text-center mb-4 fw-bold">অর্ডার করতে নিচের ফর্মটি পূরণ করুন</h3>

            <div class="row">
                {{-- Left Column - Billing Details --}}
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 fw-bold">বিলিং ডিটেইলস</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="placeOrder">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">নাম <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="আপনার নাম লিখুন">
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">দেশ/অঞ্চল <span class="text-danger">*</span></label>
                                    <select wire:model="country"
                                        class="form-select @error('country') is-invalid @enderror">
                                        <option value="Bangladesh">Bangladesh</option>
                                        <option value="India">India</option>
                                        <option value="Pakistan">Pakistan</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('country')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">স্ট্রিট ঠিকানা <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="shipping_address"
                                        class="form-control @error('shipping_address') is-invalid @enderror"
                                        placeholder="আপনার স্ট্রিট ঠিকানা লিখুন">
                                    @error('shipping_address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">শহর <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control"
                                        placeholder="আপনার শহর লিখুন">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">ফোন নম্বর <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="01XXXXXXXXX">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Product Quantity Selector --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">আপনার পণ্য</label>
                                    @foreach ($products as $product)
                                    <div class="card mb-2">
                                        <div class="card-body d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                @if ($product->thumbnail)
                                                <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                                    alt="{{ $product->name }}"
                                                    class="rounded me-3"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $product->name }}</h6>
                                                    <small class="text-muted">
                                                        ৳{{ number_format($landingPage->discount_price ?? $product->price, 2) }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <button type="button"
                                                    wire:click="decrementQuantity({{ $product->id }})"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="bx bx-minus"></i>
                                                </button>
                                                <input type="text"
                                                    wire:model="quantities.{{ $product->id }}"
                                                    class="form-control form-control-sm mx-2 text-center"
                                                    style="width: 50px;" readonly>
                                                <button type="button"
                                                    wire:click="incrementQuantity({{ $product->id }})"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="bx bx-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Order Summary --}}
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0 fw-bold">আপনার অর্ডার</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($products as $product)
                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="fw-bold">{{ $product->name }}</h6>
                                <div class="d-flex justify-content-between">
                                    <span>সাবটোটাল</span>
                                    <span>৳{{ number_format(($landingPage->discount_price ?? $product->price) * ($quantities[$product->id] ?? 1), 2) }}</span>
                                </div>
                            </div>
                            @endforeach

                            <div class="d-flex justify-content-between fw-bold mb-3">
                                <span>মোট</span>
                                <span>৳{{ number_format($this->subtotal, 2) }}</span>
                            </div>

                            {{-- Delivery Zone --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">ডেলিভারি চার্জ</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check">
                                        <input type="radio" wire:model="delivery_zone" value="inside"
                                            id="inside" class="form-check-input" wire:change="$refresh">
                                        <label for="inside" class="form-check-label">
                                            ঢাকার ভেতর (৳{{ setting('dc_inside', 60) }})
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" wire:model="delivery_zone" value="outside"
                                            id="outside" class="form-check-input" wire:change="$refresh">
                                        <label for="outside" class="form-check-label">
                                            ঢাকার বাইরে (৳{{ setting('dc_outside', 120) }})
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                                <span>মোট</span>
                                <span>৳{{ number_format($this->total, 2) }}</span>
                            </div>

                            {{-- Payment Method --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">পেমেন্ট মেথড</label>
                                <div class="form-check mb-2">
                                    <input type="radio" wire:model="payment_method" value="cod"
                                        id="cod" class="form-check-input" checked>
                                    <label for="cod" class="form-check-label">
                                        ক্যাশ অন ডেলিভারি
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="radio" wire:model="payment_method" value="bkash"
                                        id="bkash" class="form-check-input">
                                    <label for="bkash" class="form-check-label">
                                        bKash
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="radio" wire:model="payment_method" value="nagad"
                                        id="nagad" class="form-check-input">
                                    <label for="nagad" class="form-check-label">
                                        Nagad
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" wire:model="payment_method" value="rocket"
                                        id="rocket" class="form-check-input">
                                    <label for="rocket" class="form-check-label">
                                        Rocket
                                    </label>
                                </div>
                            </div>

                            <small class="text-muted d-block mb-3">
                                আপনার ব্যক্তিগত তথ্য প্রক্রিয়া করার জন্য আমাদের <a href="#">গোপনীয়তা নীতি</a> পড়ুন।
                            </small>

                            <button type="submit" wire:click="placeOrder"
                                class="btn btn-success btn-lg w-100 fw-bold py-3">
                                Place Order ৳{{ number_format($this->total, 2) }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-3 bg-dark text-white text-center">
        <small>
            Copyright © {{ date('Y') }} আপনার ব্যবসার নাম | এই ওয়েবসাইটটি তৈরি করা হয়েছে ❤️ দিয়ে
        </small>
    </footer>

    {{-- Countdown Timer Script --}}
    @if ($landingPage->countdown_end_date && $landingPage->countdown_end_date > now())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countdownDate = new Date('{{ $landingPage->countdown_end_date->format('Y-m-d\TH:i:s') }}').getTime();

            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = countdownDate - now;

                if (distance < 0) {
                    clearInterval(timer);
                    document.getElementById('days').textContent = '00';
                    document.getElementById('hours').textContent = '00';
                    document.getElementById('minutes').textContent = '00';
                    document.getElementById('seconds').textContent = '00';
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('days').textContent = String(days).padStart(2, '0');
                document.getElementById('hours').textContent = String(hours).padStart(2, '0');
                document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
            }, 1000);
        });
    </script>
    @endif
</div>
