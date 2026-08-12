@if ($districts->isEmpty())
    <x-ui.empty-state title="{{ __('properties.no_districts_found') }}" description="{{ __('properties.no_districts_found_hint') }}">
        <x-slot name="action">
            @can('create', \App\Models\District::class)
                <x-ui.button :href="route('admin.districts.create')" variant="primary">{{ __('properties.create_district') }}</x-ui.button>
            @endcan
        </x-slot>
    </x-ui.empty-state>
@else
    <x-ui.table>
        <x-slot name="head">
            <th class="w-14">{{ __('messages.id') }}</th>
            <th>{{ __('properties.name') }}</th>
            <th class="hidden md:table-cell">{{ __('messages.slug') }}</th>
            <th>{{ __('properties.city') }}</th>
            <th>{{ __('properties.title') }}</th>
            <th class="hidden lg:table-cell">{{ __('properties.created') }}</th>
            <th class="text-right">{{ __('messages.actions') }}</th>
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
                            <button type="button" class="btn-icon" aria-label="{{ __('messages.actions_for', ['name' => $district->name]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-ui.dropdown-item :href="route('admin.districts.show', $district)">{{ __('properties.view_details') }}</x-ui.dropdown-item>

                            @can('update', $district)
                                <x-ui.dropdown-item :href="route('admin.districts.edit', $district)">{{ __('messages.edit') }}</x-ui.dropdown-item>
                            @endcan

                            @can('delete', $district)
                                <div class="dropdown-divider"></div>
                                <x-ui.dropdown-item
                                    type="button"
                                    class="text-danger hover:!text-danger"
                                    @click="confirmTarget = { url: '{{ route('admin.districts.destroy', $district) }}', label: @js($district->name) }; $dispatch('open-modal', 'delete-confirm')"
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
