<div class="search-container" style="position: relative;">
    <input type="text" wire:model.live.debounce.300ms="q" placeholder="Search for products..." class="form-control">

    @if(!empty($q))
        <div class="search-results" 
             style="position:absolute; top:40px; left:0; right:0; background:#fff; border:1px solid #ddd; z-index:999;">
            
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

        </div>
    @endif
</div>
