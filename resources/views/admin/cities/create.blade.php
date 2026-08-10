<x-admin-layout title="Create City" :breadcrumbs="[['label' => 'Cities', 'url' => route('admin.cities.index')], ['label' => 'Create']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Create City</h1>
            <p class="text-sm text-text-muted">Add a new city.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.cities.store') }}">
        @csrf

        <x-ui.card title="City Information">
            @include('admin.cities.partials.form', ['city' => null])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.cities.index')" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Create City</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
