{{-- Shared by create and edit. On edit, a blank password keeps the current one. --}}
<x-card :title="__('User details')">
    <div class="row">
        <div class="col-md-6">
            <x-form.input name="name" :label="__('Name')" :value="$user->name" maxlength="255" required />
        </div>
        <div class="col-md-6">
            <x-form.input name="email" type="email" :label="__('Email')" :value="$user->email" maxlength="255" required />
        </div>
        <div class="col-md-6">
            <x-form.input name="password" type="password" :label="__('Password')" :required="! $user->exists" autocomplete="new-password"
                :help="$user->exists ? __('Leave blank to keep the current password.') : null" />
        </div>
        <div class="col-md-6">
            <x-form.input name="password_confirmation" type="password" :label="__('Confirm password')" :required="! $user->exists" autocomplete="new-password" />
        </div>
        <div class="col-md-6">
            <x-form.select name="roles" :label="__('Roles')" :options="$roleOptions" :value="$user->exists ? $user->roles->pluck('name')->all() : []"
                :multiple="true" :select2="true" :placeholder="__('Choose roles')" required />
        </div>
        <div class="col-md-6 pt-md-4">
            <x-form.toggle name="is_active" :label="__('Active (can sign in)')" :checked="$user->is_active" />
        </div>
    </div>

    <x-slot:footer>
        <x-button type="submit" icon="bi-save">{{ __('Save') }}</x-button>
    </x-slot:footer>
</x-card>
