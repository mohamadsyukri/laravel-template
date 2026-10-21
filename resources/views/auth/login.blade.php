<x-layouts.guest :title="__('Sign in')">
    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <x-form.input name="email" type="email" :label="__('Email')" required autofocus autocomplete="username" />
        <x-form.input name="password" type="password" :label="__('Password')" required autocomplete="current-password" />

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                <label class="form-check-label" for="remember">{{ __('Remember me') }}</label>
            </div>
            <a href="{{ route('password.request') }}" class="small">{{ __('Forgot password?') }}</a>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Sign in') }}
        </button>
    </form>
</x-layouts.guest>
