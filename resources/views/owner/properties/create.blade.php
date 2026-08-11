<x-owner-layout title="{{ __('navigation.add_property') }}" :breadcrumbs="[['label' => __('navigation.my_properties'), 'url' => route('owner.properties.index')], ['label' => __('navigation.add_property')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('navigation.add_property') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.new_listing_review_notice') }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('owner.properties.store') }}" enctype="multipart/form-data">
        @csrf

        @include('admin.properties.partials.form', ['property' => null, 'routePrefix' => 'owner'])

        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button :href="route('owner.properties.index')" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
            <x-ui.button type="submit" variant="primary">{{ __('properties.create_property_button') }}</x-ui.button>
        </div>
    </form>
</x-owner-layout>
