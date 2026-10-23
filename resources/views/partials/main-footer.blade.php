{{-- Shared storefront footer + mobile bottom nav (same markup as homepage). --}}
<footer class="hp-footer">
    <div class="hp-container">
        <div class="f-grid">

            <div class="f-contact">
                <h6>Contact Us</h6>
                <p>
                    @if(setting('contact_address'))
                    <i class="fas fa-map-marker-alt me-2"></i>{{ setting('contact_address') }}<br>
                    @else
                    <i class="fas fa-map-marker-alt me-2"></i>Dhaka, Bangladesh<br>
                    @endif
                    @if(setting('contact_phone'))
                    <i class="fas fa-phone me-2"></i>{{ setting('contact_phone') }}<br>
                    @endif
                    @if(setting('contact_email'))
                    <i class="fas fa-envelope me-2"></i>{{ setting('contact_email') }}
                    @endif
                </p>
            </div>

            <div>
                <h6>Company</h6>
                <ul>
                    <li><a href="{{ route('frontend.about.us') }}">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>

            <div>
                <h6>About Us</h6>
                <ul>
                    <li><a href="{{ route('frontend.terms.and.conditions') }}">Terms and Conditions</a></li>
                    <li><a href="{{ route('frontend.privacy.policy') }}">Privacy Policy</a></li>
                </ul>
            </div>

            <div>
                <h6>Account</h6>
                <ul>
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                    <li><a href="{{ route('frontend.cart.view') }}">My Cart</a></li>
                    <li><a href="{{ route('frontend.order.tracking') }}">Track Order</a></li>
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
            <span>{{ setting('footer_copyright') ?: '© ' . date('Y') . ' ' . (setting('app_name') ?: 'Padmazon') . '. All rights reserved.' }}</span>
            <div class="d-flex gap-3">
                <a href="{{ route('frontend.privacy.policy') }}">Privacy Policy</a>
                <a href="{{ route('frontend.terms.and.conditions') }}">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>

{{-- ══════════════ MOBILE BOTTOM NAV ══════════════ --}}
<div class="mobile-bottom-nav d-md-none">
    <a href="{{ route('home') }}">
        <i class="fas fa-home"></i>Home
    </a>
    <a href="{{ route('frontend.category.view') }}">
        <i class="fas fa-th-large"></i>Categories
    </a>
    <a href="{{ route('frontend.cart.view') }}">
        <i class="fas fa-shopping-cart"></i>Cart
    </a>
    <a href="#">
        <i class="fas fa-heart"></i>Wishlist
    </a>
    <a href="{{ route('profile.index') }}">
        <i class="fas fa-user"></i>Account
    </a>
</div>
