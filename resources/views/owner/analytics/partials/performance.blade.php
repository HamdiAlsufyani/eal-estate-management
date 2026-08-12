@php
    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
    $availabilityVariants = ['available' => 'success', 'reserved' => 'warning', 'sold' => 'secondary', 'rented' => 'info'];
@endphp

<x-ui.card :title="__('analytics.property_performance')">
    @if ($propertyPerformance->isEmpty())
        <x-ui.empty-state :title="__('properties.no_properties_yet')" :description="__('properties.start_listing_first')" />
    @else
        <x-ui.table>
            <x-slot name="head">
                <th>{{ __('inquiries.property') }}</th>
                <th class="hidden md:table-cell">{{ __('properties.type') }}</th>
                <th class="hidden md:table-cell">{{ __('properties.city') }}</th>
                <th>{{ __('analytics.views_count') }}</th>
                <th>{{ __('analytics.favorites_count') }}</th>
                <th>{{ __('analytics.inquiries_count') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('properties.availability_label') }}</th>
            </x-slot>

            @foreach ($propertyPerformance as $property)
                <tr>
                    <td>
                        <a href="{{ route('owner.properties.show', $property) }}" class="font-medium text-text hover:text-primary">{{ $property->title }}</a>
                    </td>
                    <td class="hidden md:table-cell text-text-muted">{{ $property->propertyType?->name ?? '—' }}</td>
                    <td class="hidden md:table-cell text-text-muted">{{ $property->city?->name ?? '—' }}</td>
                    <td>{{ number_format($property->views_count) }}</td>
                    <td>{{ number_format($property->favorites_count) }}</td>
                    <td>{{ number_format($property->inquiries_count) }}</td>
                    <td><x-ui.badge :variant="$statusVariants[$property->status] ?? 'gray'">{{ __('properties.status.' . $property->status) }}</x-ui.badge></td>
                    <td><x-ui.badge :variant="$availabilityVariants[$property->availability] ?? 'gray'">{{ __('properties.availability.' . $property->availability) }}</x-ui.badge></td>
                </tr>
            @endforeach
        </x-ui.table>
    @endif
</x-ui.card>

@if ($propertyPerformance->hasPages())
    <x-ui.card class="!shadow-none">
        {{ $propertyPerformance->links() }}
    </x-ui.card>
@endif
