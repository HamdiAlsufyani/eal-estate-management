@if ($districts->isEmpty())
    <x-ui.empty-state title="No districts found" description="Try adjusting your search or filters, or create a new district.">
        <x-slot name="action">
            @can('create', \App\Models\District::class)
                <x-ui.button :href="route('admin.districts.create')" variant="primary">Create District</x-ui.button>
            @endcan
        </x-slot>
    </x-ui.empty-state>
@else
    <x-ui.table>
        <x-slot name="head">
            <th class="w-14">ID</th>
            <th>Name</th>
            <th class="hidden md:table-cell">Slug</th>
            <th>City</th>
            <th>Properties</th>
            <th class="hidden lg:table-cell">Created</th>
            <th class="text-right">Actions</th>
        </x-slot>

        @foreach ($districts as $district)
            <tr>
                <td class="text-text-muted">{{ $district->id }}</td>

                <td>
                    <a href="{{ route('admin.districts.show', $district) }}" class="font-medium text-text hover:text-primary">
                        {{ $district->name }}
                    </a>
                </td>

                <td class="hidden md:table-cell text-text-muted">{{ $district->slug }}</td>

                <td class="text-text-muted">{{ $district->city->name }}</td>

                <td class="text-text-muted">{{ number_format($district->properties_count) }}</td>

                <td class="hidden lg:table-cell text-text-muted">{{ $district->created_at->format('M j, Y') }}</td>

                <td class="text-right">
                    <x-ui.dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="btn-icon" aria-label="Actions for {{ $district->name }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-ui.dropdown-item :href="route('admin.districts.show', $district)">View Details</x-ui.dropdown-item>

                            @can('update', $district)
                                <x-ui.dropdown-item :href="route('admin.districts.edit', $district)">Edit</x-ui.dropdown-item>
                            @endcan

                            @can('delete', $district)
                                <div class="dropdown-divider"></div>
                                <x-ui.dropdown-item
                                    type="button"
                                    class="text-danger hover:!text-danger"
                                    @click="confirmTarget = { url: '{{ route('admin.districts.destroy', $district) }}', label: @js($district->name) }; $dispatch('open-modal', 'delete-confirm')"
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
