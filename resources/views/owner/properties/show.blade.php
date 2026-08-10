@php
    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
    $availabilityVariants = ['available' => 'success', 'reserved' => 'warning', 'sold' => 'gray', 'rented' => 'info'];
    $images = $property->getMedia('property-images');
    $latestRejection = $property->status === 'rejected'
        ? $property->statusHistories()->where('new_status', 'rejected')->latest()->first()
        : null;
@endphp

<x-owner-layout title="{{ $property->title }}" :breadcrumbs="[['label' => 'My Properties', 'url' => route('owner.properties.index')], ['label' => $property->title]]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl font-semibold text-text">{{ $property->title }}</h1>
                    <x-ui.badge :variant="$statusVariants[$property->status] ?? 'gray'">{{ ucfirst($property->status) }}</x-ui.badge>
                    @if ($property->featured)
                        <x-ui.badge variant="secondary">Featured</x-ui.badge>
                    @endif
                </div>
                <p class="text-sm text-text-muted">Property details and status.</p>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                @can('update', $property)
                    <x-ui.button :href="route('owner.properties.edit', $property)" variant="outline" size="sm">Edit</x-ui.button>
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

        @if ($property->status === 'pending')
            <x-ui.alert variant="warning">This property is awaiting review. It will not be visible publicly until it's approved.</x-ui.alert>
        @elseif ($property->status === 'rejected' && $latestRejection)
            <x-ui.alert variant="danger">
                <p class="font-semibold">This property was rejected</p>
                <p class="mt-1">Reason: {{ $latestRejection->reason ?? 'No reason provided.' }}</p>
                <p class="mt-1 text-xs opacity-80">Rejected on {{ $latestRejection->created_at->format('M j, Y g:ia') }}</p>
            </x-ui.alert>
        @endif

        {{-- Gallery --}}
        @if ($images->isNotEmpty())
            <x-ui.card title="Gallery">
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                    @foreach ($images as $index => $media)
                        <a href="{{ $media->getUrl() }}" target="_blank" class="group relative block overflow-hidden rounded-[var(--radius-control)]">
                            <img src="{{ $media->getUrl() }}" alt="{{ $property->title }}" class="h-32 w-full object-cover transition-transform group-hover:scale-105" />
                            @if ($index === 0)
                                <span class="absolute left-1.5 top-1.5 rounded-full bg-primary px-2 py-0.5 text-[10px] font-medium text-white">Cover</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </x-ui.card>
        @else
            <x-ui.empty-state title="No images uploaded" description="Add images from the edit page to showcase this property." />
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <x-ui.card title="Property Details">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-text-muted">Type</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->propertyType?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Purpose</dt>
                            <dd class="mt-0.5 font-medium text-text">
                                {{ ucfirst($property->purpose) }}
                                @if ($property->rent_period) ({{ ucfirst($property->rent_period) }}) @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Price</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ number_format($property->price, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Area</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ number_format($property->area, 2) }} m²</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">City / District</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->city?->name ?? '—' }} / {{ $property->district?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Address</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->address }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Bedrooms / Bathrooms</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->bedrooms }} / {{ $property->bathrooms }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Living Rooms / Kitchens</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->living_rooms }} / {{ $property->kitchens }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Floor / Parking</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->floor ?? '—' }} / {{ $property->parking_spaces }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Furnished</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->furnished ? 'Yes' : 'No' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Availability</dt>
                            <dd class="mt-0.5">
                                <x-ui.badge :variant="$availabilityVariants[$property->availability] ?? 'gray'">{{ ucfirst($property->availability) }}</x-ui.badge>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Views</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ number_format($property->views_count) }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Created</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->created_at->format('M j, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">Last Updated</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->updated_at->format('M j, Y') }}</dd>
                        </div>
                    </dl>

                    @if ($property->description)
                        <div class="mt-6 border-t border-border pt-6">
                            <dt class="text-sm text-text-muted">Description</dt>
                            <dd class="mt-1 text-sm text-text">{{ $property->description }}</dd>
                        </div>
                    @endif

                    @if ($property->amenities->isNotEmpty())
                        <div class="mt-6 border-t border-border pt-6">
                            <dt class="text-sm text-text-muted">Amenities</dt>
                            <dd class="mt-2 flex flex-wrap gap-2">
                                @foreach ($property->amenities as $amenity)
                                    <x-ui.badge variant="gray">{{ $amenity->name }}</x-ui.badge>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </x-ui.card>

                {{-- Status History --}}
                <x-ui.card title="Status History">
                    @if ($statusHistories->isEmpty())
                        <x-ui.empty-state title="No status changes yet" description="Status changes for this property will appear here." />
                    @else
                        <x-ui.table>
                            <x-slot name="head">
                                <th>Previous Status</th>
                                <th>New Status</th>
                                <th>Reason</th>
                                <th>Date</th>
                            </x-slot>

                            @foreach ($statusHistories as $history)
                                <tr>
                                    <td>
                                        @if ($history->old_status)
                                            <x-ui.badge :variant="$statusVariants[$history->old_status] ?? 'gray'">{{ ucfirst($history->old_status) }}</x-ui.badge>
                                        @else
                                            <span class="text-text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <x-ui.badge :variant="$statusVariants[$history->new_status] ?? 'gray'">{{ ucfirst($history->new_status) }}</x-ui.badge>
                                    </td>
                                    <td class="max-w-xs text-text-muted">{{ $history->reason ?? '—' }}</td>
                                    <td class="text-text-muted">{{ $history->created_at->format('M j, Y g:ia') }}</td>
                                </tr>
                            @endforeach
                        </x-ui.table>

                        @if ($statusHistories->hasPages())
                            <div class="mt-4">
                                {{ $statusHistories->links() }}
                            </div>
                        @endif
                    @endif
                </x-ui.card>
            </div>

            {{-- Actions --}}
            <div class="space-y-6">
                @can('update', $property)
                    <x-ui.card title="Actions">
                        <div class="flex flex-col gap-3">
                            <x-ui.button :href="route('owner.properties.edit', $property)" variant="outline" class="w-full justify-center">Edit Property</x-ui.button>

                            @can('delete', $property)
                                <x-ui.button
                                    type="button"
                                    variant="danger"
                                    class="w-full justify-center"
                                    @click="$dispatch('open-modal', 'delete-property')"
                                >
                                    Delete Property
                                </x-ui.button>
                            @endcan
                        </div>
                    </x-ui.card>
                @endcan
            </div>
        </div>

        @can('delete', $property)
            <x-ui.modal name="delete-property" max-width="md">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-text">Delete Property</h3>
                    <p class="mt-2 text-sm text-text-muted">
                        Are you sure you want to delete <span class="font-medium text-text">{{ $property->title }}</span>? This action cannot be undone.
                    </p>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-property')">Cancel</x-ui.button>
                        <form method="POST" action="{{ route('owner.properties.destroy', $property) }}">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" variant="danger">Delete</x-ui.button>
                        </form>
                    </div>
                </div>
            </x-ui.modal>
        @endcan
    </div>
</x-owner-layout>
