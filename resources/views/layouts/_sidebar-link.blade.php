<li class="nav-item">
    <a href="{{ $item['url'] }}" @class(['nav-link d-flex align-items-center gap-2 py-2', 'active' => $item['active']]) @if ($item['active']) aria-current="page" @endif>
        @if ($item['icon'])
            <i class="bi {{ $item['icon'] }}"></i>
        @endif
        <span>{{ $item['label'] }}</span>
    </a>
</li>
