{{--
    The screen card: list actions (Add, Print, Excel) go in the actions slot, right-aligned in the
    header; the save button goes in the footer slot — one per tab. No Back button inside cards.
--}}
@props(['title' => null])
<div {{ $attributes->class(['card shadow-sm mb-4']) }}>
    @if ($title || isset($actions))
        <div class="card-header bg-body d-flex flex-wrap align-items-center gap-2 py-3">
            @if ($title)
                <h2 class="h6 mb-0 me-auto">{{ $title }}</h2>
            @endif
            @isset($actions)
                <div class="d-flex flex-wrap gap-2 ms-auto">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-footer bg-body d-flex justify-content-end gap-2 py-3">
            {{ $footer }}
        </div>
    @endisset
</div>
