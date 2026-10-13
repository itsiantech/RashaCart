<div>
    <h4 class="fw-bold mb-4">Server Side Tracking</h4>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'meta' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'meta')">
                <i class="bx bxl-facebook-circle me-2"></i>Meta (Facebook)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'tiktok' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'tiktok')">
                <i class="bx bxl-tiktok me-2"></i>TikTok
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'ga4' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'ga4')">
                <i class="bx bxl-google me-2"></i>GA4
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'google_ads' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'google_ads')">
                <i class="bx bxs-megaphone me-2"></i>Google Ads
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'guide' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'guide')">
                <i class="bx bx-book me-2"></i>Setup Guide (বাংলা)
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        {{-- Meta Tab --}}
        <div class="tab-pane {{ $activeTab === 'meta' ? 'active show' : '' }}">
            @include('livewire.admin.partials.platform-settings', ['platform' => 'meta', 'data' => $platforms['meta']])
        </div>

        {{-- TikTok Tab --}}
        <div class="tab-pane {{ $activeTab === 'tiktok' ? 'active show' : '' }}">
            @include('livewire.admin.partials.platform-settings', ['platform' => 'tiktok', 'data' => $platforms['tiktok']])
        </div>

        {{-- GA4 Tab --}}
        <div class="tab-pane {{ $activeTab === 'ga4' ? 'active show' : '' }}">
            @include('livewire.admin.partials.platform-settings', ['platform' => 'ga4', 'data' => $platforms['ga4']])
        </div>

        {{-- Google Ads Tab --}}
        <div class="tab-pane {{ $activeTab === 'google_ads' ? 'active show' : '' }}">
            @include('livewire.admin.partials.platform-settings', ['platform' => 'google_ads', 'data' => $platforms['google_ads']])
        </div>

        {{-- Setup Guide Tab --}}
        <div class="tab-pane {{ $activeTab === 'guide' ? 'active show' : '' }}">
            @include('livewire.admin.partials.tracking-setup-guide')
        </div>
    </div>

    {{-- Save Button --}}
    @if($activeTab !== 'guide')
    <div class="mt-4">
        <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">Save Settings</span>
            <span wire:loading wire:target="save">Saving...</span>
        </button>
    </div>
    @endif
</div>
