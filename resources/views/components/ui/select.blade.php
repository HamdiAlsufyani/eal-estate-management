@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'placeholder' => null,
    'selected' => null,
    'hint' => null,
    'error' => null,
])

@php
    $error = $error ?? ($name ? $errors->first($name) : null);
    $current = old($name, $selected);
@endphp

<div>
    @if ($label)
        <label @if ($name) for="{{ $name }}" @endif class="field-label">{{ $label }}</label>
    @endif

    <select
        @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
        {{ $attributes->class(['form-select', 'has-error' => $error]) }}
    >
        @if ($placeholder)
            <option value="" @selected(is_null($current))>{{ $placeholder }}</option>
        @endif

        @if (count($options))
            @foreach ($options as $value => $label)
                <option value="{{ $value }}" @selected((string) $current === (string) $value)>{{ $label }}</option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>

    @if ($hint && ! $error)
        <p class="field-hint">{{ $hint }}</p>
    @endif

    @if ($error)
        <p class="field-error">{{ $error }}</p>
    @endif
</div>
