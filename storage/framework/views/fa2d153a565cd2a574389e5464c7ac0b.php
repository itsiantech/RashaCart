<div style="font-family: 'SolaimanLipi', 'Kalpurush', sans-serif;">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/vendor/fonts/boxicons.css')); ?>" />
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->hero_image): ?>
    <section class="hero-section" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 40px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white mb-4 mb-lg-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->main_title): ?>
                    <h1 class="display-5 fw-bold mb-2"><?php echo e($landingPage->main_title); ?></h1>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <h2 class="h3 mb-4"><?php echo e($landingPage->title); ?></h2>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->countdown_end_date && $landingPage->countdown_end_date > now()): ?>
                    <div class="countdown-timer mb-4">
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="bg-white text-dark rounded p-2 text-center" style="min-width: 70px;">
                                <div id="days" class="fs-3 fw-bold">00</div>
                                <small>Days</small>
                            </div>
                            <div class="bg-white text-dark rounded p-2 text-center" style="min-width: 70px;">
                                <div id="hours" class="fs-3 fw-bold">00</div>
                                <small>Hours</small>
                            </div>
                            <div class="bg-white text-dark rounded p-2 text-center" style="min-width: 70px;">
                                <div id="minutes" class="fs-3 fw-bold">00</div>
                                <small>Minutes</small>
                            </div>
                            <div class="bg-white text-dark rounded p-2 text-center" style="min-width: 70px;">
                                <div id="seconds" class="fs-3 fw-bold">00</div>
                                <small>Seconds</small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->regular_price && $landingPage->discount_price): ?>
                    <div class="price-section mb-4">
                        <span class="text-decoration-line-through fs-4">রেগুলার মূল্য ৳<?php echo e(number_format($landingPage->regular_price, 0)); ?></span>
                        <br>
                        <span class="fs-3 fw-bold">বর্তমান ডিসকাউন্ট মূল্য ৳<?php echo e(number_format($landingPage->discount_price, 0)); ?> টাকা</span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <a href="#checkout" class="btn btn-danger btn-lg px-4 py-3 fw-bold">
                        <i class="bx bx-cart me-2"></i> অর্ডার করতে চাই
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="hero-image-wrapper">
                        <img src="<?php echo e(asset('storage/' . $landingPage->hero_image)); ?>"
                            alt="<?php echo e($landingPage->title); ?>"
                            class="img-fluid rounded-3 shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->benefits_title && !empty($landingPage->benefits_list)): ?>
    <section class="benefits-section py-4 bg-light">
        <div class="container">
            <div class="bg-success text-white text-center py-3 rounded mb-3">
                <h3 class="fw-bold mb-0"><?php echo e($landingPage->benefits_title); ?></h3>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="benefits-list bg-white p-4 rounded shadow-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $landingPage->benefits_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $benefit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-start mb-2">
                            <i class="<?php echo e($landingPage->listIconClass('benefits_icon')); ?> text-success fs-4 me-2 mt-1"></i>
                            <p class="mb-0"><?php echo e($benefit); ?></p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="benefits-sidebar bg-white p-4 rounded shadow-sm text-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->discount_price): ?>
                        <div class="alert alert-success fw-bold mb-3">
                            ১ কেজি ৳<?php echo e(number_format($landingPage->discount_price, 0)); ?> টাকা মাত্র
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <a href="#checkout" class="btn btn-danger btn-lg w-100 fw-bold">
                            <i class="bx bx-cart me-2"></i> অর্ডার করতে চাই
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->trust_title && !empty($landingPage->trust_list)): ?>
    <section class="trust-section py-4">
        <div class="container">
            <div class="bg-success text-white text-center py-3 rounded mb-3">
                <h3 class="fw-bold mb-0"><?php echo e($landingPage->trust_title); ?></h3>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="trust-list bg-white p-4 rounded shadow-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $landingPage->trust_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trust): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-start mb-2">
                            <i class="<?php echo e($landingPage->listIconClass('trust_icon')); ?> text-success fs-4 me-2 mt-1"></i>
                            <p class="mb-0"><?php echo e($trust); ?></p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->secondary_image): ?>
                <div class="col-lg-6">
                    <div class="secondary-image-wrapper">
                        <img src="<?php echo e(asset('storage/' . $landingPage->secondary_image)); ?>"
                            alt="Quality Assurance"
                            class="img-fluid rounded shadow-sm">
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->contact_number): ?>
    <section class="contact-banner py-3 bg-success">
        <div class="container">
            <div class="text-center text-white">
                <h5 class="fw-bold mb-0">
                    যে কোন প্রয়োজনে যোগাযোগ করুন <?php echo e($landingPage->contact_number); ?>

                </h5>
            </div>
        </div>
    </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section id="checkout" class="checkout-section py-4 bg-light">
        <div class="container">
            <h3 class="text-center mb-4 fw-bold">অর্ডার করতে নিচের ফর্মটি পূরণ করুন</h3>

            <div class="row">
                
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 fw-bold">বিলিং ডিটেইলস</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="placeOrder">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">নাম <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name"
                                        class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="আপনার নাম লিখুন">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">দেশ/অঞ্চল <span class="text-danger">*</span></label>
                                    <select wire:model="country"
                                        class="form-select <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="Bangladesh">Bangladesh</option>
                                        <option value="India">India</option>
                                        <option value="Pakistan">Pakistan</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">স্ট্রিট ঠিকানা <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="shipping_address"
                                        class="form-control <?php $__errorArgs = ['shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="আপনার স্ট্রিট ঠিকানা লিখুন">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">শহর <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control"
                                        placeholder="আপনার শহর লিখুন">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">ফোন নম্বর <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="phone"
                                        class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="01XXXXXXXXX">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">আপনার পণ্য</label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="card mb-2">
                                        <div class="card-body d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->thumbnail): ?>
                                                <img src="<?php echo e(asset('storage/' . $product->thumbnail)); ?>"
                                                    alt="<?php echo e($product->name); ?>"
                                                    class="rounded me-3"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0"><?php echo e($product->name); ?></h6>
                                                    <small class="text-muted">
                                                        ৳<?php echo e(number_format($landingPage->discount_price ?? $product->price, 2)); ?>

                                                    </small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <button type="button"
                                                    wire:click="decrementQuantity(<?php echo e($product->id); ?>)"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="bx bx-minus"></i>
                                                </button>
                                                <input type="text"
                                                    wire:model="quantities.<?php echo e($product->id); ?>"
                                                    class="form-control form-control-sm mx-2 text-center"
                                                    style="width: 50px;" readonly>
                                                <button type="button"
                                                    wire:click="incrementQuantity(<?php echo e($product->id); ?>)"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="bx bx-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0 fw-bold">আপনার অর্ডার</h5>
                        </div>
                        <div class="card-body">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="fw-bold"><?php echo e($product->name); ?></h6>
                                <div class="d-flex justify-content-between">
                                    <span>সাবটোটাল</span>
                                    <span>৳<?php echo e(number_format(($landingPage->discount_price ?? $product->price) * ($quantities[$product->id] ?? 1), 2)); ?></span>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="d-flex justify-content-between fw-bold mb-3">
                                <span>মোট</span>
                                <span>৳<?php echo e(number_format($this->subtotal, 2)); ?></span>
                            </div>

                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">ডেলিভারি চার্জ</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check">
                                        <input type="radio" wire:model="delivery_zone" value="inside"
                                            id="inside" class="form-check-input" wire:change="$refresh">
                                        <label for="inside" class="form-check-label">
                                            ঢাকার ভেতর (৳<?php echo e(setting('dc_inside', 60)); ?>)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" wire:model="delivery_zone" value="outside"
                                            id="outside" class="form-check-input" wire:change="$refresh">
                                        <label for="outside" class="form-check-label">
                                            ঢাকার বাইরে (৳<?php echo e(setting('dc_outside', 120)); ?>)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                                <span>মোট</span>
                                <span>৳<?php echo e(number_format($this->total, 2)); ?></span>
                            </div>

                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">পেমেন্ট মেথড</label>
                                <div class="form-check mb-2">
                                    <input type="radio" wire:model="payment_method" value="cod"
                                        id="cod" class="form-check-input" checked>
                                    <label for="cod" class="form-check-label">
                                        ক্যাশ অন ডেলিভারি
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="radio" wire:model="payment_method" value="bkash"
                                        id="bkash" class="form-check-input">
                                    <label for="bkash" class="form-check-label">
                                        bKash
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="radio" wire:model="payment_method" value="nagad"
                                        id="nagad" class="form-check-input">
                                    <label for="nagad" class="form-check-label">
                                        Nagad
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" wire:model="payment_method" value="rocket"
                                        id="rocket" class="form-check-input">
                                    <label for="rocket" class="form-check-label">
                                        Rocket
                                    </label>
                                </div>
                            </div>

                            <small class="text-muted d-block mb-3">
                                আপনার ব্যক্তিগত তথ্য প্রক্রিয়া করার জন্য আমাদের <a href="#">গোপনীয়তা নীতি</a> পড়ুন।
                            </small>

                            <button type="submit" wire:click="placeOrder"
                                class="btn btn-success btn-lg w-100 fw-bold py-3">
                                Place Order ৳<?php echo e(number_format($this->total, 2)); ?>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <footer class="py-3 bg-dark text-white text-center">
        <small>
            Copyright © <?php echo e(date('Y')); ?> আপনার ব্যবসার নাম | এই ওয়েবসাইটটি তৈরি করা হয়েছে ❤️ দিয়ে
        </small>
    </footer>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPage->countdown_end_date && $landingPage->countdown_end_date > now()): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countdownDate = new Date('<?php echo e($landingPage->countdown_end_date->format('Y-m-d\TH:i:s')); ?>').getTime();

            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = countdownDate - now;

                if (distance < 0) {
                    clearInterval(timer);
                    document.getElementById('days').textContent = '00';
                    document.getElementById('hours').textContent = '00';
                    document.getElementById('minutes').textContent = '00';
                    document.getElementById('seconds').textContent = '00';
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('days').textContent = String(days).padStart(2, '0');
                document.getElementById('hours').textContent = String(hours).padStart(2, '0');
                document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
            }, 1000);
        });
    </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/landing-page/public-landing-page.blade.php ENDPATH**/ ?>