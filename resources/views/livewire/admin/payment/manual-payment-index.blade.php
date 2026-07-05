<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-semibold mb-1">Manual Payment Settings</h4>
            <p class="text-muted mb-0 small">Configure mobile banking &amp; bank transfer details for manual payments.</p>
        </div>
        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary px-4">
            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2" role="status"></span>
            <i wire:loading.remove wire:target="save" class="bi bi-floppy me-1"></i>
            Save Settings
        </button>
    </div>

    {{-- Success Alert --}}
    @if($successMessage)
    <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4" role="alert"
        x-data x-init="setTimeout(() => $el.remove(), 4000)">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ $successMessage }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- Left: Tab Navigation --}}
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2">
                    <div class="nav flex-column nav-pills gap-1" role="tablist">

                        @php
                        $tabs = [
                        'bkash' => ['label' => 'bKash', 'icon' => 'bi-phone', 'color' => '#e2136e'],
                        'nagad' => ['label' => 'Nagad', 'icon' => 'bi-phone', 'color' => '#f05829'],
                        'rocket' => ['label' => 'Rocket', 'icon' => 'bi-phone', 'color' => '#8b1fa9'],
                        'bank' => ['label' => 'Bank Account', 'icon' => 'bi-bank', 'color' => '#0d6efd'],
                        ];
                        $enabledMap = [
                        'bkash' => $bkash_enabled,
                        'nagad' => $nagad_enabled,
                        'rocket' => $rocket_enabled,
                        'bank' => $bank_enabled,
                        ];
                        @endphp

                        @foreach($tabs as $key => $tab)
                        <button
                            wire:click="setTab('{{ $key }}')"
                            class="nav-link text-start d-flex align-items-center gap-2 px-3 py-2 rounded {{ $activeTab === $key ? 'active' : '' }}"
                            style="{{ $activeTab === $key ? 'background-color: ' . $tab['color'] . '1a; color: ' . $tab['color'] . '; font-weight:600;' : '' }}"
                            role="tab">
                            <i class="bi {{ $tab['icon'] }} fs-5"></i>
                            <span>{{ $tab['label'] }}</span>
                            @if($enabledMap[$key])
                            <span class="badge ms-auto" style="background:{{ $tab['color'] }}; font-size:0.65rem;">ON</span>
                            @endif
                        </button>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Tab Content --}}
        <div class="col-12 col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    {{-- bKash Tab --}}
                    @if($activeTab === 'bkash')
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#e2136e1a;">
                            <i class="bi bi-phone fs-4" style="color:#e2136e;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">bKash Configuration</h5>
                            <small class="text-muted">Set your bKash number for manual payments</small>
                        </div>
                        <div class="ms-auto form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="bkash_enabled" wire:model.live="bkash_enabled"
                                style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-medium" for="bkash_enabled">
                                {{ $bkash_enabled ? 'Enabled' : 'Disabled' }}
                            </label>
                        </div>
                    </div>

                    @if($bkash_enabled)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">bKash Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" wire:model.blur="bkash_number"
                                    class="form-control @error('bkash_number') is-invalid @enderror"
                                    placeholder="01XXXXXXXXX">
                                @error('bkash_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Account Type <span class="text-danger">*</span></label>
                            <select wire:model="bkash_account_type"
                                class="form-select @error('bkash_account_type') is-invalid @enderror">
                                <option value="personal">Personal</option>
                                <option value="merchant">Merchant</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex gap-2 mt-4 mb-0" role="alert">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>How it works:</strong> Customers will be shown this bKash number and asked to
                            send payment manually. They must submit their <em>Transaction ID</em> for verification.
                        </div>
                    </div>
                    @else
                    <x-payment-disabled-notice method="bKash" color="#e2136e" />
                    @endif
                    @endif

                    {{-- Nagad Tab --}}
                    @if($activeTab === 'nagad')
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#f058291a;">
                            <i class="bi bi-phone fs-4" style="color:#f05829;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Nagad Configuration</h5>
                            <small class="text-muted">Set your Nagad number for manual payments</small>
                        </div>
                        <div class="ms-auto form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="nagad_enabled" wire:model.live="nagad_enabled"
                                style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-medium" for="nagad_enabled">
                                {{ $nagad_enabled ? 'Enabled' : 'Disabled' }}
                            </label>
                        </div>
                    </div>

                    @if($nagad_enabled)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nagad Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" wire:model.blur="nagad_number"
                                    class="form-control @error('nagad_number') is-invalid @enderror"
                                    placeholder="01XXXXXXXXX">
                                @error('nagad_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Account Type <span class="text-danger">*</span></label>
                            <select wire:model="nagad_account_type"
                                class="form-select @error('nagad_account_type') is-invalid @enderror">
                                <option value="personal">Personal</option>
                                <option value="merchant">Merchant</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex gap-2 mt-4 mb-0" role="alert">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>How it works:</strong> Customers will be shown this Nagad number and asked to
                            send payment manually. They must submit their <em>Transaction ID</em> for verification.
                        </div>
                    </div>
                    @else
                    <x-payment-disabled-notice method="Nagad" color="#f05829" />
                    @endif
                    @endif

                    {{-- Rocket Tab --}}
                    @if($activeTab === 'rocket')
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#8b1fa91a;">
                            <i class="bi bi-phone fs-4" style="color:#8b1fa9;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Rocket Configuration</h5>
                            <small class="text-muted">Set your Rocket number for manual payments</small>
                        </div>
                        <div class="ms-auto form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="rocket_enabled" wire:model.live="rocket_enabled"
                                style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-medium" for="rocket_enabled">
                                {{ $rocket_enabled ? 'Enabled' : 'Disabled' }}
                            </label>
                        </div>
                    </div>

                    @if($rocket_enabled)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Rocket Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" wire:model.blur="rocket_number"
                                    class="form-control @error('rocket_number') is-invalid @enderror"
                                    placeholder="01XXXXXXXXX">
                                @error('rocket_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex gap-2 mt-4 mb-0" role="alert">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>How it works:</strong> Customers will be shown this Rocket number and asked to
                            send payment manually. They must submit their <em>Transaction ID</em> for verification.
                        </div>
                    </div>
                    @else
                    <x-payment-disabled-notice method="Rocket" color="#8b1fa9" />
                    @endif
                    @endif

                    {{-- Bank Account Tab --}}
                    @if($activeTab === 'bank')
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#0d6efd1a;">
                            <i class="bi bi-bank fs-4" style="color:#0d6efd;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Bank Account Configuration</h5>
                            <small class="text-muted">Set your bank details for manual transfers</small>
                        </div>
                        <div class="ms-auto form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="bank_enabled" wire:model.live="bank_enabled"
                                style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-medium" for="bank_enabled">
                                {{ $bank_enabled ? 'Enabled' : 'Disabled' }}
                            </label>
                        </div>
                    </div>

                    @if($bank_enabled)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Bank Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-bank"></i></span>
                                <input type="text" wire:model.blur="bank_name"
                                    class="form-control @error('bank_name') is-invalid @enderror"
                                    placeholder="e.g. Dutch-Bangla Bank Ltd.">
                                @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Account Holder Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" wire:model.blur="bank_account_name"
                                    class="form-control @error('bank_account_name') is-invalid @enderror"
                                    placeholder="Full name on account">
                                @error('bank_account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Account Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                <input type="text" wire:model.blur="bank_account_number"
                                    class="form-control @error('bank_account_number') is-invalid @enderror"
                                    placeholder="Bank account number">
                                @error('bank_account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Routing Number <span class="text-muted small">(optional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                <input type="text" wire:model.blur="bank_routing_number"
                                    class="form-control @error('bank_routing_number') is-invalid @enderror"
                                    placeholder="9-digit routing number">
                                @error('bank_routing_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Branch Name <span class="text-muted small">(optional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" wire:model.blur="bank_branch"
                                    class="form-control"
                                    placeholder="e.g. Gulshan Branch, Dhaka">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning d-flex gap-2 mt-4 mb-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            Customers will be asked to transfer funds to this account and submit a
                            <em>payment slip / screenshot</em> for manual verification by the admin.
                        </div>
                    </div>
                    @else
                    <x-payment-disabled-notice method="Bank Transfer" color="#0d6efd" />
                    @endif
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Save --}}
    <div class="d-flex justify-content-end mt-4">
        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary px-5">
            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2" role="status"></span>
            <i wire:loading.remove wire:target="save" class="bi bi-floppy me-1"></i>
            Save All Settings
        </button>
    </div>

</div>