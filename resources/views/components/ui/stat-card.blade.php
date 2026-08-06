@props([
    'label' => null,
    'value' => null,
    'variant' => 'primary',
    'trend' => null,
    'trendDirection' => 'up',
])

@php
    $iconWraps = [
        'primary' => 'bg-primary/10 text-primary',
        'secondary' => 'bg-secondary/10 text-secondary-hover',
        'success' => 'bg-success-soft text-success-hover',
        'warning' => 'bg-warning-soft text-warning-hover',
        'danger' => 'bg-danger-soft text-danger-hover',
        'info' => 'bg-info-soft text-info-hover',
    ];
@endphp

<div {{ $attributes->class(['stat-card']) }}>
    <div>
        <p class="text-sm font-medium text-text-muted">{{ $label }}</p>
        <p class="mt-2 text-2xl font-semibold tracking-tight text-text">{{ $value }}</p>

        @if ($trend)
            <p class="mt-2 flex items-center gap-1 text-xs font-medium {{ $trendDirection === 'down' ? 'text-danger' : 'text-success' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 {{ $trendDirection === 'down' ? 'rotate-180' : '' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                </svg>
                {{ $trend }}
            </p>
        @endif
    </div>

    @isset($icon)
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] {{ $iconWraps[$variant] ?? $iconWraps['primary'] }}">
            {{ $icon }}
        </div>
    @endisset
</div>
