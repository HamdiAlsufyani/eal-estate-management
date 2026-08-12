@php
    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
@endphp

<x-ui.card :title="$title">
    @if ($rows->isEmpty())
        <x-ui.empty-state :title="__('analytics.no_data')" />
    @else
        <x-ui.table>
            <x-slot name="head">
                <th>{{ __('inquiries.property') }}</th>
                <th class="hidden md:table-cell">{{ __('properties.owner') }}</th>
                <th>{{ __('analytics.views_count') }}</th>
                <th>{{ __('analytics.favorites_count') }}</th>
                <th>{{ __('analytics.inquiries_count') }}</th>
                <th>{{ __('messages.status') }}</th>
            </x-slot>

            @foreach ($rows as $property)
                <tr>
                    <td>
                        <a href="{{ route('admin.properties.show', $property) }}" class="font-medium text-text hover:text-primary">{{ $property->title }}</a>
                    </td>
                    <td class="hidden md:table-cell text-text-muted">{{ $property->user?->name ?? '—' }}</td>
                    <td>{{ number_format($property->views_count) }}</td>
                    <td>{{ number_format($property->favorites_count) }}</td>
                    <td>{{ number_format($property->inquiries_count) }}</td>
                    <td><x-ui.badge :variant="$statusVariants[$property->status] ?? 'gray'">{{ __('properties.status.' . $property->status) }}</x-ui.badge></td>
                </tr>
            @endforeach
        </x-ui.table>
    @endif
</x-ui.card>
