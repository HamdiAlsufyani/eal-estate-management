@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'hint' => null,
    'error' => null,
])

@php
    $error = $error ?? ($name ? $errors->first($name) : null);
@endphp

<div>
    @if ($label)
        <label @if ($name) for="{{ $name }}" @endif class="field-label">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
        {{ $attributes->class(['form-input', 'has-error' => $error]) }}
    />

    @if ($hint && ! $error)
        <p class="field-hint">{{ $hint }}</p>
    @endif

    @if ($error)
        <p class="field-error">{{ $error }}</p>
    @endif
</div>
