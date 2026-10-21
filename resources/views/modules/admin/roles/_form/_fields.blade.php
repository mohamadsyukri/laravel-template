{{--
    Shared by create and edit. Permissions are grouped by screen; a sidebar item shows for a role
    as soon as it holds that screen's List permission.
--}}
<x-card :title="__('Role details')">
    <x-form.input name="name" :label="__('Role name')" :value="$role->name" maxlength="100" required :readonly="$isSuperAdmin" />

    <h3 class="h6 mt-4 mb-3">{{ __('Permissions') }}</h3>

    @if ($isSuperAdmin)
        <div class="alert alert-info mb-0">{{ __('This role passes every permission check, so there is nothing to tick.') }}</div>
    @else
        @error('permissions')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        @php($checked = old('permissions', $granted))
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <tbody>
                    @foreach ($groups as $group)
                        <tr>
                            <th class="text-nowrap">{{ $group['label'] }}</th>
                            <td>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach ($group['permissions'] as $permission => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission }}"
                                                id="permission-{{ str_replace('.', '-', $permission) }}" @checked(in_array($permission, $checked, true))>
                                            <label class="form-check-label" for="permission-{{ str_replace('.', '-', $permission) }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <x-slot:footer>
        <x-button type="submit" icon="bi-save">{{ __('Save') }}</x-button>
    </x-slot:footer>
</x-card>
