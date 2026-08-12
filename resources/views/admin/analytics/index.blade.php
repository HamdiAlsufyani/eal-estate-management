<x-admin-layout title="{{ __('analytics.title') }}" :breadcrumbs="[['label' => __('analytics.title')]]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text">{{ __('analytics.title') }}</h1>
                <p class="text-sm text-text-muted">{{ __('analytics.subtitle') }}</p>
            </div>

            <x-ui.button :href="route('admin.reports.index')" variant="outline" class="hidden sm:inline-flex">
                {{ __('analytics.reports') }}
            </x-ui.button>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card>
            <x-analytics.date-range-filter :action="route('admin.analytics.index')" :filters="$filters" />
        </x-ui.card>

        @include('admin.analytics.partials.overview')

        @include('admin.analytics.partials.property-breakdown')

        @include('admin.analytics.partials.charts')

        @include('admin.analytics.partials.cities-types')

        @include('admin.analytics.partials.top-properties', ['title' => __('analytics.most_viewed'), 'rows' => $topViewed])

        @include('admin.analytics.partials.top-properties', ['title' => __('analytics.most_favorited'), 'rows' => $topFavorited])

        @include('admin.analytics.partials.top-properties', ['title' => __('analytics.most_inquired'), 'rows' => $topInquired])

        @include('admin.analytics.partials.segments')
    </div>
</x-admin-layout>
