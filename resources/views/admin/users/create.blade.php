<x-admin-layout title="{{ __('users.create_user') }}" :breadcrumbs="[['label' => __('users.title'), 'url' => route('admin.users.index')], ['label' => __('messages.create')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('users.create_user') }}</h1>
            <p class="text-sm text-text-muted">{{ __('users.create_subtitle') }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
        @csrf

        <x-ui.card title="{{ __('users.user_information') }}">
            @include('admin.users.partials.form', ['user' => null])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.users.index')" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('users.create_user') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
