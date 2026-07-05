<form wire:submit.prevent="search">
    <div class="search-container" style="position: relative; z-index: 100;">
        <input type="text" wire:model.live.debounce.50ms="q" placeholder="Search for products..." class="">

        @if(!empty($q))
        <div class="search-results"
            style=" position:fixed;
        top:100px;s
        left:0;
        right:0;
        background:#fff;
        border:1px solid #ddd;
        z-index:10000;
        max-height:450px;
        overflow-y:auto;
        overflow-x:hidden;
        box-shadow:0 10px 20px rgba(0,0,0,0.08);
        border-radius:10px;">

            @forelse($results as $item)
            <a wire:navigate href="{{ route('frontend.product.single.view', $item->slug) }}"
                style="display:flex; align-items:center; gap:10px; padding:8px; border-bottom:1px solid #eee; text-decoration:none; color:#333;">

                <!-- Product Image -->
                <img src="{{ $item->thumbnail ? asset('storage/' . $item->thumbnail) : 'https://placehold.co/500x500' }}"
                    alt="{{ $item->name }}"
                    style="width:40px; height:40px; object-fit:cover; border-radius:5px;">

                <!-- Text Section -->
                <div>
                    <strong>{{ $item->name }}</strong>
                    @if(isset($item->price))
                    <div style="font-size:13px; color:gray;">${{ $item->price }}</div>
                    @endif
                </div>
            </a>
            @empty
            <p style="padding:10px;">No results found</p>
            @endforelse

            @if(session()->has('search_error'))
            <p style="padding:10px;color:red;">
                {{ session('search_error') }}
            </p>
            @endif

            @if($results->count() > 0)
            @php
            $firstCategory = $results->first()->category;
            @endphp

            @if($firstCategory)
            <a wire:navigate
                href="{{ route('frontend.category.view', $firstCategory->slug) }}"
                style="display:block; padding:10px; text-align:center; font-weight:bold; background:#f8f9fa; text-decoration:none;">
                See all in {{ $firstCategory->name }}
            </a>
            @endif
            @endif

        </div>
        @endif
    </div>
</form>