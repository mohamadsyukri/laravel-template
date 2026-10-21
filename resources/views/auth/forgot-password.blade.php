<x-layouts.guest :title="__('Forgot password')">
    <p class="text-body-secondary small">{{ __('Enter your email and we will send you a link to set a new password.') }}</p>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-form.input name="email" type="email" :label="__('Email')" required autofocus autocomplete="username" />
        <button type="submit" class="btn btn-primary w-100">{{ __('Send reset link') }}</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="small">{{ __('Back to sign in') }}</a>
    </div>
</x-layouts.guest>
