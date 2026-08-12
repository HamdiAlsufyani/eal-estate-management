<x-customer-layout title="{{ __('navigation.recently_viewed') }}" :breadcrumbs="[['label' => __('navigation.recently_viewed')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('navigation.recently_viewed') }}</h1>
            <p class="text-sm text-text-muted">{{ __('customer.recently_viewed_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($properties->isEmpty())
            <x-ui.empty-state title="{{ __('customer.no_recently_viewed') }}" description="{{ __('customer.no_recently_viewed_hint') }}">
                <x-slot name="action">
                    <x-ui.button :href="route('properties.index')" variant="primary">{{ __('properties.browse_properties') }}</x-ui.button>
                </x-slot>
            </x-ui.empty-state>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($properties as $property)
                    <div>
                        @if ($property->status === 'approved' && ! $property->trashed())
                            <x-public.property-card :property="$property" />
                        @else
                            <div class="card flex flex-col gap-3 p-4">
                                <p class="line-clamp-1 font-semibold text-text">{{ $property->title }}</p>
                                <span class="badge badge-gray w-fit">
                                    {{ $property->trashed() ? __('favorites.property_removed') : __('favorites.currently_unavailable') }}
                                </span>
                            </div>
                        @endif
                        <p class="mt-2 text-xs text-text-muted">{{ __('customer.viewed_on', ['time' => $lastViewedAt[$property->id]->diffForHumans()]) }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-customer-layout>
