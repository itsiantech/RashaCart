@props(['method' => 'Payment Method', 'color' => '#6c757d'])

<div class="text-center py-5">
    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
         style="width:64px;height:64px;background:{{ $color }}1a;">
        <i class="bi bi-toggle-off fs-2" style="color:{{ $color }};"></i>
    </div>
    <p class="fw-medium mb-1">{{ $method }} is currently disabled</p>
    <p class="text-muted small mb-0">Toggle the switch above to configure your {{ $method }} details.</p>
</div>
