<x-admin-layout title="{{ __('properties.create_property_type') }}" :breadcrumbs="[['label' => __('navigation.property_types'), 'url' => route('admin.property-types.index')], ['label' => __('messages.create')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.create_property_type') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.create_property_type_subtitle') }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.property-types.store') }}">
        @csrf

        <x-ui.card title="{{ __('properties.property_type_information') }}">
            @include('admin.property-types.partials.form', ['propertyType' => null])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.property-types.index')" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('properties.create_property_type') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
