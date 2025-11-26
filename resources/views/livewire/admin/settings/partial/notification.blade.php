<div class="card mb-4">
    <h5 class="card-header">Notification Settings</h5>
    <div class="card-body">
        <form wire:submit.prevent="update">

            <!-- Email Notification Toggle -->
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" wire:model="email_notification" id="emailNotification"
                    value="1">
                <label class="form-check-label" for="emailNotification">Email Notifications</label>
            </div>


            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-white">
                   BULK SMS BD API Settings
                </div>
                <div class="card-body">
                    <!-- Note -->
                    <!-- SMS Notification Toggle -->
                    <div class="form-check form-switch mb-3 mt-3">
                        <input class="form-check-input" type="checkbox" wire:model="sms_notification"
                            id="smsNotification" value="1">
                        <label class="form-check-label" for="smsNotification">SMS Notifications</label>
                    </div>

                    <!-- API Base URL -->
                    <div class="mb-3">
                        <label for="sms_api_url" class="form-label">BulkSmsBD Base URL</label>
                        <input type="text" class="form-control" id="sms_api_url" wire:model="sms_api_url"
                            placeholder="https://api.example.com">
                        @error('sms_api_url')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- API Key -->
                    <div class="mb-3">
                        <label for="sms_api_key" class="form-label">API KEY</label>
                        <input type="text" class="form-control" id="sms_api_key" wire:model="sms_api_key"
                            placeholder="Enter your API key">
                        @error('sms_api_key')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- API Secret -->
                    <div class="mb-3">
                        <label for="sms_sender_id" class="form-label">Sender ID</label>
                        <input type="text" class="form-control" id="sms_sender_id" wire:model="sms_sender_id"
                            placeholder="Enter your API secret">
                        @error('sms_sender_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                     <p class="text-muted small mb-3 mt-3">
                        Note: Use your Bulksms BD SMS API credentials if active sms features.  if properly configured, you will see current SMS balance information below.
                    </p>


                   @if(setting('sms_notification', true))
                        <div class="alert alert-info">
                            <p class="mb-1"> <i class="bi bi-info-circle-fill"></i>
                                @if(setting('sms_api_key') && setting('sms_api_url') && setting('sms_sender_id'))
                                    SMS notification service is properly configured.
                                    <br> 
                                    Current SMS Balance: <strong>{{ $balance }} tk</strong>
                                    <br>
                                    Per SMS Rate: 0.35 tk
                                @else
                                    SMS notification service is not configured yet. Please provide valid API credentials.
                                @endif
                            </p>
                           
                            
                            
                        </div>
                    @endif
                    
                </div>
            </div>


            {{-- <!-- OTP SMS Notification Toggle -->
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" wire:model="otp_sms_notification" id="otpSmsNotification" value="1">
                <label class="form-check-label" for="otpSmsNotification">OTP SMS Notifications</label>
            </div>

            <!-- OTP Email Notification Toggle -->
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" wire:model="otp_email_notification" id="otpEmailNotification" value="1">
                <label class="form-check-label" for="otpEmailNotification">OTP Email Notifications</label>
            </div> --}}

            <div class="mt-3">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    Save Changes
                </button>
            </div>
        </form>




    </div>
</div>
