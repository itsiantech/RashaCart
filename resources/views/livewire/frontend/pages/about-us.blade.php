@extends('livewire.frontend.layouts.app')

@section('page-title', 'About Us')

@section('content')
<div class="container py-5">

    {{-- Hero Section --}}
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="{{ asset('assets/frontend/images/about-store.jpg') }}" 
                 alt="About Our Store" 
                 class="img-fluid rounded shadow-sm w-100">
        </div>
        <div class="col-lg-6">
            <h1 class="fw-bold mb-3">About <span class="text-primary">{{ config('app.name') }}</span></h1>
            <p class="text-muted">
                Welcome to <strong>{{ config('app.name') }}</strong> — your trusted destination for premium products at the best prices.  
                We’re passionate about bringing you a seamless shopping experience, quality items, and fast delivery — all from the comfort of your home.
            </p>
            <p class="text-muted">
                Since our journey began, our mission has been simple: to make online shopping reliable, affordable, and enjoyable for everyone.
            </p>
        </div>
    </div>

    {{-- Mission, Vision, Values --}}
    <div class="row text-center mb-5">
        <div class="col-md-4 mb-4">
            <i class="fas fa-bullseye fa-3x text-primary mb-3"></i>
            <h4 class="fw-semibold">Our Mission</h4>
            <p class="text-muted small">
                To provide high-quality products with exceptional service and fast, secure delivery.
            </p>
        </div>
        <div class="col-md-4 mb-4">
            <i class="fas fa-eye fa-3x text-success mb-3"></i>
            <h4 class="fw-semibold">Our Vision</h4>
            <p class="text-muted small">
                To be your go-to online store for everything you love — trusted by thousands worldwide.
            </p>
        </div>
        <div class="col-md-4 mb-4">
            <i class="fas fa-handshake fa-3x text-warning mb-3"></i>
            <h4 class="fw-semibold">Our Promise</h4>
            <p class="text-muted small">
                Customer satisfaction comes first — we’re here to make every purchase a happy experience.
            </p>
        </div>
    </div>

    {{-- Why Choose Us Section --}}
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
            <img src="{{ asset('assets/frontend/images/why-choose-us.jpg') }}" 
                 alt="Why Choose Us" 
                 class="img-fluid rounded shadow-sm w-100">
        </div>
        <div class="col-lg-6 order-lg-1">
            <h2 class="fw-bold mb-3">Why Choose {{ config('app.name') }}?</h2>
            <ul class="list-unstyled text-muted">
                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> 100% genuine, top-quality products</li>
                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Secure payments & fast checkout</li>
                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Express delivery and easy returns</li>
                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Friendly customer support 24/7</li>
            </ul>
        </div>
    </div>

    {{-- CTA Section --}}
    <div class="bg-light p-5 text-center rounded shadow-sm">
        <h3 class="fw-semibold mb-3">Join Thousands of Happy Shoppers!</h3>
        <p class="text-muted mb-4">Experience a smoother, smarter way to shop online.</p>
        <a href="{{ route('home') }}" class="btn btn-danger px-4">
            <i class="fas fa-shopping-bag me-2"></i>Start Shopping
        </a>
    </div>

</div>
@endsection
