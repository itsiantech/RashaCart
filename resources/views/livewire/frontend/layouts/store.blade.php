<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meta['title'] ?? (setting('app_name') ?: config('app.name')) }}</title>
    <meta name="description" content="{{ $meta['description'] ?? '' }}">

    @php $favicon = setting('fev_icon') ?: setting('app_logo'); @endphp
    @if($favicon)
    <link rel="icon" href="{{ asset('storage/logo/' . rawurlencode($favicon)) }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/plp.css') }}" />

    @include('partials.style-vars')
    @include('partials.main-chrome-css')
    @include('partials.seo-tags')

    @stack('styles')
    @livewireStyles
</head>
<body class="hp-store">
    @include('partials.main-header')
    @include('partials.seo-body')

    {{ $slot ?? '' }}@yield('content')

    @include('partials.main-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('success', (e) => toastr.success(e.message || e[0]?.message || 'Success!'));
            Livewire.on('error', (e) => toastr.error(e.message || e[0]?.message || 'Error!'));
        });

        toastr.options = {
            "progressBar": true,
            "closeButton": true,
            "positionClass": "toast-top-right",
            "timeOut": 2500,
            "preventDuplicates": true,
            "preventOpenDuplicates": true
        };
    </script>
    @include('partials.customer-account-scripts')
    @include('partials.customer-auth-modal')

    @livewireScripts
    @stack('scripts')
</body>
</html>
