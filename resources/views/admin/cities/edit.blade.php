<x-admin-layout title="{{ __('properties.edit_city') }}" :breadcrumbs="[['label' => __('navigation.cities'), 'url' => route('admin.cities.index')], ['label' => $city->name, 'url' => route('admin.cities.show', $city)], ['label' => __('messages.edit')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.edit_city') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.update_subtitle', ['name' => $city->name]) }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.cities.update', $city) }}">
        @csrf
        @method('PUT')

        <x-ui.card title="{{ __('properties.city_information') }}">
            @include('admin.cities.partials.form', ['city' => $city])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.cities.show', $city)" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('messages.save_changes') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
