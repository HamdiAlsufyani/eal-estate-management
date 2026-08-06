<x-admin-layout title="Edit User" :breadcrumbs="[['label' => 'Users', 'url' => route('admin.users.index')], ['label' => $user->name, 'url' => route('admin.users.show', $user)], ['label' => 'Edit']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Edit User</h1>
            <p class="text-sm text-text-muted">Update {{ $user->name }}'s account details.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <x-ui.card title="User Information">
            @include('admin.users.partials.form', ['user' => $user])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.users.show', $user)" variant="outline">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
