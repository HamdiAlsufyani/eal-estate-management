<x-admin-layout title="{{ __('favorites.title') }}" :breadcrumbs="[['label' => __('favorites.title')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('favorites.title') }}</h1>
            <p class="text-sm text-text-muted">{{ __('favorites.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card>
            <form
                method="GET"
                action="{{ route('admin.favorites.index') }}"
                class="mb-6 grid grid-cols-1 gap-3 border-b border-border pb-6 sm:grid-cols-2 lg:grid-cols-4 lg:items-end"
            >
                <div class="lg:col-span-3">
                    <x-ui.input name="search" label="{{ __('messages.search') }}" placeholder="{{ __('favorites.filter_search_placeholder') }}" :value="$filters['search'] ?? ''" />
                </div>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="primary" class="w-full justify-center">{{ __('messages.filter') }}</x-ui.button>

                    @if (array_filter($filters))
                        <x-ui.button :href="route('admin.favorites.index')" variant="ghost" aria-label="{{ __('messages.reset_filters') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </x-ui.button>
                    @endif
                </div>
            </form>

            @if ($favorites->isEmpty())
                <x-ui.empty-state title="{{ __('favorites.no_favorites_found') }}" description="{{ __('favorites.no_favorites_found_hint') }}" />
            @else
                <x-ui.table>
                    <x-slot name="head">
                        <th>{{ __('inquiries.customer') }}</th>
                        <th>{{ __('properties.property') }}</th>
                        <th class="hidden lg:table-cell">{{ __('inquiries.date') }}</th>
                        <th class="text-right">{{ __('messages.actions') }}</th>
                    </x-slot>

                    @foreach ($favorites as $favorite)
                        <tr>
                            <td>
                                <span class="block font-medium text-text">{{ $favorite->user?->name ?? '—' }}</span>
                                <span class="block truncate text-xs text-text-muted">{{ $favorite->user?->email }}</span>
                            </td>
                            <td>
                                @if ($favorite->property)
                                    {{ $favorite->property->title }}
                                @else
                                    <span class="text-text-muted">{{ __('favorites.property_unavailable') }}</span>
                                @endif
                            </td>
                            <td class="hidden lg:table-cell text-text-muted">{{ $favorite->created_at->format('M j, Y') }}</td>
                            <td class="text-right">
                                @if ($favorite->property)
                                    <x-ui.button :href="route('admin.properties.show', $favorite->property)" variant="outline" size="sm">{{ __('properties.view_property') }}</x-ui.button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.card>

        @if ($favorites->hasPages())
            <x-ui.card class="!shadow-none">
                {{ $favorites->links() }}
            </x-ui.card>
        @endif
    </div>
</x-admin-layout>
