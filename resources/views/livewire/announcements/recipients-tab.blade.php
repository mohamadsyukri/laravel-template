<div>
    <x-card :title="__('Recipients')">
        <x-slot:actions>
            <x-button variant="outline-primary" icon="bi-plus-lg" wire:click="addRow">{{ __('Add row') }}</x-button>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('No.') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $index => $row)
                        <tr wire:key="recipient-row-{{ $index }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <input type="text" wire:model="rows.{{ $index }}.name" maxlength="150" aria-label="{{ __('Name') }}"
                                    @class(['form-control', 'is-invalid' => $errors->has('rows.'.$index.'.name')])>
                                @error('rows.'.$index.'.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="email" wire:model="rows.{{ $index }}.email" maxlength="150" aria-label="{{ __('Email') }}"
                                    @class(['form-control', 'is-invalid' => $errors->has('rows.'.$index.'.email')])>
                                @error('rows.'.$index.'.email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeRow({{ $index }})" title="{{ __('Remove') }}" aria-label="{{ __('Remove') }}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-slot:footer>
            <button type="button" class="btn btn-primary" data-confirm="{{ __('Save the recipient list?') }}" data-confirm-livewire="save">
                <i class="bi bi-save me-1"></i>{{ __('Save') }}
            </button>
        </x-slot:footer>
    </x-card>
</div>
