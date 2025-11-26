@extends('livewire.frontend.layouts.app')

@section('page-title', 'Refund & Return Policy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <h1 class="fw-bold mb-4 text-center">Refund & Return Policy</h1>
            <p class="text-muted mb-4 text-center">
                Last updated: {{ now()->format('F d, Y') }}
            </p>

            <p class="text-muted">
                Thank you for shopping at <strong>{{ config('app.name') }}</strong>!  
                We value your satisfaction and strive to make every purchase a positive experience.  
                If you are not entirely satisfied with your purchase, we’re here to help.
            </p>

            <hr class="my-5">

            <h4 class="fw-semibold mb-3">1. Returns</h4>
            <p class="text-muted">
                You have <strong>7 days</strong> from the date you received your item to request a return.  
                To be eligible for a return, your item must:
            </p>
            <ul class="text-muted">
                <li>Be unused and in the same condition that you received it</li>
                <li>Be in the original packaging (with tags, manuals, and accessories)</li>
                <li>Include a valid proof of purchase (order ID, invoice, or receipt)</li>
            </ul>
            <p class="text-muted">
                Certain products such as perishable goods, personal care items, or digital downloads 
                cannot be returned due to hygiene and licensing restrictions.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">2. Return Process</h4>
            <p class="text-muted">
                To initiate a return, please contact our support team at  
                <a href="mailto:{{ setting('contact_email') ?? 'support@yourstore.com' }}">{{ setting('contact_email') ?? 'support@yourstore.com' }}</a>.  
                Once your request is approved, we’ll provide return instructions and a shipping address.
            </p>
            <p class="text-muted">
                You will be responsible for paying your own shipping costs for returning your item.  
                Shipping costs are non-refundable unless the product was defective or incorrect.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">3. Refunds</h4>
            <p class="text-muted">
                Once we receive and inspect your returned item, we’ll notify you about the status of your refund.  
                If approved, your refund will be processed to your original payment method within  
                <strong>5–10 business days</strong>.
            </p>
            <p class="text-muted">
                Please note that your bank or credit card company may take additional time to post the refund.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">4. Exchanges</h4>
            <p class="text-muted">
                We replace items only if they are defective, damaged, or shipped incorrectly.  
                If you need to exchange an item, contact us at  
                <a href="mailto:{{ setting('contact_email') ?? 'support@yourstore.com' }}">{{ setting('contact_email') ?? 'support@yourstore.com' }}</a>  
                with your order details and a photo of the item.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">5. Non-Refundable Items</h4>
            <ul class="text-muted">
                <li>Gift cards or promotional items</li>
                <li>Downloadable software or digital content</li>
                <li>Perishable goods (food, flowers, etc.)</li>
                <li>Health and personal care products</li>
            </ul>

            <h4 class="fw-semibold mt-5 mb-3">6. Order Cancellations</h4>
            <p class="text-muted">
                Orders can only be canceled <strong>before shipment</strong>.  
                Once shipped, you must follow the return process to obtain a refund.  
                Please contact us as soon as possible if you need to cancel.
            </p>

            <h4 class="fw-semibold mt-5 mb-3">7. Contact Us</h4>
            <p class="text-muted">
                If you have any questions about our Refund & Return Policy, please contact us:
            </p>
            <ul class="text-muted">
                <li><i class="fas fa-envelope me-2 text-primary"></i> Email: {{ setting('contact_email') ?? 'support@yourstore.com' }}</li>
                <li><i class="fas fa-phone me-2 text-primary"></i> Phone: {{ setting('contact_phone') ?? '+8801XXXXXXXXX' }}</li>
                <li><i class="fas fa-map-marker-alt me-2 text-primary"></i> Address: {{ setting('contact_address') ?? 'Your Store Address' }}</li>
            </ul>

            <div class="text-center mt-5">
                <a href="{{ route('home') }}" class="btn btn-danger px-4">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
