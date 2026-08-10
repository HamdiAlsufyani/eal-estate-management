<x-admin-layout title="{{ $amenity->name }}" :breadcrumbs="[['label' => 'Amenities', 'url' => route('admin.amenities.index')], ['label' => $amenity->name]]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text">{{ $amenity->name }}</h1>
                <p class="text-sm text-text-muted">Amenity details and usage.</p>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                @can('update', $amenity)
                    <x-ui.button :href="route('admin.amenities.edit', $amenity)" variant="outline" size="sm">Edit</x-ui.button>
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
                        @if ($amenity->icon)
                            <span class="text-sm font-semibold uppercase">{{ substr($amenity->icon, 0, 3) }}</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        @endif
                    </span>

                    <h2 class="mt-4 text-lg font-semibold text-text">{{ $amenity->name }}</h2>
                    <p class="text-sm text-text-muted">{{ $amenity->slug }}</p>

                    <div class="mt-3">
                        <x-ui.badge :variant="$amenity->is_active ? 'success' : 'gray'">
                            {{ $amenity->is_active ? 'Active' : 'Inactive' }}
                        </x-ui.badge>
                    </div>
                </div>

                <dl class="mt-6 space-y-4 border-t border-border pt-6 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">Icon</dt>
                        <dd class="font-medium text-text">{{ $amenity->icon ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">Created</dt>
                        <dd class="font-medium text-text">{{ $amenity->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">Last Updated</dt>
                        <dd class="font-medium text-text">{{ $amenity->updated_at->format('M j, Y') }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            {{-- Stats + Actions --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-1">
                    <x-ui.stat-card label="Properties" :value="number_format($amenity->properties_count)" variant="primary">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </x-slot>
                    </x-ui.stat-card>
                </div>

                <x-ui.card title="Actions">
                    <div class="flex flex-wrap gap-3">
                        @can('update', $amenity)
                            <x-ui.button :href="route('admin.amenities.edit', $amenity)" variant="outline" size="sm">Edit Amenity</x-ui.button>
                        @endcan

                        @can('delete', $amenity)
                            @if ($amenity->properties_count > 0)
                                <p class="text-sm text-text-muted">
                                    This amenity is assigned to {{ number_format($amenity->properties_count) }} propert{{ $amenity->properties_count === 1 ? 'y' : 'ies' }} and cannot be deleted. Deactivate it instead to hide it from new listings.
                                </p>
                            @else
                                <x-ui.button
                                    type="button"
                                    variant="danger"
                                    size="sm"
                                    class="ml-auto"
                                    @click="$dispatch('open-modal', 'delete-amenity')"
                                >
                                    Delete Amenity
                                </x-ui.button>
                            @endif
                        @endcan
                    </div>
                </x-ui.card>
            </div>
        </div>

        @can('delete', $amenity)
            @if ($amenity->properties_count === 0)
                <x-ui.modal name="delete-amenity" max-width="md">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-text">Delete Amenity</h3>
                        <p class="mt-2 text-sm text-text-muted">
                            Are you sure you want to delete <span class="font-medium text-text">{{ $amenity->name }}</span>? This action cannot be undone.
                        </p>

                        <div class="mt-6 flex justify-end gap-3">
                            <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-amenity')">Cancel</x-ui.button>
                            <form method="POST" action="{{ route('admin.amenities.destroy', $amenity) }}">
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
