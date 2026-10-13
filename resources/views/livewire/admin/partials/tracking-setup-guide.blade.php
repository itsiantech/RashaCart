<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bx bx-book me-2"></i>সেটআপ গাইড (Setup Guide)</h5>
    </div>
    <div class="card-body">
        {{-- Setup Progress --}}
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading"><i class="bx bx-check-circle me-2"></i>সেটআপ প্রোগ্রেস</h6>
            <ul class="mb-0">
                <li class="{{ \App\Models\TrackingPlatform::where('platform', 'meta')->whereNotNull('pixel_id')->exists() ? 'text-decoration-line-through text-muted' : '' }}">
                    Meta Pixel ID সেভ করা হয়েছে
                </li>
                <li class="{{ \App\Models\TrackingPlatform::where('platform', 'meta')->whereNotNull('access_token')->exists() ? 'text-decoration-line-through text-muted' : '' }}">
                    Meta Access Token সেভ করা হয়েছে
                </li>
                <li class="{{ \App\Models\TrackingPlatform::where('platform', 'meta')->where('is_active', true)->exists() ? 'text-decoration-line-through text-muted' : '' }}">
                    Meta Pixel সক্রিয় করা হয়েছে
                </li>
                <li class="{{ \App\Models\TrackingLog::where('platform_id', \App\Models\TrackingPlatform::where('platform', 'meta')->value('id'))->where('status', 'success')->exists() ? 'text-decoration-line-through text-muted' : '' }}">
                    প্রথম সার্ভার ইভেন্ট সফলভাবে পাঠানো হয়েছে
                </li>
                <li class="{{ \App\Models\TrackingLog::where('event_name', 'Purchase')->where('status', 'success')->exists() ? 'text-decoration-line-through text-muted' : '' }}">
                    Purchase ইভেন্ট ডিডুপ্লিকেশন নিশ্চিত (browser + server একই event_id)
                </li>
            </ul>
        </div>

        {{-- Guide Content --}}
        <div class="accordion" id="trackingGuideAccordion">
            {{-- Meta CAPI Setup --}}
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#metaSetup">
                        <i class="bx bxl-facebook-circle me-2"></i>Facebook CAPI সেটআপ
                    </button>
                </h2>
                <div id="metaSetup" class="accordion-collapse collapse show" data-bs-parent="#trackingGuideAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li><strong>Meta Business Suite</strong> এ যান: <a href="https://business.facebook.com" target="_blank">business.facebook.com</a></li>
                            <li><strong>Events Manager</strong> এ যান এবং আপনার পিক্সেল সিলেক্ট করুন</li>
                            <li><strong>Settings</strong> > <strong>Conversions API</strong> এ যান</li>
                            <li><strong>Generate Access Token</strong> ক্লিক করুন</li>
                            <li>টোকেন কপি করুন এবং এখানে পেস্ট করুন</li>
                            <li><strong>Test Event Code</strong> কপি করুন (টেস্টিং এর জন্য)</li>
                            <li>পিক্সেল ID কপি করুন এবং এখানে পেস্ট করুন</li>
                            <li><strong>Save</strong> বাটনে ক্লিক করুন</li>
                            <li><strong>Test Connection</strong> বাটনে ক্লিক করে ভেরিফাই করুন</li>
                        </ol>
                        <p class="mb-0">
                            <a href="https://developers.facebook.com/docs/marketing-api/conversions-api/" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-external-link me-1"></i>অফিসিয়াল ডকুমেন্টেশন
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- TikTok Events API Setup --}}
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tiktokSetup">
                        <i class="bx bxl-tiktok me-2"></i>TikTok Events API সেটআপ
                    </button>
                </h2>
                <div id="tiktokSetup" class="accordion-collapse collapse" data-bs-parent="#trackingGuideAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li><strong>TikTok Ads Manager</strong> এ যান: <a href="https://ads.tiktok.com" target="_blank">ads.tiktok.com</a></li>
                            <li><strong>Assets</strong> > <strong>Event</strong> > <strong>Web Events</strong> এ যান</li>
                            <li>আপনার পিক্সেল সিলেক্ট করুন অথবা নতুন তৈরি করুন</li>
                            <li><strong>Event Setup</strong> > <strong>Events API</strong> এ যান</li>
                            <li><strong>Generate Access Token</strong> ক্লিক করুন</li>
                            <li>টোকেন কপি করুন এবং এখানে পেস্ট করুন</li>
                            <li>পিক্সেল ID কপি করুন এবং এখানে পেস্ট করুন</li>
                            <li><strong>Save</strong> বাটনে ক্লিক করুন</li>
                            <li><strong>Test Connection</strong> বাটনে ক্লিক করে ভেরিফাই করুন</li>
                        </ol>
                        <p class="mb-0">
                            <a href="https://ads.tiktok.com/help/article/standard-events-parameters" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-external-link me-1"></i>অফিসিয়াল ডকুমেন্টেশন
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- GA4 Setup --}}
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ga4Setup">
                        <i class="bx bxl-google me-2"></i>GA4 Measurement Protocol সেটআপ
                    </button>
                </h2>
                <div id="ga4Setup" class="accordion-collapse collapse" data-bs-parent="#trackingGuideAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li><strong>Google Analytics</strong> এ যান: <a href="https://analytics.google.com" target="_blank">analytics.google.com</a></li>
                            <li>আপনার প্রপার্টি সিলেক্ট করুন</li>
                            <li><strong>Admin</strong> > <strong>Data Streams</strong> এ যান</li>
                            <li>আপনার ওয়েব স্ট্রিম সিলেক্ট করুন</li>
                            <li><strong>Measurement ID</strong> কপি করুন (G-XXXXXXXXXX)</li>
                            <li><strong>Measurement Protocol API Secrets</strong> এ যান</li>
                            <li><strong>Create</strong> ক্লিক করে নতুন API Secret তৈরি করুন</li>
                            <li>API Secret কপি করুন এবং এখানে পেস্ট করুন</li>
                            <li><strong>Save</strong> বাটনে ক্লিক করুন</li>
                            <li><strong>Test Connection</strong> বাটনে ক্লিক করে ভেরিফাই করুন</li>
                        </ol>
                        <p class="mb-0">
                            <a href="https://developers.google.com/analytics/devguides/collection/protocol/ga4" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-external-link me-1"></i>অফিসিয়াল ডকুমেন্টেশন
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Event Rules --}}
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eventRules">
                        <i class="bx bx-list-check me-2"></i>ইভেন্ট রুলস ব্যাখ্যা
                    </button>
                </h2>
                <div id="eventRules" class="accordion-collapse collapse" data-bs-parent="#trackingGuideAccordion">
                    <div class="accordion-body">
                        <h6>ইভেন্ট টাইপসমূহ:</h6>
                        <ul>
                            <li><strong>PageView:</strong> পেজ ভিউ ট্র্যাক করে</li>
                            <li><strong>ViewContent:</strong> প্রোডাক্ট দেখলে ট্র্যাক করে</li>
                            <li><strong>AddToCart:</strong> কার্টে প্রোডাক্ট যোগ করলে ট্র্যাক করে</li>
                            <li><strong>InitiateCheckout:</strong> চেকআউট শুরু করলে ট্র্যাক করে</li>
                            <li><strong>Purchase:</strong> অর্ডার সম্পন্ন হলে ট্র্যাক করে</li>
                        </ul>
                        <h6>ট্রিগার টাইপ:</h6>
                        <ul>
                            <li><strong>Frontend:</strong> ব্রাউজার থেকে ইভেন্ট পাঠায়</li>
                            <li><strong>Order Placed:</strong> অর্ডার প্লেস হলে ইভেন্ট পাঠায়</li>
                            <li><strong>Order Status:</strong> নির্দিষ্ট স্ট্যাটাসে (যেমন: confirmed) ইভেন্ট পাঠায়</li>
                        </ul>
                        <h6>ভ্যালু মোড:</h6>
                        <ul>
                            <li><strong>Full:</strong> মোট অর্ডার ভ্যালু</li>
                            <li><strong>Exclude Shipping:</strong> ডেলিভারি চার্জ বাদ দিয়ে ভ্যালু</li>
                            <li><strong>Custom:</strong> কাস্টম ভ্যালু</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- How to Switch Pixel --}}
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#switchPixel">
                        <i class="bx bx-sync me-2"></i>পিক্সেল কিভাবে পরিবর্তন করবেন
                    </button>
                </h2>
                <div id="switchPixel" class="accordion-collapse collapse" data-bs-parent="#trackingGuideAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li>এই পেজে আসুন</li>
                            <li>যে প্ল্যাটফর্মের পিক্সেল পরিবর্তন করতে চান সেই ট্যাবে যান</li>
                            <li>নতুন Pixel ID এবং Access Token পেস্ট করুন</li>
                            <li><strong>Save</strong> বাটনে ক্লিক করুন</li>
                            <li>কোন কোড পরিবর্তন বা ডিপ্লয়মেন্ট প্রয়োজন নেই!</li>
                            <li>পরবর্তী ইভেন্ট স্বয়ংক্রিয়ভাবে নতুন পিক্সেলে পাঠানো হবে</li>
                        </ol>
                        <div class="alert alert-success">
                            <i class="bx bx-check-circle me-2"></i>
                            <strong>গুরুত্বপূর্ণ:</strong> পিক্সেল পরিবর্তন করলে সাথে সাথে ট্র্যাকিং নতুন পিক্সেলে চালু হয়ে যাবে। কোন কোড পরিবর্তন বা সার্ভার রিস্টার্ট প্রয়োজন নেই।
                        </div>
                    </div>
                </div>
            </div>

            {{-- Verification --}}
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#verification">
                        <i class="bx bx-check-double me-2"></i>ভেরিফিকেশন চেকলিস্ট
                    </button>
                </h2>
                <div id="verification" class="accordion-collapse collapse" data-bs-parent="#trackingGuideAccordion">
                    <div class="accordion-body">
                        <h6>Meta:</h6>
                        <ul>
                            <li>Events Manager এ Test Events চেক করুন</li>
                            <li>ব্রাউজার এবং সার্ভার ইভেন্ট deduplication চেক করুন</li>
                            <li>Event Match Quality 7+ টার্গেট করুন</li>
                        </ul>
                        <h6>TikTok:</h6>
                        <ul>
                            <li>Events Manager এ Test Events চেক করুন</li>
                            <li>ইভেন্ট রিয়েল-টাইমে দেখছেন কিনা চেক করুন</li>
                        </ul>
                        <h6>GA4:</h6>
                        <ul>
                            <li>DebugView এ ইভেন্ট চেক করুন</li>
                            <li>Realtime রিপোর্টে ইভেন্ট দেখুন</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Troubleshooting --}}
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#troubleshooting">
                        <i class="bx bx-wrench me-2"></i>ট্রাবলশুটিং
                    </button>
                </h2>
                <div id="troubleshooting" class="accordion-collapse collapse" data-bs-parent="#trackingGuideAccordion">
                    <div class="accordion-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>সমস্যা</th>
                                    <th>সমাধান</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Test Connection ফেইল হচ্ছে</td>
                                    <td>Pixel ID এবং Access Token চেক করুন</td>
                                </tr>
                                <tr>
                                    <td>ইভেন্ট পাঠাচ্ছে না</td>
                                    <td>Queue worker চলছে কিনা চেক করুন</td>
                                </tr>
                                <tr>
                                    <td>টোকেন মাস্কড দেখাচ্ছে</td>
                                    <td>এটি নরমাল, টোকেন এনক্রিপ্টেড আছে</td>
                                </tr>
                                <tr>
                                    <td>PII এরর</td>
                                    <td>ফোন নম্বর ফরম্যাট চেক করুন (01XXXXXXXXX)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
