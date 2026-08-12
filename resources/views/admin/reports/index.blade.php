@php
    $tabs = [
        'properties' => __('analytics.reports_properties'),
        'users' => __('analytics.reports_users'),
        'inquiries' => __('analytics.reports_inquiries'),
        'views' => __('analytics.reports_views'),
        'favorites' => __('analytics.reports_favorites'),
    ];
@endphp

<x-admin-layout title="{{ __('analytics.reports') }}" :breadcrumbs="[['label' => __('analytics.reports')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('analytics.reports') }}</h1>
            <p class="text-sm text-text-muted">{{ __('analytics.reports_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-wrap gap-2 border-b border-border pb-4">
            @foreach ($tabs as $key => $label)
                <a
                    href="{{ route('admin.reports.index', ['type' => $key]) }}"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors {{ $type === $key ? 'bg-primary text-white' : 'bg-primary-soft text-primary hover:bg-primary/20' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <x-ui.card>
            @include('admin.reports.partials.filters')

            @includeWhen($type === 'properties', 'admin.reports.partials.properties-table')
            @includeWhen($type === 'users', 'admin.reports.partials.users-table')
            @includeWhen($type === 'inquiries', 'admin.reports.partials.inquiries-table')
            @includeWhen($type === 'views', 'admin.reports.partials.views-table')
            @includeWhen($type === 'favorites', 'admin.reports.partials.favorites-table')
        </x-ui.card>

        @if ($results->hasPages())
            <x-ui.card class="!shadow-none">
                {{ $results->links() }}
            </x-ui.card>
        @endif
    </div>
</x-admin-layout>
