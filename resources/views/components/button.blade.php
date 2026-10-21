@props([
    'variant' => 'primary',
    'icon' => null,
    'href' => null,
    'type' => 'button',
])
@if ($href)
    <a href="{{ $href }}" {{ $attributes->class(['btn', 'btn-'.$variant]) }}>
        @if ($icon)<i class="bi {{ $icon }} me-1"></i>@endif{{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class(['btn', 'btn-'.$variant]) }}>
        @if ($icon)<i class="bi {{ $icon }} me-1"></i>@endif{{ $slot }}
    </button>
@endif
