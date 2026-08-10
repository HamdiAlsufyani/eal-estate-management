<x-owner-layout title="Edit Property" :breadcrumbs="[['label' => 'My Properties', 'url' => route('owner.properties.index')], ['label' => $property->title, 'url' => route('owner.properties.show', $property)], ['label' => 'Edit']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Edit Property</h1>
            <p class="text-sm text-text-muted">Update {{ $property->title }}'s details.</p>
        </div>
    </x-slot>

    @if ($property->status === 'approved')
        <x-ui.alert variant="info" class="mb-6">
            This property is already approved. Changes you save here will not affect its approval status.
        </x-ui.alert>
    @endif

    <form method="POST" action="{{ route('owner.properties.update', $property) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.properties.partials.form', ['property' => $property, 'routePrefix' => 'owner'])

        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button :href="route('owner.properties.show', $property)" variant="outline">Cancel</x-ui.button>
            <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
        </div>
    </form>
</x-owner-layout>
