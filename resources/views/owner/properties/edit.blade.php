<x-owner-layout title="{{ __('properties.edit_property') }}" :breadcrumbs="[['label' => __('navigation.my_properties'), 'url' => route('owner.properties.index')], ['label' => $property->title, 'url' => route('owner.properties.show', $property)], ['label' => __('messages.edit')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.edit_property') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.edit_subtitle', ['title' => $property->title]) }}</p>
        </div>
    </x-slot>

    @if ($property->status === 'approved')
        <x-ui.alert variant="info" class="mb-6">
            {{ __('properties.already_approved_notice') }}
        </x-ui.alert>
    @endif

    <form method="POST" action="{{ route('owner.properties.update', $property) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.properties.partials.form', ['property' => $property, 'routePrefix' => 'owner'])

        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button :href="route('owner.properties.show', $property)" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
            <x-ui.button type="submit" variant="primary">{{ __('messages.save_changes') }}</x-ui.button>
        </div>
    </form>
</x-owner-layout>
