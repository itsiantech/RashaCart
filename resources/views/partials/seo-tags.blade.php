{{-- Google SEO tags: Search Console verification + Tag Manager + Schema.org. GA4 renders from the tracking module (single source, no duplicates). --}}
@php
    $seoGtm = \App\Services\SeoService::gtmId();
    $seoVerify = \App\Services\SeoService::verificationCode();
    $seoSchemas = array_filter([
        \App\Services\SeoService::orgSchema(),
        \App\Services\SeoService::websiteSchema(),
        ($__seoProduct = \App\Services\SeoService::currentProduct()) ? \App\Services\SeoService::productSchema($__seoProduct) : null,
    ]);
@endphp
@if ($seoVerify)
<meta name="google-site-verification" content="{{ $seoVerify }}" />
@endif
@if ($seoGtm)
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $seoGtm }}');</script>
<!-- End Google Tag Manager -->
@endif
@foreach ($seoSchemas as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach
