<x-admin-layout title="{{ __('properties.create_amenity') }}" :breadcrumbs="[['label' => __('navigation.amenities'), 'url' => route('admin.amenities.index')], ['label' => __('messages.create')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.create_amenity') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.create_amenity_subtitle') }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.amenities.store') }}">
        @csrf

        <x-ui.card title="{{ __('properties.amenity_information') }}">
            @include('admin.amenities.partials.form', ['amenity' => null])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.amenities.index')" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('properties.create_amenity') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
