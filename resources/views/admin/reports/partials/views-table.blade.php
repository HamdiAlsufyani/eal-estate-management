<x-ui.table>
    <x-slot name="head">
        <th>{{ __('inquiries.property') }}</th>
        <th>{{ __('properties.owner') }}</th>
        <th>{{ __('analytics.views_count') }}</th>
        <th class="hidden md:table-cell">{{ __('properties.city') }}</th>
        <th class="hidden md:table-cell">{{ __('properties.type') }}</th>
        <th>{{ __('analytics.created_date') }}</th>
    </x-slot>

    @forelse ($results as $property)
        <tr>
            <td><a href="{{ route('admin.properties.show', $property) }}" class="font-medium text-text hover:text-primary">{{ $property->title }}</a></td>
            <td class="text-text-muted">{{ $property->user?->name ?? '—' }}</td>
            <td>{{ number_format($property->views_count) }}</td>
            <td class="hidden md:table-cell text-text-muted">{{ $property->city?->name ?? '—' }}</td>
            <td class="hidden md:table-cell text-text-muted">{{ $property->propertyType?->name ?? '—' }}</td>
            <td class="text-text-muted">{{ $property->created_at->format('M j, Y') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6"><x-ui.empty-state :title="__('analytics.no_data')" /></td>
        </tr>
    @endforelse
</x-ui.table>
