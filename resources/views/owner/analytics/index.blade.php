<x-owner-layout title="{{ __('analytics.title') }}">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('analytics.title') }}</h1>
            <p class="text-sm text-text-muted">{{ __('analytics.owner_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card>
            <x-analytics.date-range-filter :action="route('owner.analytics.index')" :filters="$filters" />
        </x-ui.card>

        @include('owner.analytics.partials.overview')

        @include('owner.analytics.partials.charts')

        @include('owner.analytics.partials.performance')

        @include('owner.analytics.partials.top-properties', ['title' => __('analytics.most_viewed'), 'rows' => $topViewed])

        @include('owner.analytics.partials.top-properties', ['title' => __('analytics.most_favorited'), 'rows' => $topFavorited])

        @include('owner.analytics.partials.top-properties', ['title' => __('analytics.most_inquired'), 'rows' => $topInquired])
    </div>
</x-owner-layout>
