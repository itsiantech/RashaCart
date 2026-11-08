
<footer class="hp-footer">
    <div class="hp-container">
        <div class="f-grid">

            <div class="f-contact">
                <h6>Contact Us</h6>
                <p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('contact_address')): ?>
                    <i class="fas fa-map-marker-alt me-2"></i><?php echo e(setting('contact_address')); ?><br>
                    <?php else: ?>
                    <i class="fas fa-map-marker-alt me-2"></i>Dhaka, Bangladesh<br>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('contact_phone')): ?>
                    <i class="fas fa-phone me-2"></i><?php echo e(setting('contact_phone')); ?><br>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('contact_email')): ?>
                    <i class="fas fa-envelope me-2"></i><?php echo e(setting('contact_email')); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>

            <div>
                <h6>Company</h6>
                <ul>
                    <li><a href="<?php echo e(route('frontend.about.us')); ?>">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>

            <div>
                <h6>About Us</h6>
                <ul>
                    <li><a href="<?php echo e(route('frontend.terms.and.conditions')); ?>">Terms and Conditions</a></li>
                    <li><a href="<?php echo e(route('frontend.privacy.policy')); ?>">Privacy Policy</a></li>
                </ul>
            </div>

            <div>
                <h6>Account</h6>
                <ul>
                    <li><a href="<?php echo e(route('login')); ?>">Login</a></li>
                    <li><a href="<?php echo e(route('register')); ?>">Register</a></li>
                    <li><a href="<?php echo e(route('frontend.cart.view')); ?>">My Cart</a></li>
                    <li><a href="<?php echo e(route('frontend.order.tracking')); ?>">Track Order</a></li>
                </ul>
            </div>

            <div>
                <h6>Social</h6>
                <div class="f-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

        </div>

        <div class="f-bottom">
            <span><?php echo e(setting('footer_copyright') ?: '© ' . date('Y') . ' ' . (setting('app_name') ?: 'Padmazon') . '. All rights reserved.'); ?></span>
            <div class="d-flex gap-3">
                <a href="<?php echo e(route('frontend.privacy.policy')); ?>">Privacy Policy</a>
                <a href="<?php echo e(route('frontend.terms.and.conditions')); ?>">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>


<div class="mobile-bottom-nav d-md-none">
    <a href="<?php echo e(route('home')); ?>">
        <i class="fas fa-home"></i>Home
    </a>
    <a href="<?php echo e(route('frontend.category.view')); ?>">
        <i class="fas fa-th-large"></i>Categories
    </a>
    <a href="<?php echo e(route('frontend.cart.view')); ?>">
        <i class="fas fa-shopping-cart"></i>Cart
    </a>
    <a href="#">
        <i class="fas fa-heart"></i>Wishlist
    </a>
    <a href="<?php echo e(route('profile.index')); ?>">
        <i class="fas fa-user"></i>Account
    </a>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views\partials\main-footer.blade.php ENDPATH**/ ?>