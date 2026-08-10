@php
    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
    $availabilityVariants = ['available' => 'success', 'reserved' => 'warning', 'sold' => 'gray', 'rented' => 'info'];
    $availabilityOptions = $property->purpose === 'sale'
        ? ['available' => 'Available', 'reserved' => 'Reserved', 'sold' => 'Sold']
        : ['available' => 'Available', 'reserved' => 'Reserved', 'rented' => 'Rented'];
    $images = $property->getMedia('property-images');
@endphp

<x-admin-layout title="{{ $property->title }}" :breadcrumbs="[['label' => 'Properties', 'url' => route('admin.properties.index')], ['label' => $property->title]]">
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
                <p class="text-sm text-text-muted">Property details, approval status, and history.</p>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                @can('update', $property)
                    <x-ui.button :href="route('admin.properties.edit', $property)" variant="outline" size="sm">Edit</x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div
        x-data="{}"
        @if ($errors->has('reason')) x-init="$dispatch('open-modal', 'reject-property')" @endif
        class="space-y-6"
    >
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="danger" dismissible>{{ session('error') }}</x-ui.alert>
        @endif

        @if ($errors->any())
            <x-ui.alert variant="danger" dismissible>{{ $errors->first() }}</x-ui.alert>
        @endif

        {{-- Gallery --}}
        @if ($images->isNotEmpty())
            <x-ui.card title="Gallery">
                <div class="grid grid-cols-2 gap-2 p-2 sm:grid-cols-4">
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
            {{-- Details --}}
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
                        @if ($property->latitude && $property->longitude)
                            <div>
                                <dt class="text-text-muted">Coordinates</dt>
                                <dd class="mt-0.5 font-medium text-text">{{ $property->latitude }}, {{ $property->longitude }}</dd>
                            </div>
                        @endif
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
                            <dt class="text-text-muted">Owner</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->user?->name ?? '—' }}</dd>
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
                                <th>Changed By</th>
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
                                    <td class="text-text-muted">{{ $history->user?->name ?? 'System' }}</td>
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
                @can('changeStatus', $property)
                    <x-ui.card title="Approval">
                        <div class="flex flex-col gap-3">
                            @if (in_array('approved', $availableTransitions, true))
                                <form method="POST" action="{{ route('admin.properties.status', $property) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved" />
                                    <x-ui.button type="submit" variant="success" class="w-full justify-center">Approve</x-ui.button>
                                </form>
                            @endif

                            @if (in_array('rejected', $availableTransitions, true))
                                <x-ui.button
                                    type="button"
                                    variant="danger"
                                    class="w-full justify-center"
                                    @click="$dispatch('open-modal', 'reject-property')"
                                >
                                    Reject
                                </x-ui.button>
                            @endif

                            @if (in_array('pending', $availableTransitions, true))
                                <form method="POST" action="{{ route('admin.properties.status', $property) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="pending" />
                                    <x-ui.button type="submit" variant="outline" class="w-full justify-center">Move Back to Pending</x-ui.button>
                                </form>
                            @endif

                            @if (empty($availableTransitions))
                                <p class="text-sm text-text-muted">No further status changes are available for this property.</p>
                            @endif
                        </div>
                    </x-ui.card>
                @endcan

                @can('changeAvailability', $property)
                    @if ($property->status === 'approved')
                        <x-ui.card title="Availability">
                            <form method="POST" action="{{ route('admin.properties.availability', $property) }}" class="space-y-3">
                                @csrf
                                <x-ui.select name="availability" :options="$availabilityOptions" :selected="$property->availability" />
                                <x-ui.button type="submit" variant="outline" class="w-full justify-center">Update Availability</x-ui.button>
                            </form>
                        </x-ui.card>
                    @endif
                @endcan

                @can('delete', $property)
                    <x-ui.card title="Danger Zone">
                        <p class="text-sm text-text-muted">Deleting a property removes it from listings. This action can be reversed by a database administrator only.</p>
                        <x-ui.button
                            type="button"
                            variant="danger"
                            class="mt-4 w-full justify-center"
                            @click="$dispatch('open-modal', 'delete-property')"
                        >
                            Delete Property
                        </x-ui.button>
                    </x-ui.card>
                @endcan
            </div>
        </div>

        @can('changeStatus', $property)
            <x-ui.modal name="reject-property" max-width="md">
                <form method="POST" action="{{ route('admin.properties.status', $property) }}">
                    @csrf
                    <input type="hidden" name="status" value="rejected" />

                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-text">Reject Property</h3>
                        <p class="mt-2 text-sm text-text-muted">
                            Please provide a reason for rejecting <span class="font-medium text-text">{{ $property->title }}</span>. This will be recorded in the property's status history.
                        </p>

                        <div class="mt-4">
                            <x-ui.textarea
                                name="reason"
                                label="Reason"
                                placeholder="e.g. Property documents are incomplete."
                                required
                            >{{ old('reason') }}</x-ui.textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'reject-property')">Cancel</x-ui.button>
                            <x-ui.button type="submit" variant="danger">Reject Property</x-ui.button>
                        </div>
                    </div>
                </form>
            </x-ui.modal>
        @endcan

        @can('delete', $property)
            <x-ui.modal name="delete-property" max-width="md">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-text">Delete Property</h3>
                    <p class="mt-2 text-sm text-text-muted">
                        Are you sure you want to delete <span class="font-medium text-text">{{ $property->title }}</span>? This action cannot be undone.
                    </p>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-property')">Cancel</x-ui.button>
                        <form method="POST" action="{{ route('admin.properties.destroy', $property) }}">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" variant="danger">Delete</x-ui.button>
                        </form>
                    </div>
                </div>
            </x-ui.modal>
        @endcan
    </div>
</x-admin-layout>
