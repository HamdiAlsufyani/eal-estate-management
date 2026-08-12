@props([
    'label' => null,
    'value' => null,
    'variant' => 'primary',
    'trend' => null,
    'trendDirection' => 'up',
])

@php
    $iconWraps = [
        'primary' => 'bg-primary-soft text-primary ring-1 ring-primary/15',
        'secondary' => 'bg-secondary-soft text-secondary-hover ring-1 ring-secondary/20',
        'success' => 'bg-success-soft text-success-hover ring-1 ring-success/15',
        'warning' => 'bg-warning-soft text-warning-hover ring-1 ring-warning/15',
        'danger' => 'bg-danger-soft text-danger-hover ring-1 ring-danger/15',
        'info' => 'bg-info-soft text-info-hover ring-1 ring-info/15',
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
