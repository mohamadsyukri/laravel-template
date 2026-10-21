{{-- The browser's own date picker (no plugin to clash with Livewire). Value is Y-m-d. --}}
@props([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
])
@php
    $id = $attributes->get('id', 'field-'.$name);
    $current = old($name, $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value);
@endphp
<div class="mb-3">
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}@if ($required)<span class="text-danger ms-1">*</span>@endif
        </label>
    @endif
    <input type="date" name="{{ $name }}" id="{{ $id }}" value="{{ $current }}"
        {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
        @required($required)>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
