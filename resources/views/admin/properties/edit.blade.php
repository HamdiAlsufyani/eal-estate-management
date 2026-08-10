<x-admin-layout title="Edit Property" :breadcrumbs="[['label' => 'Properties', 'url' => route('admin.properties.index')], ['label' => $property->title, 'url' => route('admin.properties.show', $property)], ['label' => 'Edit']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Edit Property</h1>
            <p class="text-sm text-text-muted">Update {{ $property->title }}'s details.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.properties.update', $property) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.properties.partials.form', ['property' => $property])

        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button :href="route('admin.properties.show', $property)" variant="outline">Cancel</x-ui.button>
            <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
        </div>
    </form>
</x-admin-layout>
