<x-layouts.app :title="__('Profile')" :breadcrumbs="[__('Profile') => null]">
    <div class="row g-4">
        <div class="col-lg-5">
            <x-card :title="__('My account')">
                <dl class="row mb-0">
                    <dt class="col-sm-4">{{ __('Name') }}</dt>
                    <dd class="col-sm-8">{{ auth()->user()->name }}</dd>
                    <dt class="col-sm-4">{{ __('Email') }}</dt>
                    <dd class="col-sm-8">{{ auth()->user()->email }}</dd>
                    <dt class="col-sm-4">{{ __('Roles') }}</dt>
                    <dd class="col-sm-8 mb-0">{{ auth()->user()->getRoleNames()->implode(', ') }}</dd>
                </dl>
            </x-card>
        </div>

        <div class="col-lg-7">
            {{-- Saved by Fortify (PUT user/password); its errors use the updatePassword bag. --}}
            <form method="POST" action="{{ route('user-password.update') }}">
                @csrf
                @method('PUT')
                <x-card :title="__('Change password')">
                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success" role="status">{{ __('Your password has been changed.') }}</div>
                    @endif
                    <x-form.input name="current_password" type="password" :label="__('Current password')" error-bag="updatePassword" required autocomplete="current-password" />
                    <x-form.input name="password" type="password" :label="__('New password')" error-bag="updatePassword" required autocomplete="new-password" />
                    <x-form.input name="password_confirmation" type="password" :label="__('Confirm new password')" error-bag="updatePassword" required autocomplete="new-password" />

                    <x-slot:footer>
                        <x-button type="submit" icon="bi-save">{{ __('Save') }}</x-button>
                    </x-slot:footer>
                </x-card>
            </form>
        </div>
    </div>
</x-layouts.app>
