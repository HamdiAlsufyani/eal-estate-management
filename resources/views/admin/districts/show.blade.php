<x-admin-layout title="{{ $district->name }}" :breadcrumbs="[['label' => __('navigation.districts'), 'url' => route('admin.districts.index')], ['label' => $district->name]]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text">{{ $district->name }}</h1>
                <p class="text-sm text-text-muted">{{ __('properties.district_details_subtitle') }}</p>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                @can('update', $district)
                    <x-ui.button :href="route('admin.districts.edit', $district)" variant="outline" size="sm">{{ __('messages.edit') }}</x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div x-data="{}" class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="danger" dismissible>{{ session('error') }}</x-ui.alert>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Details --}}
            <x-ui.card class="lg:col-span-1">
                <div class="flex flex-col items-center text-center">
                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.159.69.159 1.006 0Z" />
                        </svg>
                    </span>

                    <h2 class="mt-4 text-lg font-semibold text-text">{{ $district->name }}</h2>
                    <p class="text-sm text-text-muted">{{ $district->slug }}</p>

                    <div class="mt-3">
                        <x-ui.badge variant="gray">{{ $district->city->name }}</x-ui.badge>
                    </div>
                </div>

                <dl class="mt-6 space-y-4 border-t border-border pt-6 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">{{ __('properties.city') }}</dt>
                        <dd class="font-medium text-text">
                            <a href="{{ route('admin.cities.show', $district->city) }}" class="text-primary hover:underline">{{ $district->city->name }}</a>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">{{ __('properties.created') }}</dt>
                        <dd class="font-medium text-text">{{ $district->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">{{ __('properties.last_updated') }}</dt>
                        <dd class="font-medium text-text">{{ $district->updated_at->format('M j, Y') }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            {{-- Stats + Actions --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-1">
                    <x-ui.stat-card label="{{ __('properties.title') }}" :value="number_format($district->properties_count)" variant="primary">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </x-slot>
                    </x-ui.stat-card>
                </div>

                <x-ui.card title="{{ __('messages.actions') }}">
                    <div class="flex flex-wrap gap-3">
                        @can('update', $district)
                            <x-ui.button :href="route('admin.districts.edit', $district)" variant="outline" size="sm">{{ __('properties.edit_district') }}</x-ui.button>
                        @endcan

                        @can('delete', $district)
                            @if ($district->properties_count > 0)
                                <p class="text-sm text-text-muted">
                                    {{ __('properties.district_cannot_delete', ['properties' => trans_choice('properties.property_count', $district->properties_count, ['count' => number_format($district->properties_count)])]) }}
                                </p>
                            @else
                                <x-ui.button
                                    type="button"
                                    variant="danger"
                                    size="sm"
                                    class="ml-auto"
                                    @click="$dispatch('open-modal', 'delete-district')"
                                >
                                    {{ __('properties.delete_district') }}
                                </x-ui.button>
                            @endif
                        @endcan
                    </div>
                </x-ui.card>
            </div>
        </div>

        @can('delete', $district)
            @if ($district->properties_count === 0)
                <x-ui.modal name="delete-district" max-width="md">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-text">{{ __('properties.delete_district') }}</h3>
                        <p class="mt-2 text-sm text-text-muted">
                            {{ __('messages.delete_confirm_prefix') }} <span class="font-medium text-text">{{ $district->name }}</span>{{ __('messages.delete_confirm_suffix') }}
                        </p>

                        <div class="mt-6 flex justify-end gap-3">
                            <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-district')">{{ __('messages.cancel') }}</x-ui.button>
                            <form method="POST" action="{{ route('admin.districts.destroy', $district) }}">
                                @csrf @method('DELETE')
                                <x-ui.button type="submit" variant="danger">{{ __('messages.delete') }}</x-ui.button>
                            </form>
                        </div>
                    </div>
                </x-ui.modal>
            @endif
        @endcan
    </div>
</x-admin-layout>
