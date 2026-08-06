@props(['href' => null, 'type' => 'submit'])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class(['dropdown-item']) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class(['dropdown-item']) }}>
        {{ $slot }}
    </button>
@endif
