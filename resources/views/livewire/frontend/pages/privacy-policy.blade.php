@extends('livewire.frontend.layouts.app')

@section('page-title', 'Privacy Policy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="fw-bold mb-4 text-center">Privacy Policy</h1>
            <p class="text-muted mb-4 text-center">
                Last updated: {{ now()->format('F d, Y') }}
            </p>

            <p class="text-muted">
                At <strong>{{ config('app.name') }}</strong>, your privacy is important to us. 
                This Privacy Policy describes how we collect, use, and protect your personal information 
                when you visit or make a purchase from our website.
            </p>

            <hr class="my-5">

            <h4 class="fw-semibold mb-3">1. Information We Collect</h4>
            <p class="text-muted">
                We collect information that helps us process your orders and improve your shopping experience, such as:
            </p>
            <ul class="text-muted">
                <li>Personal information (name, email, phone number, billing/shipping address)</li>
                <li>Payment details (handled securely via third-party payment gateways)</li>
                <li>Order history and preferences</li>
                <li>Technical information like IP address, browser type, and device data</li>
            </ul>

            <h4 class="fw-semibold mt-5 mb-3">2. How We Use Your Information</h4>
            <ul class="text-muted">
                <li>To process and fulfill your orders</li>
                <li>To send updates about your order or account</li>
                <li>To improve our website, products, and customer service</li>
                <li>To prevent fraud and ensure website security</li>
                <li>To send promotional offers (only if you’ve opted in)</li>
            </ul>

            <h4 class="fw-semibold mt-5 mb-3">3. Sharing Your Information</h4>
            <p class="text-muted">
                We never sell your personal information.  
                We may share limited data with trusted partners for:
            </p>
            <ul class="text-muted">
                <li>Payment processing (e.g., SSL-secured payment gateways)</li>
                <li>Order delivery (courier or logistics partners)</li>
                <li>Email or SMS notifications (if applicable)</li>
            </ul>

            <h4 class="fw-semibold mt-5 mb-3">4. Cookies & Tracking</h4>
            <p class="text-muted">
                We use cookies to improve your browsing experience — for example, remembering items in your cart or 
                showing personalized recommendations.  
                You can disable cookies in your browser settings at any time.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">5. Data Security</h4>
            <p class="text-muted">
                We take data security seriously. Your information is protected using secure servers, encryption, and SSL technology.  
                However, no online transmission is 100% secure — so please use our site responsibly.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">6. Your Rights</h4>
            <ul class="text-muted">
                <li>Access or update your personal data</li>
                <li>Request deletion of your account or data</li>
                <li>Opt-out from promotional communications</li>
            </ul>

            <h4 class="fw-semibold mt-5 mb-3">7. Changes to This Policy</h4>
            <p class="text-muted">
                We may update this Privacy Policy from time to time.  
                All changes will be posted on this page with a new “Last Updated” date.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">8. Contact Us</h4>
            <p class="text-muted">
                If you have any questions about our Privacy Policy, please contact us:
            </p>
            <ul class="text-muted">
                <li><i class="fas fa-envelope me-2 text-primary"></i> Email: {{ setting('contact_email') ?? 'support@yourstore.com' }}</li>
                <li><i class="fas fa-phone me-2 text-primary"></i> Phone: {{ setting('contact_phone') ?? '+8801XXXXXXXXX' }}</li>
                <li><i class="fas fa-map-marker-alt me-2 text-primary"></i> Address: {{ setting('contact_address') ?? 'Your Store Address' }}</li>
            </ul>

            <div class="text-center mt-5">
                <a href="{{ route('home') }}" class="btn btn-success px-4">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
