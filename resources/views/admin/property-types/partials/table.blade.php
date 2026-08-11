@if ($propertyTypes->isEmpty())
    <x-ui.empty-state title="{{ __('properties.no_property_types_found') }}" description="{{ __('properties.no_property_types_found_hint') }}">
        <x-slot name="action">
            @can('create', \App\Models\PropertyType::class)
                <x-ui.button :href="route('admin.property-types.create')" variant="primary">{{ __('properties.create_property_type') }}</x-ui.button>
            @endcan
        </x-slot>
    </x-ui.empty-state>
@else
    <x-ui.table>
        <x-slot name="head">
            <th class="w-14">{{ __('messages.id') }}</th>
            <th class="w-14">{{ __('messages.icon') }}</th>
            <th>{{ __('properties.name') }}</th>
            <th class="hidden md:table-cell">{{ __('messages.slug') }}</th>
            <th>{{ __('properties.title') }}</th>
            <th>{{ __('messages.status') }}</th>
            <th class="hidden lg:table-cell">{{ __('properties.created') }}</th>
            <th class="text-right">{{ __('messages.actions') }}</th>
        </x-slot>

        @foreach ($propertyTypes as $propertyType)
            <tr>
                <td class="text-text-muted">{{ $propertyType->id }}</td>

                <td>
                    <span class="flex h-9 w-9 items-center justify-center rounded-[var(--radius-control)] bg-primary/10 text-primary">
                        @if ($propertyType->icon)
                            <span class="text-xs font-medium uppercase">{{ substr($propertyType->icon, 0, 2) }}</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        @endif
                    </span>
                </td>

                <td>
                    <a href="{{ route('admin.property-types.show', $propertyType) }}" class="font-medium text-text hover:text-primary">
                        {{ $propertyType->name }}
                    </a>
                </td>

                <td class="hidden md:table-cell text-text-muted">{{ $propertyType->slug }}</td>

                <td class="text-text-muted">{{ number_format($propertyType->properties_count) }}</td>

                <td>
                    <x-ui.badge :variant="$propertyType->is_active ? 'success' : 'gray'">
                        {{ $propertyType->is_active ? __('messages.active') : __('messages.inactive') }}
                    </x-ui.badge>
                </td>

                <td class="hidden lg:table-cell text-text-muted">{{ $propertyType->created_at->format('M j, Y') }}</td>

                <td class="text-right">
                    <x-ui.dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="btn-icon" aria-label="{{ __('messages.actions_for', ['name' => $propertyType->name]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-ui.dropdown-item :href="route('admin.property-types.show', $propertyType)">{{ __('properties.view_details') }}</x-ui.dropdown-item>

                            @can('update', $propertyType)
                                <x-ui.dropdown-item :href="route('admin.property-types.edit', $propertyType)">{{ __('messages.edit') }}</x-ui.dropdown-item>
                            @endcan

                            @can('delete', $propertyType)
                                <div class="dropdown-divider"></div>
                                <x-ui.dropdown-item
                                    type="button"
                                    class="text-danger hover:!text-danger"
                                    @click="confirmTarget = { url: '{{ route('admin.property-types.destroy', $propertyType) }}', label: @js($propertyType->name) }; $dispatch('open-modal', 'delete-confirm')"
                                >
                                    {{ __('messages.delete') }}
                                </x-ui.dropdown-item>
                            @endcan
                        </x-slot>
                    </x-ui.dropdown>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
@endif
