<x-admin-layout title="Edit City" :breadcrumbs="[['label' => 'Cities', 'url' => route('admin.cities.index')], ['label' => $city->name, 'url' => route('admin.cities.show', $city)], ['label' => 'Edit']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Edit City</h1>
            <p class="text-sm text-text-muted">Update {{ $city->name }}'s details.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.cities.update', $city) }}">
        @csrf
        @method('PUT')

        <x-ui.card title="City Information">
            @include('admin.cities.partials.form', ['city' => $city])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.cities.show', $city)" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
