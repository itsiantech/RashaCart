@extends('livewire.frontend.layouts.app')

@section('page-title', 'Terms and Conditions')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <h1 class="fw-bold mb-4 text-center">Terms and Conditions</h1>
            <p class="text-muted mb-4 text-center">
                Last updated: {{ now()->format('F d, Y') }}
            </p>

            <p class="text-muted">
                Welcome to <strong>{{ config('app.name') }}</strong>!  
                By accessing or using our website (<a href="{{ url('/') }}">{{ url('/') }}</a>), 
                you agree to be bound by the following Terms and Conditions.  
                Please read them carefully before making any purchase or using our services.
            </p>

            <hr class="my-5">

            <h4 class="fw-semibold mb-3">1. General Information</h4>
            <p class="text-muted">
                These Terms govern your use of our website and any related services provided by 
                <strong>{{ config('app.name') }}</strong>.  
                We reserve the right to update or modify these Terms at any time, and such changes 
                will take effect immediately upon posting.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">2. Use of the Website</h4>
            <p class="text-muted">
                You agree to use this website for lawful purposes only. You must not:
            </p>
            <ul class="text-muted">
                <li>Engage in any fraudulent or deceptive activity</li>
                <li>Distribute malware, spam, or harmful code</li>
                <li>Copy, modify, or exploit our website content without permission</li>
            </ul>

            <h4 class="fw-semibold mt-5 mb-3">3. Products and Services</h4>
            <p class="text-muted">
                We make every effort to display accurate product information, pricing, and availability.  
                However, errors may occur. We reserve the right to correct such errors and cancel any 
                affected orders prior to shipment.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">4. Orders and Payments</h4>
            <p class="text-muted">
                When you place an order, you agree to provide accurate and complete billing information.  
                All payments must be made through our secure payment gateways.  
                We do not store your payment card details on our servers.
            </p>
            <p class="text-muted">
                <strong>{{ config('app.name') }}</strong> reserves the right to refuse or cancel any order 
                if fraud or unauthorized activity is suspected.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">5. Shipping and Delivery</h4>
            <p class="text-muted">
                Delivery times may vary based on your location and product availability.  
                We are not responsible for delays caused by courier services or events beyond our control.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">6. Returns and Refunds</h4>
            <p class="text-muted">
                We aim for 100% customer satisfaction. If you’re not fully happy with your purchase, 
                please review our <a href="{{ route('frontend.privacy.policy') }}">Return & Refund Policy</a> 
                or contact our support team within 7 days of receiving your order.  
                Products must be unused and in original packaging to qualify for a refund or exchange.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">7. Intellectual Property</h4>
            <p class="text-muted">
                All content on this website — including logos, images, graphics, and text — are the property of 
                <strong>{{ config('app.name') }}</strong> and protected by applicable copyright laws.  
                You may not reproduce or distribute any materials without prior written consent.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">8. Limitation of Liability</h4>
            <p class="text-muted">
                To the fullest extent permitted by law, <strong>{{ config('app.name') }}</strong> shall not be held liable 
                for any direct, indirect, or incidental damages resulting from the use of our website or products.  
                You agree to use our services at your own risk.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">9. Privacy Policy</h4>
            <p class="text-muted">
                Your privacy is important to us. Please review our 
                <a href="{{ route('frontend.privacy.policy') }}">Privacy Policy</a> to understand how we collect, use, and protect your data.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">10. Governing Law</h4>
            <p class="text-muted">
                These Terms and Conditions are governed by the laws of your country or region.  
                Any disputes arising shall be resolved in accordance with applicable legal procedures.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">11. Contact Information</h4>
            <p class="text-muted">
                If you have any questions about these Terms and Conditions, please contact us:
            </p>
            <ul class="text-muted">
                <li><i class="fas fa-envelope me-2 text-primary"></i> Email: {{ setting('contact_email') ?? 'support@yourstore.com' }}</li>
                <li><i class="fas fa-phone me-2 text-primary"></i> Phone: {{ setting('contact_phone') ?? '+8801XXXXXXXXX' }}</li>
                <li><i class="fas fa-map-marker-alt me-2 text-primary"></i> Address: {{ setting('contact_address') ?? 'Your Store Address' }}</li>
            </ul>

            <div class="text-center mt-5">
                <a href="{{ route('home') }}" class="btn btn-danger px-4">
                    <i class="fas fa-shopping-bag me-2"></i>Return to Shop
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
