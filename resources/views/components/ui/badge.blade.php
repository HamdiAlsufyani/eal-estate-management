@props(['variant' => 'gray'])

@php
    $variants = [
        'primary' => 'badge-primary',
        'secondary' => 'badge-secondary',
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
        'info' => 'badge-info',
        'gray' => 'badge-gray',
    ];
@endphp

<span {{ $attributes->class(['badge', $variants[$variant] ?? $variants['gray']]) }}>
    {{ $slot }}
</span>
