@php
    $icons = [
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />',
        'building' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />',
        'x' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75 14.25 14.25M14.25 9.75 9.75 14.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
        'eye' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
        'heart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />',
        'chat' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />',
    ];

    $lifetimeCards = [
        ['key' => 'total_users', 'label' => __('analytics.total_users'), 'variant' => 'primary', 'icon' => 'users'],
        ['key' => 'total_owners', 'label' => __('analytics.total_owners'), 'variant' => 'secondary', 'icon' => 'users'],
        ['key' => 'total_customers', 'label' => __('analytics.total_customers'), 'variant' => 'info', 'icon' => 'users'],
        ['key' => 'total_properties', 'label' => __('analytics.total_properties'), 'variant' => 'primary', 'icon' => 'building'],
        ['key' => 'approved_properties', 'label' => __('analytics.approved_properties'), 'variant' => 'success', 'icon' => 'check'],
        ['key' => 'pending_properties', 'label' => __('analytics.pending_properties'), 'variant' => 'warning', 'icon' => 'clock'],
        ['key' => 'rejected_properties', 'label' => __('analytics.rejected_properties'), 'variant' => 'danger', 'icon' => 'x'],
        ['key' => 'total_inquiries', 'label' => __('analytics.total_inquiries'), 'variant' => 'info', 'icon' => 'chat'],
        ['key' => 'total_favorites', 'label' => __('analytics.total_favorites'), 'variant' => 'secondary', 'icon' => 'heart'],
        ['key' => 'total_property_views', 'label' => __('analytics.total_property_views'), 'variant' => 'info', 'icon' => 'eye'],
    ];

    $periodCards = [
        ['key' => 'new_properties', 'label' => __('analytics.new_properties'), 'variant' => 'primary', 'icon' => 'building'],
        ['key' => 'new_users', 'label' => __('analytics.new_users'), 'variant' => 'secondary', 'icon' => 'users'],
        ['key' => 'views', 'label' => __('analytics.total_property_views'), 'variant' => 'info', 'icon' => 'eye'],
        ['key' => 'favorites', 'label' => __('analytics.total_favorites'), 'variant' => 'warning', 'icon' => 'heart'],
        ['key' => 'inquiries', 'label' => __('analytics.total_inquiries'), 'variant' => 'success', 'icon' => 'chat'],
    ];
@endphp

<div>
    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-text-muted">{{ __('analytics.lifetime_totals') }}</h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($lifetimeCards as $card)
            <x-ui.stat-card :label="$card['label']" :value="number_format($overview[$card['key']])" :variant="$card['variant']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        {!! $icons[$card['icon']] !!}
                    </svg>
                </x-slot>
            </x-ui.stat-card>
        @endforeach
    </div>
</div>

<div>
    <h2 class="mb-3 mt-6 text-sm font-semibold uppercase tracking-wide text-text-muted">
        {{ __('analytics.period_stats') }} &middot; {{ __('analytics.range.' . $filters['range']) }}
    </h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ($periodCards as $card)
            <x-ui.stat-card :label="$card['label']" :value="number_format($period[$card['key']])" :variant="$card['variant']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        {!! $icons[$card['icon']] !!}
                    </svg>
                </x-slot>
            </x-ui.stat-card>
        @endforeach
    </div>
</div>
