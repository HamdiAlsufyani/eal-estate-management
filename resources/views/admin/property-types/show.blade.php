<x-admin-layout title="{{ $propertyType->name }}" :breadcrumbs="[['label' => 'Property Types', 'url' => route('admin.property-types.index')], ['label' => $propertyType->name]]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text">{{ $propertyType->name }}</h1>
                <p class="text-sm text-text-muted">Property type details and usage.</p>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                @can('update', $propertyType)
                    <x-ui.button :href="route('admin.property-types.edit', $propertyType)" variant="outline" size="sm">Edit</x-ui.button>
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
                        @if ($propertyType->icon)
                            <span class="text-sm font-semibold uppercase">{{ substr($propertyType->icon, 0, 3) }}</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        @endif
                    </span>

                    <h2 class="mt-4 text-lg font-semibold text-text">{{ $propertyType->name }}</h2>
                    <p class="text-sm text-text-muted">{{ $propertyType->slug }}</p>

                    <div class="mt-3">
                        <x-ui.badge :variant="$propertyType->is_active ? 'success' : 'gray'">
                            {{ $propertyType->is_active ? 'Active' : 'Inactive' }}
                        </x-ui.badge>
                    </div>
                </div>

                <dl class="mt-6 space-y-4 border-t border-border pt-6 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">Icon</dt>
                        <dd class="font-medium text-text">{{ $propertyType->icon ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">Created</dt>
                        <dd class="font-medium text-text">{{ $propertyType->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">Last Updated</dt>
                        <dd class="font-medium text-text">{{ $propertyType->updated_at->format('M j, Y') }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            {{-- Stats + Actions --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <x-ui.stat-card label="Total Properties" :value="number_format($propertyType->properties_count)" variant="primary">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </x-slot>
                    </x-ui.stat-card>

                    <x-ui.stat-card label="Active Properties" :value="number_format($propertyType->active_properties_count)" variant="success">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </x-slot>
                    </x-ui.stat-card>

                    <x-ui.stat-card label="Inactive Properties" :value="number_format($propertyType->properties_count - $propertyType->active_properties_count)" variant="warning">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </x-slot>
                    </x-ui.stat-card>
                </div>

                <x-ui.card title="Actions">
                    <div class="flex flex-wrap gap-3">
                        @can('update', $propertyType)
                            <x-ui.button :href="route('admin.property-types.edit', $propertyType)" variant="outline" size="sm">Edit Property Type</x-ui.button>
                        @endcan

                        @can('delete', $propertyType)
                            @if ($propertyType->properties_count > 0)
                                <p class="text-sm text-text-muted">
                                    This property type has {{ number_format($propertyType->properties_count) }} propert{{ $propertyType->properties_count === 1 ? 'y' : 'ies' }} assigned and cannot be deleted. Deactivate it instead to hide it from new listings.
                                </p>
                            @else
                                <x-ui.button
                                    type="button"
                                    variant="danger"
                                    size="sm"
                                    class="ml-auto"
                                    @click="$dispatch('open-modal', 'delete-property-type')"
                                >
                                    Delete Property Type
                                </x-ui.button>
                            @endif
                        @endcan
                    </div>
                </x-ui.card>
            </div>
        </div>

        @can('delete', $propertyType)
            @if ($propertyType->properties_count === 0)
                <x-ui.modal name="delete-property-type" max-width="md">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-text">Delete Property Type</h3>
                        <p class="mt-2 text-sm text-text-muted">
                            Are you sure you want to delete <span class="font-medium text-text">{{ $propertyType->name }}</span>? This action cannot be undone.
                        </p>

                        <div class="mt-6 flex justify-end gap-3">
                            <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-property-type')">Cancel</x-ui.button>
                            <form method="POST" action="{{ route('admin.property-types.destroy', $propertyType) }}">
                                @csrf @method('DELETE')
                                <x-ui.button type="submit" variant="danger">Delete</x-ui.button>
                            </form>
                        </div>
                    </div>
                </x-ui.modal>
            @endif
        @endcan
    </div>
</x-admin-layout>
