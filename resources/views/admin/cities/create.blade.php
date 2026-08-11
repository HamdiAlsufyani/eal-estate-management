<x-admin-layout title="{{ __('properties.create_city') }}" :breadcrumbs="[['label' => __('navigation.cities'), 'url' => route('admin.cities.index')], ['label' => __('messages.create')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.create_city') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.create_city_subtitle') }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.cities.store') }}">
        @csrf

        <x-ui.card title="{{ __('properties.city_information') }}">
            @include('admin.cities.partials.form', ['city' => null])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.cities.index')" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('properties.create_city') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
