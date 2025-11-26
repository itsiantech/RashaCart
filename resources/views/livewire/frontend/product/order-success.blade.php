<div>
     <div class="thankyou">
        <div class="ty-icon"><i class="fa-solid fa-check"></i></div>
        <div class="ty-kicker">THANK YOU</div>
        <h1 class="ty-title">YOUR ORDER IS PLACED</h1>
        <p class="ty-text">We received your order and will begin processing it soon. Your order information appears below.</p>
        <p class="ty-order">Your order Number <span>{{ $invoiceNo }}</span></p>
        <a class="ty-btn" wire:navigate href="{{route('home')}}">Continue buying <i class="fa-solid fa-arrow-right-long"></i></a>
      </div>
</div>
