@extends('livewire.frontend.layouts.app')

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        border-color: #0d6efd !important;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .section-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f8d7da;
        color: #dc3545;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">

            <div class="card border-0 shadow-lg">

                <div class="card-body p-4 p-md-5">

                    <h2 class="h4 fw-bold mb-1">Vendor Registration</h2>
                    <p class="text-muted small mb-4">
                        Please fill in the details below to create your shop account.
                    </p>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('vendor.register') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Personal Information -->
                        <div class="mb-5">
                            <div class="section-header">
                                <div class="section-number">1</div>
                                <h5 class="mb-0 fw-semibold">Personal Information</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase small fw-bold text-muted">
                                    Full Name
                                </label>
                                <input type="text"
                                    name="name"
                                    class="form-control form-control-lg bg-light border-0"
                                    value="{{ old('name') }}"
                                    placeholder="John Doe"
                                    required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-uppercase small fw-bold text-muted">
                                        Email Address
                                    </label>
                                    <input type="email"
                                        name="email"
                                        class="form-control bg-light border-0"
                                        value="{{ old('email') }}"
                                        placeholder="name@example.com"
                                        required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-uppercase small fw-bold text-muted">
                                        Phone Number
                                    </label>
                                    <input type="text"
                                        name="phone"
                                        class="form-control bg-light border-0"
                                        value="{{ old('phone') }}"
                                        placeholder="017XXXXXXXX"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div class="mb-5">
                            <div class="section-header">
                                <div class="section-number">2</div>
                                <h5 class="mb-0 fw-semibold">Business Details</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase small fw-bold text-muted">
                                    Shop Name
                                </label>
                                <input type="text"
                                    name="shop_name"
                                    class="form-control bg-light border-0"
                                    value="{{ old('shop_name') }}"
                                    placeholder="Elite Fashion Store"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase small fw-bold text-muted">
                                    Shop Address
                                </label>
                                <textarea name="shop_address"
                                    class="form-control bg-light border-0"
                                    rows="2"
                                    placeholder="House #, Road #, City..."
                                    required>{{ old('shop_address') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase small fw-bold text-muted">
                                    NID Number
                                </label>
                                <input type="text"
                                    name="NID_number"
                                    class="form-control bg-light border-0"
                                    value="{{ old('NID_number') }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase small fw-bold text-muted" required>
                                    Upload logo
                                </label>
                                <input type="file" name="profile_image" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase small fw-bold text-muted">
                                    Trade License Front
                                </label>
                                <input type="file" name="trade_license_front" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase small fw-bold text-muted">
                                    Trade License Back
                                </label>
                                <input type="file" name="trade_license_back" class="form-control">
                            </div>
                        </div>

                        <!-- Payment Settings -->
                        <div class="mb-5">
                            <div class="section-header">
                                <div class="section-number">3</div>
                                <h5 class="mb-0 fw-semibold">Payment Settings</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-uppercase small fw-bold text-muted">
                                    Payment Method
                                </label>

                                <select name="payment_method"
                                    class="form-select bg-light border-0"
                                    onchange="toggleBankFields(this.value)">
                                    <option value="">Select Method</option>
                                    <option value="cod">Cash on Delivery</option>
                                    <option value="bank">Bank Transfer</option>
                                </select>
                            </div>

                            <div id="bank_details_fields"
                                class="p-3 rounded bg-light border"
                                style="display:none">

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">
                                        Bank Name
                                    </label>
                                    <input type="text" name="bank_name" class="form-control border-0 shadow-sm">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-muted">
                                            Account Holder
                                        </label>
                                        <input type="text" name="account_holder_name" class="form-control border-0 shadow-sm">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-muted">
                                            Account Number
                                        </label>
                                        <input type="text" name="bank_account_number" class="form-control border-0 shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security -->
                        <div class="mb-4">
                            <div class="section-header">
                                <div class="section-number">4</div>
                                <h5 class="mb-0 fw-semibold">Security</h5>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-uppercase small fw-bold text-muted">
                                        Password
                                    </label>
                                    <input type="password"
                                        name="password"
                                        class="form-control bg-light border-0"
                                        required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-uppercase small fw-bold text-muted">
                                        Confirm Password
                                    </label>
                                    <input type="password"
                                        name="password_confirmation"
                                        class="form-control bg-light border-0"
                                        required>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="btn btn-danger btn-lg w-100 fw-bold py-2 shadow-sm">
                            Create My Vendor Account
                        </button>

                    </form>
                </div>
            </div>

            <div class="text-center mt-4 text-muted">
                <p>
                    Already have a vendor account?
                    <a href="{{ route('vendor.login.form') }}"
                        class="text-danger fw-bold text-decoration-none">
                        Sign In
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleBankFields(val) {
        document.getElementById('bank_details_fields').style.display =
            val === 'bank' ? 'block' : 'none';
    }
</script>
@endpush
