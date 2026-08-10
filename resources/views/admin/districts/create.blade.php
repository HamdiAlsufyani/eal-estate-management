<x-admin-layout title="Create District" :breadcrumbs="[['label' => 'Districts', 'url' => route('admin.districts.index')], ['label' => 'Create']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Create District</h1>
            <p class="text-sm text-text-muted">Add a new district to a city.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.districts.store') }}">
        @csrf

        <x-ui.card title="District Information">
            @include('admin.districts.partials.form', ['district' => null, 'cities' => $cities])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.districts.index')" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Create District</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
