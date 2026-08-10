@php
    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
@endphp

@if ($properties->isEmpty())
    <x-ui.empty-state title="No properties yet." description="Start by listing your first property.">
        <x-slot name="action">
            @can('create', \App\Models\Property::class)
                <x-ui.button :href="route('owner.properties.create')" variant="primary">Add Your First Property</x-ui.button>
            @endcan
        </x-slot>
    </x-ui.empty-state>
@else
    <x-ui.table>
        <x-slot name="head">
            <th class="w-14">Image</th>
            <th>Title</th>
            <th class="hidden md:table-cell">Type</th>
            <th class="hidden md:table-cell">Purpose</th>
            <th>Price</th>
            <th class="hidden lg:table-cell">City / District</th>
            <th>Status</th>
            <th class="hidden lg:table-cell">Availability</th>
            <th class="hidden xl:table-cell">Views</th>
            <th class="hidden xl:table-cell">Created</th>
            <th class="text-right">Actions</th>
        </x-slot>

        @foreach ($properties as $property)
            <tr>
                <td>
                    @if ($cover = $property->getFirstMediaUrl('property-images'))
                        <img src="{{ $cover }}" alt="{{ $property->title }}" class="h-12 w-12 rounded-[var(--radius-control)] object-cover" />
                    @else
                        <span class="flex h-12 w-12 items-center justify-center rounded-[var(--radius-control)] bg-primary/10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 8.69 9.31a2.25 2.25 0 0 1 3.182 0l5.658 5.658m-3-3 1.19-1.19a2.25 2.25 0 0 1 3.182 0l2.678 2.678M3 8.25V18a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 18V8.25m-18 0V6a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 6v2.25m-18 0h18" />
                            </svg>
                        </span>
                    @endif
                </td>

                <td>
                    <a href="{{ route('owner.properties.show', $property) }}" class="font-medium text-text hover:text-primary">
                        {{ $property->title }}
                    </a>
                </td>

                <td class="hidden md:table-cell text-text-muted">{{ $property->propertyType?->name ?? '—' }}</td>

                <td class="hidden md:table-cell text-text-muted">{{ ucfirst($property->purpose) }}</td>

                <td class="text-text-muted">{{ number_format($property->price, 0) }}</td>

                <td class="hidden lg:table-cell text-text-muted">{{ $property->city?->name ?? '—' }} / {{ $property->district?->name ?? '—' }}</td>

                <td>
                    <x-ui.badge :variant="$statusVariants[$property->status] ?? 'gray'">{{ ucfirst($property->status) }}</x-ui.badge>
                </td>

                <td class="hidden lg:table-cell text-text-muted">{{ ucfirst($property->availability) }}</td>

                <td class="hidden xl:table-cell text-text-muted">{{ number_format($property->views_count ?? 0) }}</td>

                <td class="hidden xl:table-cell text-text-muted">{{ $property->created_at->format('M j, Y') }}</td>

                <td class="text-right">
                    <x-ui.dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="btn-icon" aria-label="Actions for {{ $property->title }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-ui.dropdown-item :href="route('owner.properties.show', $property)">View Details</x-ui.dropdown-item>

                            @can('update', $property)
                                <x-ui.dropdown-item :href="route('owner.properties.edit', $property)">Edit</x-ui.dropdown-item>
                            @endcan

                            @can('delete', $property)
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('owner.properties.destroy', $property) }}" onsubmit="return confirm('Delete this property?')">
                                    @csrf @method('DELETE')
                                    <x-ui.dropdown-item type="submit" class="text-danger hover:!text-danger">Delete</x-ui.dropdown-item>
                                </form>
                            @endcan
                        </x-slot>
                    </x-ui.dropdown>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
@endif
