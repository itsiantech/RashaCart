{{-- Account icon block: guest -> auth modal trigger, customer -> dropdown, other roles -> legacy profile link. --}}
@php use App\Services\CustomerAuthSettings; @endphp
@guest
    @if(CustomerAuthSettings::enabled())
    <button type="button" class="nav-icon" title="Login / Register" data-cust-open="login" aria-haspopup="dialog" style="border:none;background:none;cursor:pointer;">
        <i class="fas fa-user"></i>
    </button>
    @endif
@elseif(Auth::user()->role === 'customer' && CustomerAuthSettings::enabled())
    <div class="cust-dropdown">
        <button type="button" class="nav-icon" id="custMenuBtn" aria-haspopup="menu" aria-expanded="false" title="My Account" style="border:none;background:none;cursor:pointer;">
            <i class="fas fa-user"></i>
        </button>
        <div class="cust-menu" id="custMenu" role="menu" hidden>
            <a href="{{ route('customer.dashboard') }}" role="menuitem"><i class="fas fa-gauge-high me-2"></i>Dashboard</a>
            <a href="{{ route('customer.dashboard', ['tab' => 'orders']) }}" role="menuitem"><i class="fas fa-bag-shopping me-2"></i>Orders</a>
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" role="menuitem"><i class="fas fa-right-from-bracket me-2"></i>Logout</button>
            </form>
        </div>
    </div>
@else
    <a href="{{ route('profile.index') }}" class="nav-icon d-none d-md-block">
        <i class="fas fa-user"></i>
    </a>
@endguest
