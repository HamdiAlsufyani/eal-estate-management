@php
    $availabilityVariants = ['available' => 'success', 'reserved' => 'warning', 'sold' => 'gray', 'rented' => 'info'];
@endphp

<x-customer-layout title="{{ __('navigation.recently_viewed') }}" :breadcrumbs="[['label' => __('navigation.recently_viewed')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('navigation.recently_viewed') }}</h1>
            <p class="text-sm text-text-muted">{{ __('customer.recently_viewed_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <x-ui.button :href="route('customer.recently-viewed')" variant="{{ is_null($purpose) ? 'primary' : 'outline' }}" size="sm">
                    {{ __('properties.all_properties') }}
                </x-ui.button>
                <x-ui.button :href="route('customer.recently-viewed', ['purpose' => 'sale'])" variant="{{ $purpose === 'sale' ? 'primary' : 'outline' }}" size="sm">
                    {{ __('properties.for_sale') }}
                </x-ui.button>
                <x-ui.button :href="route('customer.recently-viewed', ['purpose' => 'rent'])" variant="{{ $purpose === 'rent' ? 'primary' : 'outline' }}" size="sm">
                    {{ __('properties.for_rent') }}
                </x-ui.button>
            </div>

            @if ($properties->isNotEmpty())
                <form
                    method="POST"
                    action="{{ route('customer.recently-viewed.clear') }}"
                    onsubmit="return confirm('{{ __('customer.clear_recently_viewed_confirm') }} {{ __('customer.are_you_sure') }}')"
                >
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger" size="sm">{{ __('customer.clear_all') }}</x-ui.button>
                </form>
            @endif
        </div>

        @if ($properties->isEmpty())
            <x-ui.empty-state title="{{ __('customer.no_recently_viewed') }}" description="{{ __('customer.no_recently_viewed_hint') }}">
                <x-slot name="action">
                    <x-ui.button :href="route('properties.index')" variant="primary">{{ __('properties.browse_properties') }}</x-ui.button>
                </x-slot>
            </x-ui.empty-state>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($properties as $property)
                    <div class="flex flex-col gap-2">
                        @if ($property->status === 'approved' && ! $property->trashed())
                            <x-public.property-card :property="$property" />
                        @else
                            <div class="card flex flex-col gap-3 p-4">
                                <p class="line-clamp-1 font-semibold text-text">{{ $property->title }}</p>
                                <span class="badge badge-gray w-fit">{{ __('customer.property_no_longer_available') }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-2 px-1">
                            <div class="flex items-center gap-2 text-xs text-text-muted">
                                @if ($property->status === 'approved' && ! $property->trashed())
                                    <x-ui.badge :variant="$availabilityVariants[$property->availability] ?? 'gray'">
                                        {{ __('properties.availability.' . $property->availability) }}
                                    </x-ui.badge>
                                @endif
                                <span>{{ __('customer.viewed_on', ['time' => $property->last_viewed_at->diffForHumans()]) }}</span>
                            </div>

                            <form
                                method="POST"
                                action="{{ route('customer.recently-viewed.destroy', $property) }}"
                                onsubmit="return confirm('{{ __('customer.are_you_sure') }}')"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-danger hover:underline">
                                    {{ __('customer.remove') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-2">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</x-customer-layout>
