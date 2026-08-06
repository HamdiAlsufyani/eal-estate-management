@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'error' => null,
    'rows' => 4,
])

@php
    $error = $error ?? ($name ? $errors->first($name) : null);
@endphp

<div>
    @if ($label)
        <label @if ($name) for="{{ $name }}" @endif class="field-label">{{ $label }}</label>
    @endif

    <textarea
        @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
        rows="{{ $rows }}"
        {{ $attributes->class(['form-textarea', 'has-error' => $error]) }}
    >{{ $slot }}</textarea>

    @if ($hint && ! $error)
        <p class="field-hint">{{ $hint }}</p>
    @endif

    @if ($error)
        <p class="field-error">{{ $error }}</p>
    @endif
</div>
