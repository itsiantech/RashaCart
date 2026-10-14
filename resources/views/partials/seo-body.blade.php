{{-- GTM noscript (right after <body>) --}}
@php $seoGtmBody = \App\Services\SeoService::gtmId(); @endphp
@if ($seoGtmBody)
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $seoGtmBody }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
