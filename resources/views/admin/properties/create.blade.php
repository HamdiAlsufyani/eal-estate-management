<x-admin-layout title="{{ __('properties.add_property') }}" :breadcrumbs="[['label' => __('properties.title'), 'url' => route('admin.properties.index')], ['label' => __('messages.create')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.add_property') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.create_subtitle') }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.properties.store') }}" enctype="multipart/form-data">
        @csrf

        @include('admin.properties.partials.form', ['property' => null])

        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button :href="route('admin.properties.index')" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
            <x-ui.button type="submit" variant="primary">{{ __('properties.add_property') }}</x-ui.button>
        </div>
    </form>
</x-admin-layout>
