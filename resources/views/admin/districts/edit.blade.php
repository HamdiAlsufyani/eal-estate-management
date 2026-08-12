<x-admin-layout title="{{ __('properties.edit_district') }}" :breadcrumbs="[['label' => __('navigation.districts'), 'url' => route('admin.districts.index')], ['label' => $district->name, 'url' => route('admin.districts.show', $district)], ['label' => __('messages.edit')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('properties.edit_district') }}</h1>
            <p class="text-sm text-text-muted">{{ __('properties.update_subtitle', ['name' => $district->name]) }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.districts.update', $district) }}">
        @csrf
        @method('PUT')

        <x-ui.card title="{{ __('properties.district_information') }}">
            @include('admin.districts.partials.form', ['district' => $district, 'cities' => $cities])

            <x-slot name="footer">
                <x-ui.button :href="route('admin.districts.show', $district)" variant="outline">{{ __('messages.cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="primary">{{ __('messages.save_changes') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</x-admin-layout>
