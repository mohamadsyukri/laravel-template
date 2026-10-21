@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'help' => null,
    'errorBag' => 'default',
])
@php
    // Array names (rows[0][email]) become dot keys for old() and the error bag.
    $key = rtrim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $attributes->get('id', 'field-'.str_replace('.', '-', $key));
    $bag = $errors->getBag($errorBag);
@endphp
<div class="mb-3">
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}@if ($required)<span class="text-danger ms-1">*</span>@endif
        </label>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}"
        value="{{ $type === 'password' ? '' : old($key, $value) }}"
        {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $bag->has($key)]) }}
        @required($required)>
    @if ($help)
        <div class="form-text">{{ $help }}</div>
    @endif
    @if ($bag->has($key))
        <div class="invalid-feedback">{{ $bag->first($key) }}</div>
    @endif
</div>
