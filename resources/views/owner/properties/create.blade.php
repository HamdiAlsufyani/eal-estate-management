<x-owner-layout title="Add Property" :breadcrumbs="[['label' => 'My Properties', 'url' => route('owner.properties.index')], ['label' => 'Add Property']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Add Property</h1>
            <p class="text-sm text-text-muted">New listings are reviewed by our team before they go live.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('owner.properties.store') }}" enctype="multipart/form-data">
        @csrf

        @include('admin.properties.partials.form', ['property' => null, 'routePrefix' => 'owner'])

        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button :href="route('owner.properties.index')" variant="outline">Cancel</x-ui.button>
            <x-ui.button type="submit" variant="primary">Create Property</x-ui.button>
        </div>
    </form>
</x-owner-layout>
