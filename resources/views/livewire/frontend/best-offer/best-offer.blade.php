<div>
    <div>
        @if($offers->count())
        <section class="best-offers-section py-5">
            <div class="container">

                {{-- Section Header --}}
                <div class="best-offers-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="offer-icon-wrap">
                            <i class="fa fa-bolt"></i>
                        </div>
                        <div>
                            <h2 class="section-title mb-0">Best Offers</h2>
                            <p class="section-sub mb-0">Limited time deals — grab them before they're gone!</p>
                        </div>
                    </div>
                </div>

                {{-- Offer Cards --}}
                {{-- Offer Cards --}}
                <div class="offers-grid">
                    @foreach($offers as $offer)
                    @php
                    $product = $offer->product;
                    $stock = $product->inventories->sortByDesc('id')->first()?->current_stock ?? 0;
                    $expiresAt = $offer->expires_at->toIso8601String();
                    @endphp

                    <div class="offer-card">

                        {{-- Badge --}}
                        <div class="offer-badge">
                            {{ $offer->discount_value }}{{ $offer->discount_type === 'percent' ? '%' : ' '.setting('currency') }} OFF
                        </div>

                        {{-- Image --}}
                        <a href="{{ route('frontend.product.single.view', $product->slug) }}" class="offer-img-wrap">
                            <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : 'https://placehold.co/300x300' }}"
                                alt="{{ $product->name }}" class="offer-img">
                        </a>

                        {{-- Body --}}
                        <div class="offer-body">
                            <a href="{{ route('frontend.product.single.view', $product->slug) }}" class="offer-name">
                                {{ Str::limit($product->name, 50) }}
                            </a>

                            {{-- Pricing --}}
                            <div class="offer-pricing mt-2">
                                <span class="offer-price">{{ setting('currency') }} {{ number_format($offer->offer_price, 2) }}</span>
                                <span class="offer-original">{{ setting('currency') }} {{ number_format($product->sale_price ?? $product->price, 2) }}</span>
                            </div>

                            {{-- Stock --}}
                            <div class="offer-stock mt-2">
                                @if($stock > 10)
                                <span class="text-success small"><i class="fa fa-check-circle me-1"></i>In Stock</span>
                                @elseif($stock > 0)
                                <span class="text-warning small"><i class="fa fa-exclamation-triangle me-1"></i>Only {{ $stock }} left</span>
                                @else
                                <span class="text-danger small"><i class="fa fa-times-circle me-1"></i>Out of Stock</span>
                                @endif
                            </div>

                            {{-- Countdown --}}
                            <div class="offer-countdown mt-3" data-expires="{{ $expiresAt }}">
                                <div class="countdown-label"><i class="fa fa-clock me-1"></i> Ends in:</div>
                                <div class="countdown-timer d-flex gap-1 mt-1">
                                    <div class="countdown-unit">
                                        <span class="countdown-days">00</span>
                                        <small>Days</small>
                                    </div>
                                    <div class="countdown-sep">:</div>
                                    <div class="countdown-unit">
                                        <span class="countdown-hours">00</span>
                                        <small>Hrs</small>
                                    </div>
                                    <div class="countdown-sep">:</div>
                                    <div class="countdown-unit">
                                        <span class="countdown-mins">00</span>
                                        <small>Min</small>
                                    </div>
                                    <div class="countdown-sep">:</div>
                                    <div class="countdown-unit">
                                        <span class="countdown-secs">00</span>
                                        <small>Sec</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Button --}}
                            <a href="{{ route('frontend.product.single.view', $product->slug) }}"
                                class="btn offer-btn mt-3 w-100 {{ $stock <= 0 ? 'disabled' : '' }}">
                                <i class="fa fa-shopping-bag me-1"></i>
                                {{ $stock <= 0 ? 'Out of Stock' : 'Grab This Deal' }}
                            </a>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
    </div>
    </section>

    <style>
        .best-offers-section {
            background: white;
        }

        .offers-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
        }

        @media (max-width: 1200px) {
            .offers-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 900px) {
            .offers-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 480px) {
            .offers-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .offer-icon-wrap {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #ff6b35, #f7c59f);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
        }

        .section-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1a1a1a;
        }

        .section-sub {
            font-size: 0.85rem;
            color: #888;
        }

        /* Card */
        .offer-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .offer-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        /* Badge */
        .offer-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: linear-gradient(135deg, #ff4757, #ff6b81);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            z-index: 2;
            letter-spacing: 0.3px;
        }

        /* Image */
        .offer-img-wrap {
            display: block;
            width: 100%;
            aspect-ratio: 1/1;
            overflow: hidden;
            background: #f9f9f9;
        }

        .offer-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .offer-card:hover .offer-img {
            transform: scale(1.04);
        }

        /* Body */
        .offer-body {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .offer-name {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            text-decoration: none;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .offer-name:hover {
            color: #ff4757;
        }

        .offer-pricing {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .offer-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #ff4757;
        }

        .offer-original {
            font-size: 0.85rem;
            color: #aaa;
            text-decoration: line-through;
        }

        /* Countdown */
        .countdown-label {
            font-size: 11px;
            color: #888;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .countdown-timer {
            align-items: center;
        }

        .countdown-unit {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #fff3f3;
            border: 1px solid #ffe0e0;
            border-radius: 8px;
            padding: 4px 6px;
            min-width: 44px;
        }

        .countdown-unit span {
            font-size: 1rem;
            font-weight: 700;
            color: #ff4757;
            line-height: 1;
        }

        .countdown-unit small {
            font-size: 9px;
            color: #999;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .countdown-sep {
            font-size: 1rem;
            font-weight: 700;
            color: #ff4757;
            margin-bottom: 12px;
        }

        /* Button */
        .offer-btn {
            background: linear-gradient(135deg, #ff4757, #ff6b81);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            padding: 10px;
            transition: opacity 0.2s;
            text-decoration: none;
            text-align: center;
            margin-top: auto !important;
        }

        .offer-btn:hover {
            opacity: 0.9;
            color: #fff;
        }

        .offer-btn.disabled {
            background: #ccc;
            pointer-events: none;
        }

        @media (max-width: 576px) {
            .section-title {
                font-size: 1.3rem;
            }

            .countdown-unit {
                min-width: 36px;
                padding: 5px 7px;
            }

            .countdown-unit span {
                font-size: 0.95rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', initCountdowns);
        document.addEventListener('livewire:navigated', initCountdowns);

        function initCountdowns() {
            document.querySelectorAll('.offer-countdown').forEach(el => {
                const expires = new Date(el.dataset.expires).getTime();

                // Clear old interval if any
                if (el._countdownInterval) clearInterval(el._countdownInterval);

                el._countdownInterval = setInterval(() => {
                    const now = Date.now();
                    const diff = expires - now;

                    if (diff <= 0) {
                        clearInterval(el._countdownInterval);
                        el.innerHTML = '<div class="text-danger small fw-bold"><i class="fa fa-times-circle me-1"></i>Offer Expired</div>';
                        return;
                    }

                    const days = Math.floor(diff / 86400000);
                    const hours = Math.floor((diff % 86400000) / 3600000);
                    const mins = Math.floor((diff % 3600000) / 60000);
                    const secs = Math.floor((diff % 60000) / 1000);

                    const pad = n => String(n).padStart(2, '0');

                    el.querySelector('.countdown-days').textContent = pad(days);
                    el.querySelector('.countdown-hours').textContent = pad(hours);
                    el.querySelector('.countdown-mins').textContent = pad(mins);
                    el.querySelector('.countdown-secs').textContent = pad(secs);
                }, 1000);
            });
        }
    </script>
    @endif
</div>
</div>