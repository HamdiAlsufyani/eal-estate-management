<x-admin-layout title="Edit Amenity" :breadcrumbs="[['label' => 'Amenities', 'url' => route('admin.amenities.index')], ['label' => $amenity->name, 'url' => route('admin.amenities.show', $amenity)], ['label' => 'Edit']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Edit Amenity</h1>
            <p class="text-sm text-text-muted">Update {{ $amenity->name }}'s details.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.amenities.update', $amenity) }}">
        @csrf
        @method('PUT')

        <x-ui.card title="Amenity Information">
            @include('admin.amenities.partials.form', ['amenity' => $amenity])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.amenities.show', $amenity)" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
