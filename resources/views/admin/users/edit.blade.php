<x-admin-layout title="{{ __('users.edit_user') }}" :breadcrumbs="[['label' => __('users.title'), 'url' => route('admin.users.index')], ['label' => $user->name, 'url' => route('admin.users.show', $user)], ['label' => __('messages.edit')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('users.edit_user') }}</h1>
            <p class="text-sm text-text-muted">{{ __('users.update_subtitle', ['name' => $user->name]) }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <x-ui.card title="{{ __('users.user_information') }}">
            @include('admin.users.partials.form', ['user' => $user])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.users.show', $user)" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('messages.save_changes') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
