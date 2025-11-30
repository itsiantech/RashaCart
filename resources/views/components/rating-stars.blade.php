@props(['rating' => 0, 'count' => 0])

<div class="stars">
    @for ($i = 1; $i <= 5; $i++)
        <i class="{{ $i <= round($rating) ? 'fas' : 'far' }} fa-star"></i>
    @endfor

    <span class="rating-count">({{ $count }})</span>
</div>
