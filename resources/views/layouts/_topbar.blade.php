<header class="navbar bg-body border-bottom px-3 py-2">
    <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#app-sidebar" aria-controls="app-sidebar" aria-label="{{ __('Menu') }}">
        <i class="bi bi-list"></i>
    </button>

    <div class="dropdown ms-auto">
        <button class="btn btn-link text-body text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li><span class="dropdown-item-text small text-body-secondary">{{ auth()->user()->getRoleNames()->implode(', ') }}</span></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>{{ __('Profile') }}</a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>{{ __('Sign out') }}</button>
                </form>
            </li>
        </ul>
    </div>
</header>
