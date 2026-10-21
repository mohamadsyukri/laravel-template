{{-- options: value => label. Add :select2="true" for search; multiple posts name[]. --}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'multiple' => false,
    'select2' => false,
])
@php
    $id = $attributes->get('id', 'field-'.$name);
    $selected = collect(old($name, $value))->map(fn (mixed $item): string => (string) $item)->all();
    $invalid = $errors->has($name) || $errors->has($name.'.*');
@endphp
<div class="mb-3">
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}@if ($required)<span class="text-danger ms-1">*</span>@endif
        </label>
    @endif
    <select name="{{ $multiple ? $name.'[]' : $name }}" id="{{ $id }}"
        @if ($multiple) multiple @endif
        @if ($placeholder) data-placeholder="{{ $placeholder }}" @endif
        {{ $attributes->except('id')->class(['form-select', 'js-select2' => $select2, 'is-invalid' => $invalid]) }}
        @required($required)>
        @if ($placeholder && ! $multiple)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected(in_array((string) $optionValue, $selected, true))>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($invalid)
        <div class="invalid-feedback d-block">{{ $errors->first($name) ?: $errors->first($name.'.*') }}</div>
    @endif
</div>
