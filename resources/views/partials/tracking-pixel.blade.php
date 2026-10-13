@php
    use App\Models\TrackingPlatform;
    use App\Services\Tracking\AttributionService;

    $activePlatforms = TrackingPlatform::getActivePlatforms();
    $attributionService = app(AttributionService::class);
@endphp

@if($activePlatforms->isNotEmpty())
    {{-- Meta Pixel --}}
    @foreach($activePlatforms->where('platform', 'meta') as $metaPlatform)
        @if($metaPlatform->pixel_id)
            <script>
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');

                fbq('init', '{{ $metaPlatform->pixel_id }}');
                @if($metaPlatform->is_test_mode)
                fbq('set', 'testMode', true);
                @endif
                fbq('track', 'PageView');
            </script>
            <noscript>
                <img height="1" width="1" style="display:none"
                     src="https://www.facebook.com/tr?id={{ $metaPlatform->pixel_id }}&ev=PageView&noscript=1"/>
            </noscript>
        @endif
    @endforeach

    {{-- TikTok Pixel --}}
    @foreach($activePlatforms->where('platform', 'tiktok') as $tiktokPlatform)
        @if($tiktokPlatform->pixel_id)
            <script>
                !function (w, d, t) {
                  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=i;ttq._t=e;ttq._e=n;var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+n;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
                  ttq.load('{{ $tiktokPlatform->pixel_id }}', 'Tiktok Pixel');
                  ttq.page();
                }(window, document, 'ttq');
            </script>
        @endif
    @endforeach

    {{-- GA4 --}}
    @foreach($activePlatforms->where('platform', 'ga4') as $ga4Platform)
        @if($ga4Platform->pixel_id)
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Platform->pixel_id }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $ga4Platform->pixel_id }}');
            </script>
        @endif
    @endforeach

    {{-- Global tracking helper --}}
    <script>
        window.TrackingHelper = {
            generateEventId: function() {
                return crypto.randomUUID();
            },

            trackEvent: function(platform, eventName, params) {
                var eventId = this.generateEventId();

                @if($activePlatforms->where('platform', 'meta')->isNotEmpty())
                if (platform === 'meta' && typeof fbq !== 'undefined') {
                    fbq('track', eventName, params, {eventID: eventId});
                }
                @endif

                @if($activePlatforms->where('platform', 'tiktok')->isNotEmpty())
                if (platform === 'tiktok' && typeof ttq !== 'undefined') {
                    ttq.track(eventName, params, {event_id: eventId});
                }
                @endif

                @if($activePlatforms->where('platform', 'ga4')->isNotEmpty())
                if (platform === 'ga4' && typeof gtag !== 'undefined') {
                    gtag('event', eventName, params);
                }
                @endif

                return eventId;
            },

            trackViewContent: function(contentId, contentName, value, currency) {
                return this.trackEvent('meta', 'ViewContent', {
                    content_ids: [contentId],
                    content_name: contentName,
                    value: value,
                    currency: currency
                });
            },

            trackAddToCart: function(contentId, contentName, value, currency, quantity) {
                return this.trackEvent('meta', 'AddToCart', {
                    content_ids: [contentId],
                    content_name: contentName,
                    value: value,
                    currency: currency,
                    num_items: quantity
                });
            },

            trackInitiateCheckout: function(value, currency, numItems) {
                return this.trackEvent('meta', 'InitiateCheckout', {
                    value: value,
                    currency: currency,
                    num_items: numItems
                });
            },

            trackPurchase: function(orderId, value, currency, contents) {
                var eventId = 'purchase_' + orderId;

                @if($activePlatforms->where('platform', 'meta')->isNotEmpty())
                if (typeof fbq !== 'undefined') {
                    fbq('track', 'Purchase', {
                        content_ids: contents.map(function(c) { return c.id; }),
                        contents: contents,
                        value: value,
                        currency: currency,
                        num_items: contents.reduce(function(sum, c) { return sum + c.quantity; }, 0)
                    }, {eventID: eventId});
                }
                @endif

                @if($activePlatforms->where('platform', 'tiktok')->isNotEmpty())
                if (typeof ttq !== 'undefined') {
                    ttq.track('CompletePayment', {
                        value: value,
                        currency: currency,
                        contents: contents
                    }, {event_id: eventId});
                }
                @endif

                @if($activePlatforms->where('platform', 'ga4')->isNotEmpty())
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'purchase', {
                        transaction_id: orderId,
                        value: value,
                        currency: currency,
                        items: contents
                    });
                }
                @endif

                return eventId;
            }
        };
    </script>
@endif
