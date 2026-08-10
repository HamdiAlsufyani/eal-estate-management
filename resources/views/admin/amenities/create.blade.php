<x-admin-layout title="Create Amenity" :breadcrumbs="[['label' => 'Amenities', 'url' => route('admin.amenities.index')], ['label' => 'Create']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Create Amenity</h1>
            <p class="text-sm text-text-muted">Add a new amenity that properties can list.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.amenities.store') }}">
        @csrf

        <x-ui.card title="Amenity Information">
            @include('admin.amenities.partials.form', ['amenity' => null])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.amenities.index')" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Create Amenity</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
