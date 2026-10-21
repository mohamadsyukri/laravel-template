{{-- Built from the menus table and filtered by what this user may open: App\Support\Menu\Sidebar. --}}
<aside class="offcanvas-lg offcanvas-start app-sidebar bg-body border-end" tabindex="-1" id="app-sidebar" aria-label="{{ __('Main menu') }}">
    <div class="offcanvas-header border-bottom">
        <a href="{{ route('dashboard') }}" class="fw-semibold text-decoration-none">{{ config('app.name') }}</a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#app-sidebar" aria-label="{{ __('Close') }}"></button>
    </div>

    <div class="d-none d-lg-block px-3 py-3 border-bottom">
        <a href="{{ route('dashboard') }}" class="fw-semibold text-decoration-none">
            <i class="bi bi-grid-1x2-fill text-primary me-1"></i>{{ config('app.name') }}
        </a>
    </div>

    <nav class="offcanvas-body d-block p-2">
        <ul class="nav flex-column gap-1">
            @foreach ($menuItems as $item)
                @if ($item['children'])
                    <li class="nav-item mt-3">
                        <div class="small text-uppercase text-body-secondary fw-semibold px-2 mb-1">
                            @if ($item['icon'])
                                <i class="bi {{ $item['icon'] }} me-1"></i>
                            @endif
                            {{ $item['label'] }}
                        </div>
                        <ul class="nav flex-column gap-1">
                            @foreach ($item['children'] as $child)
                                @include('layouts._sidebar-link', ['item' => $child])
                            @endforeach
                        </ul>
                    </li>
                @else
                    @include('layouts._sidebar-link', ['item' => $item])
                @endif
            @endforeach
        </ul>
    </nav>
</aside>
