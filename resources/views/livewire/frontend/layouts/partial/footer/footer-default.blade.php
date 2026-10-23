<!-- Footer -->
<footer class="main-footer">
    <!-- Footer Part 1 -->
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <!-- About Column -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h3 class="footer-title">About</h3>
                    <p>{{ setting('app_description') }}</p>
                </div>

                <!-- Contact Us Column -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h3 class="footer-title mb-3">Contact Us</h3>
                    <ul class="list-unstyled footer-links mb-0">
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt me-2 "></i>
                            <span>{{ setting('contact_address') }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:{{ setting('contact_phone') }}" class="text-decoration-none ">
                                {{ setting('contact_phone') }}
                            </a>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fas fa-envelope me-2 "></i>
                            <a href="mailto:{{ setting('contact_email') }}" class="text-decoration-none ">
                                {{ setting('contact_email') }}
                            </a>
                        </li>
                    </ul>
                </div>


                <!-- Quick Links Column -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="list-unstyled footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('frontend.category.view') }}">Categories</a></li>
                        <li><a href="{{route('frontend.about.us')}}">About Us</a></li>

                    </ul>
                </div>

                <!-- Useful Links Column -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h3 class="footer-title">Useful Links</h3>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">My Account</a></li>
                        <li><a wire:navigate href="{{route('frontend.order.tracking')}}">Order Tracking</a></li>
                        <li><a href="#">Wishlist</a></li>
                        <li><a href="tel:{{ setting('contact_phone') }}">Help Center</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="bottom-nav">
        <a wire:navigate href="{{ route('home') }}"
            class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fas fa-home"></i><span>Home</span>
        </a>

        <a wire:navigate href="{{ route('frontend.category.view') }}"
            class="{{ request()->routeIs('frontend.category.*') ? 'active' : '' }}">
            <i class="fa-solid fa-table-list"></i><span>Categories</span>
        </a>

        <a href="tel:{{ setting('contact_phone') }}"
            class="{{ request()->routeIs('contact_phone') ? 'active' : '' }}">
            <i class="fas fa-phone"></i><span>Call</span>
        </a>

        <a wire:navigate href="{{ route('frontend.order.tracking') }}"
            class="{{ request()->routeIs('frontend.order.tracking') ? 'active' : '' }}">
            <i class="fa-solid fa-cube"></i><span>Trak Order</span>
        </a>

        <a wire:navigate href="{{ route('login') }}"
            class="{{ request()->routeIs('login') ? 'active' : '' }}">
            <i class="fas fa-user"></i><span>Account</span>
        </a>
    </div>
    <!-- Bottom Nav end -->

    <!-- Footer Part 2 -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <div class="footer-bottom-links">
                    <a href="#">Wishlist</a>
                    <span class="divider">|</span>
                    <a wire:navigate href="{{route('frontend.refund.and.returns')}}">Refund and Returns</a>
                    <span class="divider">|</span>
                    <a wire:navigate href="{{route('frontend.terms.and.conditions')}}">Terms & Condition</a>
                    <span class="divider">|</span>
                    <a wire:navigate href="{{route('frontend.privacy.policy')}}">Privacy Policy</a>
                </div>
                <div class="copyright">
                    {{ setting('footer_copyright') ?: '© ' . date('Y') . ' ' . (setting('app_name') ?: 'Padmazon') . '. All rights reserved.' }}
                </div>
            </div>
        </div>
    </div>
</footer>