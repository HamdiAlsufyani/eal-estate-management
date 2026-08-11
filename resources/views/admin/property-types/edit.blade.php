<x-admin-layout title="{{ __('properties.edit_property_type') }}" :breadcrumbs="[['label' => __('navigation.property_types'), 'url' => route('admin.property-types.index')], ['label' => $propertyType->name, 'url' => route('admin.property-types.show', $propertyType)], ['label' => __('messages.edit')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.edit_property_type') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.update_subtitle', ['name' => $propertyType->name]) }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.property-types.update', $propertyType) }}">
        @csrf
        @method('PUT')

        <x-ui.card title="{{ __('properties.property_type_information') }}">
            @include('admin.property-types.partials.form', ['propertyType' => $propertyType])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.property-types.show', $propertyType)" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('messages.save_changes') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
