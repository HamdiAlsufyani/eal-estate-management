@if ($amenities->isEmpty())
    <x-ui.empty-state title="No amenities found" description="Try adjusting your search or filters, or create a new amenity.">
        <x-slot name="action">
            @can('create', \App\Models\Amenity::class)
                <x-ui.button :href="route('admin.amenities.create')" variant="primary">Create Amenity</x-ui.button>
            @endcan
        </x-slot>
    </x-ui.empty-state>
@else
    <x-ui.table>
        <x-slot name="head">
            <th class="w-14">ID</th>
            <th class="w-14">Icon</th>
            <th>Name</th>
            <th class="hidden md:table-cell">Slug</th>
            <th>Properties</th>
            <th>Status</th>
            <th class="hidden lg:table-cell">Created</th>
            <th class="text-right">Actions</th>
        </x-slot>

        @foreach ($amenities as $amenity)
            <tr>
                <td class="text-text-muted">{{ $amenity->id }}</td>

                <td>
                    <span class="flex h-9 w-9 items-center justify-center rounded-[var(--radius-control)] bg-primary/10 text-primary">
                        @if ($amenity->icon)
                            <span class="text-xs font-medium uppercase">{{ substr($amenity->icon, 0, 2) }}</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        @endif
                    </span>
                </td>

                <td>
                    <a href="{{ route('admin.amenities.show', $amenity) }}" class="font-medium text-text hover:text-primary">
                        {{ $amenity->name }}
                    </a>
                </td>

                <td class="hidden md:table-cell text-text-muted">{{ $amenity->slug }}</td>

                <td class="text-text-muted">{{ number_format($amenity->properties_count) }}</td>

                <td>
                    <x-ui.badge :variant="$amenity->is_active ? 'success' : 'gray'">
                        {{ $amenity->is_active ? 'Active' : 'Inactive' }}
                    </x-ui.badge>
                </td>

                <td class="hidden lg:table-cell text-text-muted">{{ $amenity->created_at->format('M j, Y') }}</td>

                <td class="text-right">
                    <x-ui.dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="btn-icon" aria-label="Actions for {{ $amenity->name }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-ui.dropdown-item :href="route('admin.amenities.show', $amenity)">View Details</x-ui.dropdown-item>

                            @can('update', $amenity)
                                <x-ui.dropdown-item :href="route('admin.amenities.edit', $amenity)">Edit</x-ui.dropdown-item>
                            @endcan

                            @can('delete', $amenity)
                                <div class="dropdown-divider"></div>
                                <x-ui.dropdown-item
                                    type="button"
                                    class="text-danger hover:!text-danger"
                                    @click="confirmTarget = { url: '{{ route('admin.amenities.destroy', $amenity) }}', label: @js($amenity->name) }; $dispatch('open-modal', 'delete-confirm')"
                                >
                                    Delete
                                </x-ui.dropdown-item>
                            @endcan
                        </x-slot>
                    </x-ui.dropdown>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
@endif
