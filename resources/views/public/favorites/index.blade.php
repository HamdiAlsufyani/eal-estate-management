<x-public-layout :title="__('favorites.my_favorites')" :meta-description="__('favorites.meta_description')">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-text sm:text-3xl">{{ __('favorites.my_favorites') }}</h1>
            <p class="mt-1 text-sm text-text-muted">{{ trans_choice('favorites.saved_count', $favorites->total(), ['count' => $favorites->total()]) }}</p>
        </div>

        @if ($favorites->isEmpty())
            <x-ui.empty-state :title="__('favorites.no_favorites')" :description="__('favorites.no_favorites_hint')">
                <x-slot name="action">
                    <x-ui.button :href="route('properties.index')" variant="primary">{{ __('properties.browse_properties') }}</x-ui.button>
                </x-slot>
            </x-ui.empty-state>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($favorites as $favorite)
                    @php $property = $favorite->property; @endphp

                    @if ($property && $property->status === 'approved' && ! $property->trashed())
                        <x-public.property-card :property="$property" />
                    @else
                        <div class="card flex flex-col gap-3 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="line-clamp-1 font-semibold text-text">{{ $property?->title ?? __('favorites.property_unavailable') }}</p>
                                    <span class="badge badge-gray mt-2">
                                        {{ $property?->trashed() ? __('favorites.property_removed') : __('favorites.currently_unavailable') }}
                                    </span>
                                </div>
                                @if ($property)
                                    <x-public.favorite-button :property="$property" :favorited="true" />
                                @endif
                            </div>
                            <p class="text-sm text-text-muted">{{ __('favorites.unavailable_hint') }}</p>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @endif
    </div>
</x-public-layout>
