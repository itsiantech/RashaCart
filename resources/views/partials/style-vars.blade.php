{{-- Dynamic style vars (Style Settings page). Falls back gracefully everywhere. --}}
<link href="{{ \App\Services\StyleSettings::FONT_LINK }}" rel="stylesheet" />
<style id="style-customizer-vars">
    :root {
        @foreach (\App\Services\StyleSettings::cssVars() as $k => $v){{ $k }}: {{ $v }};
        @endforeach
    }
</style>
