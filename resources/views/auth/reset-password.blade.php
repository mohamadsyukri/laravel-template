<x-layouts.guest :title="__('Set a new password')">
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <x-form.input name="email" type="email" :label="__('Email')" :value="$request->email" required autocomplete="username" />
        <x-form.input name="password" type="password" :label="__('New password')" required autocomplete="new-password" />
        <x-form.input name="password_confirmation" type="password" :label="__('Confirm new password')" required autocomplete="new-password" />
        <button type="submit" class="btn btn-primary w-100">{{ __('Save new password') }}</button>
    </form>
</x-layouts.guest>
