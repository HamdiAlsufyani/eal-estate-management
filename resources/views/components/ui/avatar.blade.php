@props(['user', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-16 w-16 text-lg',
        'xl' => 'h-24 w-24 text-2xl',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $photoUrl = $user->profilePhotoUrl();
@endphp

@if ($photoUrl)
    <img
        src="{{ $photoUrl }}"
        alt="{{ $user->name }}"
        {{ $attributes->class(["$sizeClass rounded-full object-cover ring-2 ring-surface shadow-soft shrink-0"]) }}
    />
@else
    <span
        {{ $attributes->class(["$sizeClass inline-flex shrink-0 items-center justify-center rounded-full bg-primary font-semibold text-white ring-2 ring-surface shadow-soft"]) }}
    >
        {{ $user->initials() }}
    </span>
@endif
