<x-admin-layout title="Create Property Type" :breadcrumbs="[['label' => 'Property Types', 'url' => route('admin.property-types.index')], ['label' => 'Create']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Create Property Type</h1>
            <p class="text-sm text-text-muted">Add a new real estate category.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.property-types.store') }}">
        @csrf

        <x-ui.card title="Property Type Information">
            @include('admin.property-types.partials.form', ['propertyType' => null])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.property-types.index')" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Create Property Type</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
