<x-ui.table>
    <x-slot name="head">
        <th>{{ __('inquiries.property') }}</th>
        <th>{{ __('properties.owner') }}</th>
        <th class="hidden md:table-cell">{{ __('properties.type') }}</th>
        <th class="hidden md:table-cell">{{ __('properties.purpose_label') }}</th>
        <th class="hidden lg:table-cell">{{ __('properties.city') }}</th>
        <th class="hidden lg:table-cell">{{ __('properties.district') }}</th>
        <th>{{ __('properties.price') }}</th>
        <th>{{ __('messages.status') }}</th>
        <th class="hidden lg:table-cell">{{ __('properties.availability_label') }}</th>
        <th>{{ __('analytics.views_count') }}</th>
        <th>{{ __('analytics.favorites_count') }}</th>
        <th>{{ __('analytics.inquiries_count') }}</th>
        <th>{{ __('analytics.created_date') }}</th>
    </x-slot>

    @forelse ($results as $property)
        <tr>
            <td><a href="{{ route('admin.properties.show', $property) }}" class="font-medium text-text hover:text-primary">{{ $property->title }}</a></td>
            <td class="text-text-muted">{{ $property->user?->name ?? '—' }}</td>
            <td class="hidden md:table-cell text-text-muted">{{ $property->propertyType?->name ?? '—' }}</td>
            <td class="hidden md:table-cell text-text-muted">{{ __('properties.purpose.' . $property->purpose) }}</td>
            <td class="hidden lg:table-cell text-text-muted">{{ $property->city?->name ?? '—' }}</td>
            <td class="hidden lg:table-cell text-text-muted">{{ $property->district?->name ?? '—' }}</td>
            <td class="text-text-muted">{{ number_format($property->price, 0) }}</td>
            <td>
                <x-ui.badge :variant="match ($property->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">
                    {{ __('properties.status.' . $property->status) }}
                </x-ui.badge>
            </td>
            <td class="hidden lg:table-cell text-text-muted">{{ __('properties.availability.' . $property->availability) }}</td>
            <td>{{ number_format($property->views_count) }}</td>
            <td>{{ number_format($property->favorites_count) }}</td>
            <td>{{ number_format($property->inquiries_count) }}</td>
            <td class="text-text-muted">{{ $property->created_at->format('M j, Y') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="13"><x-ui.empty-state :title="__('analytics.no_data')" /></td>
        </tr>
    @endforelse
</x-ui.table>
