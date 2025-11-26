<div class="card  border-primary">
    <div class="card-header bg-primary text-white mb-4">
        <h5 class="text-white">API Settings</h5>
        <p class="mb-0">Configure your API settings for third-party services here. Make sure to enter valid credentials to ensure proper functionality.</p>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="update">
            
            <!-- Steedfast Courier API Section -->
            <div class="card border-primary mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary">Steadfast Courier API Settings</h6>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted">Enter your Steadfast Courier API credentials below to enable shipping functionalities.</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="selectedCourier" class="form-label">Courier Provider</label>
                                <select class="form-select" id="selectedCourier" wire:model="selected_courier">
                                    <option value="">-- Select Courier --</option>
                                    <option value="steedfast">Steadfast Courier</option>
                                </select>
                                @error('selected_courier')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apiBaseUrl" class="form-label">API Base URL</label>
                                <input type="text" class="form-control" id="apiBaseUrl"
                                    wire:model="steedfast_api_base_url" placeholder="https://api.example.com">
                                @error('steedfast_api_base_url')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apiKey" class="form-label">API Key</label>
                                <input type="text" class="form-control" id="apiKey" wire:model="steedfast_api_key"
                                    placeholder="Enter your API key">
                                @error('steedfast_api_key')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apiSecret" class="form-label">API Secret</label>
                                <input type="text" class="form-control" id="apiSecret" wire:model="steedfast_api_secret"
                                    placeholder="Enter your API secret">
                                @error('steedfast_api_secret')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Google Login Section -->
            <div class="card border-primary mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary">Google Login Settings</h6>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted">Create your OAuth Client ID from <a href="https://console.cloud.google.com/" target="_blank" class="text-decoration-none">Google Cloud Console</a></p>
                    
                    <div class="alert alert-info">
                        <strong>Important:</strong> Add this redirect URI to your Google Cloud Console:
                        <div class="mt-2">
                            <code class="bg-light p-2 rounded d-block">{{ url('/auth/google/callback') }}</code>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="GOOGLE_CLIENT_ID" class="form-label">Client ID</label>
                                <input type="text" class="form-control" id="GOOGLE_CLIENT_ID" wire:model="GOOGLE_CLIENT_ID"
                                    placeholder="Enter your Client ID">
                                @error('GOOGLE_CLIENT_ID')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="GOOGLE_CLIENT_SECRET" class="form-label">Client Secret</label>
                                <input type="text" class="form-control" id="GOOGLE_CLIENT_SECRET" wire:model="GOOGLE_CLIENT_SECRET"
                                    placeholder="Enter your Client Secret">
                                @error('GOOGLE_CLIENT_SECRET')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Setup Instructions:</h6>
                            <ol class="mb-0">
                                <li>Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                                <li>Create a new project or select existing one</li>
                                <li>Enable Google+ API</li>
                                <li>Create OAuth 2.0 credentials</li>
                                <li>Add the redirect URI shown above</li>
                                <li>Copy Client ID and Client Secret here</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>