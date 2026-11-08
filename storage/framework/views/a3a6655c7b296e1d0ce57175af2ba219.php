<div>

    <div class="card-header bg-primary text-white mb-3">Business Settings</div>
    <form wire:submit.prevent="update" enctype="multipart/form-data">

        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="currency" class="form-label">currency Icon</label>
                <select class="form-control <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="currency"
                    wire:model.defer="currency">
                    <option value="">Select Currency</option>
                    <option value="৳">BDT - Bangladeshi Taka (৳)</option>
                    <option value="$">USD - US Dollar ($)</option>
                    <option value="€">EUR - Euro (€)</option>
                    <option value="£">GBP - British Pound (£)</option>
                    <option value="¥">JPY - Japanese Yen (¥)</option>
                    <option value="₹">INR - Indian Rupee (₹)</option>
                    <option value="₩">KRW - South Korean Won (₩)</option>
                    <option value="₽">RUB - Russian Ruble (₽)</option>
                    <option value="₺">TRY - Turkish Lira (₺)</option>
                    <option value="₫">VND - Vietnamese Dong (₫)</option>
                    <option value="₱">PHP - Philippine Peso (₱)</option>
                    <option value="₪">ILS - Israeli Shekel (₪)</option>
                    <option value="฿">THB - Thai Baht (฿)</option>
                    <option value="₦">NGN - Nigerian Naira (₦)</option>
                </select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="row mb-3">
            <div class="col-md-8">
                <label for="footer_copyright" class="form-label">Footer Copyright Text</label>
                <input type="text" class="form-control <?php $__errorArgs = ['footer_copyright'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    id="footer_copyright" wire:model.defer="footer_copyright"
                    placeholder="© <?php echo e(date('Y')); ?> <?php echo e(setting('app_name') ?: 'Padmazon'); ?>. All rights reserved.">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['footer_copyright'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <small class="text-muted">সব পেজের footer-এর বাম পাশে এই টেক্সট দেখাবে। খালি রাখলে ডিফল্ট copyright দেখাবে।</small>
            </div>
        </div>

        
        <div class="border-top pt-3 mt-4 mb-3">
            <h5 class="mb-1">Purchase Actions</h5>
            <small class="text-muted d-block mb-3">
                Add to Cart এবং Buy Now আলাদাভাবে চালু/বন্ধ ও নাম পরিবর্তন করা যাবে। দুটোই বন্ধ থাকলে product page-এ
                নিচের disabled text দেখাবে। বন্ধ করলেও আগে থেকে cart-এ থাকা product checkout করা যাবে।
            </small>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="add_to_cart_enabled"
                            id="add_to_cart_enabled" value="1">
                        <label class="form-check-label" for="add_to_cart_enabled">Enable Add to Cart</label>
                    </div>
                    <label for="add_to_cart_label" class="form-label">Add to Cart Button Text</label>
                    <input type="text" class="form-control <?php $__errorArgs = ['add_to_cart_label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        id="add_to_cart_label" wire:model.defer="add_to_cart_label"
                        placeholder="Add to Cart" maxlength="40">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['add_to_cart_label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="col-md-6">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="buy_now_enabled"
                            id="buy_now_enabled" value="1">
                        <label class="form-check-label" for="buy_now_enabled">Enable Buy Now</label>
                    </div>
                    <label for="buy_now_label" class="form-label">Buy Now Button Text</label>
                    <input type="text" class="form-control <?php $__errorArgs = ['buy_now_label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        id="buy_now_label" wire:model.defer="buy_now_label" placeholder="Buy Now" maxlength="40">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['buy_now_label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="purchase_disabled_text" class="form-label">Both Buttons Disabled Text</label>
                    <input type="text" class="form-control <?php $__errorArgs = ['purchase_disabled_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        id="purchase_disabled_text" wire:model.defer="purchase_disabled_text"
                        placeholder="Contact us to order" maxlength="120">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['purchase_disabled_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <small class="text-muted">Add to Cart ও Buy Now দুটোই বন্ধ থাকলে এই লেখাটি দেখাবে।</small>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="cart_drawer_enabled"
                            id="cart_drawer_enabled" value="1">
                        <label class="form-check-label" for="cart_drawer_enabled">Enable Cart Drawer</label>
                    </div>
                    <small class="text-muted">
                        Add to Cart করলে পাশ থেকে slide করে cart drawer খুলবে এবং website-এ floating cart button দেখাবে।
                        বন্ধ থাকলে আগের মতো header-এর cart page ব্যবহার হবে।
                    </small>
                </div>
            </div>
        </div>

        
        <div class="border-top pt-3 mt-4 mb-3">
            <h5 class="mb-1">Home Page Category Menu</h5>
            <small class="text-muted d-block mb-3">
                চালু থাকলে হোমপেজে বাম পাশে ক্যাটাগরি সাইডবার দেখাবে, স্লাইডার তার ডানে থাকবে।
                বন্ধ করলে সাইডবার থাকবে না — স্লাইডার পুরো প্রস্থ নেবে এবং হেডারের মেনুতে
                <b>Categories</b> বাটন আসবে, যেটায় ক্লিক করলে ড্রপডাউন প্যানেল খুলবে।
                মোবাইলে (&lt; 768px) দুই ক্ষেত্রেই হেডারের হ্যামবার্গার থেকে বাম দিকের ড্রয়ার খুলবে।
            </small>

            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" wire:model="home_category_sidebar"
                    id="home_category_sidebar" value="1">
                <label class="form-check-label" for="home_category_sidebar">Show Category Sidebar on Home Page</label>
            </div>
        </div>

        
        <div class="border-top pt-3 mt-4 mb-3">
            <h5 class="mb-1">Delivery Charge (City-wise)</h5>
            <small class="text-muted d-block mb-3">
                যত ইচ্ছা শহর যোগ করুন — Product Detail Page-এ ঠিক এই তালিকা (শহর + চার্জ) দেখাবে।
                প্রথম দুইটি row checkout-এর Inside/Outside charge হিসেবেও ব্যবহৃত হবে।
            </small>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-2">
                    <thead>
                        <tr>
                            <th style="width:45%;">City / Area Name</th>
                            <th style="width:30%;">Delivery Charge</th>
                            <th style="width:25%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $delivery_cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr wire:key="delivery-city-<?php echo e($index); ?>">
                                <td>
                                    <input type="text"
                                        class="form-control <?php $__errorArgs = ['delivery_cities.' . $index . '.city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        wire:model.defer="delivery_cities.<?php echo e($index); ?>.city"
                                        placeholder="যেমন: Dhaka / Chattogram / Sylhet">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['delivery_cities.' . $index . '.city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control <?php $__errorArgs = ['delivery_cities.' . $index . '.charge'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        wire:model.defer="delivery_cities.<?php echo e($index); ?>.charge"
                                        placeholder="0">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['delivery_cities.' . $index . '.charge'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        wire:click="removeDeliveryCity(<?php echo e($index); ?>)"
                                        title="এই শহরটি বাদ দিন">
                                        <i class="bx bx-trash"></i> Remove
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($delivery_cities)): ?>
                <div class="text-muted small mb-2">কোনো শহর যোগ করা নেই।</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addDeliveryCity">
                <i class="bx bx-plus"></i> Add City
            </button>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" wire:model="attribute_system" id="attribute_system"
                value="1">
            <label class="form-check-label" for="attribute_system">Attribute System</label>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" wire:model="automation_menu" id="automation_menu"
                value="1">
            <label class="form-check-label" for="automation_menu">Automation Menu <small class="text-muted">(sidebar-এ Automation section দেখাবে)</small></label>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" wire:model="seller_menu" id="seller_menu"
                value="1">
            <label class="form-check-label" for="seller_menu">Seller Manage Menu <small class="text-muted">(sidebar-এ Seller Manage দেখাবে; vendor-দের Payout থাকে)</small></label>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" wire:model="seo_menu" id="seo_menu"
                value="1">
            <label class="form-check-label" for="seo_menu">Google SEO Menu <small class="text-muted">(sidebar-এ Google SEO section দেখাবে)</small></label>
        </div>

       


        <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2" wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">
                    Save Changes
                </span>
                <span wire:loading wire:target="update">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Saving...
                </span>
            </button>
        </div>
    </form>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\settings\partial\business-settings.blade.php ENDPATH**/ ?>