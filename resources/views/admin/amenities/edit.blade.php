<x-admin-layout title="{{ __('properties.edit_amenity') }}" :breadcrumbs="[['label' => __('navigation.amenities'), 'url' => route('admin.amenities.index')], ['label' => $amenity->name, 'url' => route('admin.amenities.show', $amenity)], ['label' => __('messages.edit')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.edit_amenity') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.update_subtitle', ['name' => $amenity->name]) }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.amenities.update', $amenity) }}">
        @csrf
        @method('PUT')

        <x-ui.card title="{{ __('properties.amenity_information') }}">
            @include('admin.amenities.partials.form', ['amenity' => $amenity])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.amenities.show', $amenity)" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('messages.save_changes') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
