<x-admin-layout title="Create Property" :breadcrumbs="[['label' => 'Properties', 'url' => route('admin.properties.index')], ['label' => 'Create']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Create Property</h1>
            <p class="text-sm text-text-muted">List a new property. New listings start as pending review.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.properties.store') }}" enctype="multipart/form-data">
        @csrf

        @include('admin.properties.partials.form', ['property' => null])

        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button :href="route('admin.properties.index')" variant="outline">Cancel</x-ui.button>
            <x-ui.button type="submit" variant="primary">Create Property</x-ui.button>
        </div>
    </form>
</x-admin-layout>
