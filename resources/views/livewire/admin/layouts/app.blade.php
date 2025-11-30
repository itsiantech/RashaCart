<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets/admin')}}/"
  data-template="vertical-menu-template-free"
>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    
    <style>
        @import url('https://fonts.maateen.me/solaiman-lipi/font.css');
    </style>

    <title>@yield('page-title')</title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/admin')}}/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/admin')}}/vendor/fonts/boxicons.css" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin')}}/vendor/css/core.css" />
    <link rel="stylesheet" href="{{ asset('assets/admin')}}/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="{{ asset('assets/admin')}}/css/demo.css" />
    <link rel="stylesheet" href="{{ asset('assets/admin')}}/css/myCss.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin')}}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('assets/admin')}}/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    @livewireStyles
</head>

<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        @auth
            @include('livewire.admin.layouts.partial.sidebar')
            <div class="layout-page">
                @include('livewire.admin.layouts.partial.header')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-lg-12 mb-4 order-0">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/admin/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/admin/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/admin/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/admin/vendor/js/menu.js') }}"></script>
<script src="{{ asset('assets/admin/js/main.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>



<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('success', (event) => {
            const message = event.message || event[0]?.message || 'Success!';
            toastr.success(message);
        });

        Livewire.on('error', (event) => {
            const message = event.message || event[0]?.message || 'Error!';
            toastr.error(message);
        });
    });

    // Toastr options
    toastr.options = {
        "progressBar": true,
        "closeButton": true,
        "positionClass": "toast-top-right", 
        "timeOut": 2500,
        "preventDuplicates": true,
        "preventOpenDuplicates": true
    };

    // SweetAlert Delete Confirmation
    window.addEventListener('showDeleteConfirmation', () => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won’t be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('deleteConfirmed');
            }
        });
    });
</script>
@livewireScripts
@stack('scripts')
</body>
</html>
