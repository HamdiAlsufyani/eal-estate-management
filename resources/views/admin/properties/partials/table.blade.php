@php
    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
@endphp

@if ($properties->isEmpty())
    <x-ui.empty-state title="{{ __('properties.no_properties_found') }}" description="{{ __('messages.try_adjusting_filters') }}">
        @can('create', \App\Models\Property::class)
            <x-slot name="action">
                <x-ui.button :href="route('admin.properties.create')" variant="primary">{{ __('properties.add_property') }}</x-ui.button>
            </x-slot>
        @endcan
    </x-ui.empty-state>
@else
    <x-ui.table>
        <x-slot name="head">
            <th class="w-16">{{ __('properties.image') }}</th>
            <th>{{ __('properties.property_title') }}</th>
            <th class="hidden lg:table-cell">{{ __('properties.owner') }}</th>
            <th class="hidden md:table-cell">{{ __('properties.property_type') }}</th>
            <th class="hidden md:table-cell">{{ __('properties.purpose_label') }}</th>
            <th class="hidden lg:table-cell">{{ __('properties.city_district') }}</th>
            <th>{{ __('properties.price') }}</th>
            <th>{{ __('messages.status') }}</th>
            <th class="hidden lg:table-cell">{{ __('properties.availability_label') }}</th>
            <th class="hidden xl:table-cell">{{ __('properties.featured') }}</th>
            <th class="hidden xl:table-cell">{{ __('properties.created') }}</th>
            <th class="text-right">{{ __('messages.actions') }}</th>
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
                    <a href="{{ route('admin.properties.show', $property) }}" class="font-medium text-text hover:text-primary">
                        {{ $property->title }}
                    </a>
                </td>

                <td class="hidden lg:table-cell text-text-muted">{{ $property->user?->name ?? '—' }}</td>

                <td class="hidden md:table-cell text-text-muted">{{ $property->propertyType?->name ?? '—' }}</td>

                <td class="hidden md:table-cell text-text-muted">{{ __('properties.purpose.'.$property->purpose) }}</td>

                <td class="hidden lg:table-cell text-text-muted">{{ $property->city?->name ?? '—' }} / {{ $property->district?->name ?? '—' }}</td>

                <td class="text-text-muted">{{ number_format($property->price, 0) }}</td>

                <td>
                    <x-ui.badge :variant="$statusVariants[$property->status] ?? 'gray'">{{ __('properties.status.'.$property->status) }}</x-ui.badge>
                </td>

                <td class="hidden lg:table-cell text-text-muted">{{ __('properties.availability.'.$property->availability) }}</td>

                <td class="hidden xl:table-cell">
                    @if ($property->featured)
                        <x-ui.badge variant="secondary">{{ __('properties.featured') }}</x-ui.badge>
                    @else
                        <span class="text-text-muted">—</span>
                    @endif
                </td>

                <td class="hidden xl:table-cell text-text-muted">{{ $property->created_at->format('M j, Y') }}</td>

                <td class="text-right">
                    <x-ui.dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="btn-icon" aria-label="{{ __('messages.actions_for', ['name' => $property->title]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-ui.dropdown-item :href="route('admin.properties.show', $property)">{{ __('properties.view_details') }}</x-ui.dropdown-item>

                            @can('update', $property)
                                <x-ui.dropdown-item :href="route('admin.properties.edit', $property)">{{ __('messages.edit') }}</x-ui.dropdown-item>
                            @endcan

                            @can('delete', $property)
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('admin.properties.destroy', $property) }}" onsubmit="return confirm('{{ __('properties.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <x-ui.dropdown-item type="submit" class="text-danger hover:!text-danger">{{ __('messages.delete') }}</x-ui.dropdown-item>
                                </form>
                            @endcan
                        </x-slot>
                    </x-ui.dropdown>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
@endif
