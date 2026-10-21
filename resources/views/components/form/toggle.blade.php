{{-- A switch that always posts a value: 0 from the hidden field when off, 1 when on. --}}
@props([
    'name',
    'label',
    'checked' => false,
])
<div class="form-check form-switch mb-3">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" role="switch" name="{{ $name }}" id="field-{{ $name }}" value="1"
        {{ $attributes->class(['form-check-input', 'is-invalid' => $errors->has($name)]) }}
        @checked(old($name, $checked))>
    <label class="form-check-label" for="field-{{ $name }}">{{ $label }}</label>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
