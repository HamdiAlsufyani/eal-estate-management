<x-admin-layout title="{{ __('navigation.cities') }}" :breadcrumbs="[['label' => __('navigation.cities')]]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text">{{ __('navigation.cities') }}</h1>
                <p class="text-sm text-text-muted">{{ __('properties.cities_subtitle') }}</p>
            </div>

            @can('create', \App\Models\City::class)
                <x-ui.button :href="route('admin.cities.create')" variant="primary" class="hidden sm:inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('properties.new_city') }}
                </x-ui.button>
            @endcan
        </div>
    </x-slot>

    <div x-data="{ confirmTarget: null }" class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="danger" dismissible>{{ session('error') }}</x-ui.alert>
        @endif

        <x-ui.card>
            @include('admin.cities.partials.filters')

            @include('admin.cities.partials.table', ['cities' => $cities])
        </x-ui.card>

        @if ($cities->hasPages())
            <x-ui.card class="!shadow-none">
                {{ $cities->links() }}
            </x-ui.card>
        @endif

        <x-ui.modal name="delete-confirm" max-width="md">
            <div class="p-6">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-danger-soft text-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>

                <h3 class="mt-4 text-lg font-semibold text-text">{{ __('properties.delete_city') }}</h3>
                <p class="mt-2 text-sm text-text-muted">
                    {{ __('messages.delete_confirm_prefix') }} <span class="font-medium text-text" x-text="confirmTarget?.label"></span>{{ __('messages.delete_confirm_suffix') }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-confirm')">{{ __('messages.cancel') }}</x-ui.button>
                    <x-ui.button
                        type="button"
                        variant="danger"
                        @click="$refs.singleDeleteForm.setAttribute('action', confirmTarget.url); $refs.singleDeleteForm.requestSubmit()"
                    >
                        {{ __('messages.delete') }}
                    </x-ui.button>
                </div>
            </div>
        </x-ui.modal>

        <form x-ref="singleDeleteForm" method="POST" action="#" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</x-admin-layout>
