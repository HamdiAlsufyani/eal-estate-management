<x-admin-layout title="Create User" :breadcrumbs="[['label' => 'Users', 'url' => route('admin.users.index')], ['label' => 'Create']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Create User</h1>
            <p class="text-sm text-text-muted">Add a new user account to the platform.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
        @csrf

        <x-ui.card title="User Information">
            @include('admin.users.partials.form', ['user' => null])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.users.index')" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Create User</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
