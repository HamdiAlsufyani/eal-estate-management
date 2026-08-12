<x-admin-layout title="{{ __('properties.create_district') }}" :breadcrumbs="[['label' => __('navigation.districts'), 'url' => route('admin.districts.index')], ['label' => __('messages.create')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.create_district') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.create_district_subtitle') }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.districts.store') }}">
        @csrf

        <x-ui.card title="{{ __('properties.district_information') }}">
            @include('admin.districts.partials.form', ['district' => null, 'cities' => $cities])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.districts.index')" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('properties.create_district') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
