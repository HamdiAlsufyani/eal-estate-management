<x-admin-layout title="Edit District" :breadcrumbs="[['label' => 'Districts', 'url' => route('admin.districts.index')], ['label' => $district->name, 'url' => route('admin.districts.show', $district)], ['label' => 'Edit']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Edit District</h1>
            <p class="text-sm text-text-muted">Update {{ $district->name }}'s details.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.districts.update', $district) }}">
        @csrf
        @method('PUT')

        <x-ui.card title="District Information">
            @include('admin.districts.partials.form', ['district' => $district, 'cities' => $cities])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.districts.show', $district)" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
