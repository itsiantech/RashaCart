<div class="row">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header">Settings</h5>
            
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3">
                        @include('livewire.admin.settings.partial.sidebar');
                    </div>

                    <div class="col-md-9">
                        <div class="tab-content" id="settings-tabContent">

                            <div class="tab-pane fade show active" id="basic" role="tabpanel"
                                aria-labelledby="basic-tab">
                                @include('livewire.admin.settings.partial.basic-settings');
                            </div>

                            <div class="tab-pane fade" id="business" role="tabpanel"
                                aria-labelledby="business-tab">
                                @include('livewire.admin.settings.partial.business-settings');
                            </div>

                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                @include('livewire.admin.settings.partial.contact-settings');
                            </div>

                            <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                                @include('livewire.admin.settings.partial.social-links');
                            </div>

                            <div class="tab-pane fade" id="notification" role="tabpanel" aria-labelledby="notification-tab">
                                @include('livewire.admin.settings.partial.notification');
                            </div>

                            <div class="tab-pane fade" id="api" role="tabpanel" aria-labelledby="api-tab">
                                @include('livewire.admin.settings.partial.api-settings');
                            </div>

                            

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>



    @push('styles')
        <style>
            .list-group-item.active {
                background-color: #696cff;
                border-color: #696cff;
            }

            .list-group-item {
                transition: all 0.3s ease;
            }

            .list-group-item:hover:not(.active) {
                background-color: #f5f5f5;
            }

            .form-section {
                margin-bottom: 2rem;
            }

            .form-section h5 {
                padding-bottom: 0.5rem;
                border-bottom: 1px solid #eee;
            }
        </style>
    @endpush

</div>
