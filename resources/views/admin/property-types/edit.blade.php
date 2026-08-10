<x-admin-layout title="Edit Property Type" :breadcrumbs="[['label' => 'Property Types', 'url' => route('admin.property-types.index')], ['label' => $propertyType->name, 'url' => route('admin.property-types.show', $propertyType)], ['label' => 'Edit']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Edit Property Type</h1>
            <p class="text-sm text-text-muted">Update {{ $propertyType->name }}'s details.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.property-types.update', $propertyType) }}">
        @csrf
        @method('PUT')

        <x-ui.card title="Property Type Information">
            @include('admin.property-types.partials.form', ['propertyType' => $propertyType])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.property-types.show', $propertyType)" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
