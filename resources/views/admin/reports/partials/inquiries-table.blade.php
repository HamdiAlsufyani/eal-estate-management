<x-ui.table>
    <x-slot name="head">
        <th>{{ __('inquiries.property') }}</th>
        <th>{{ __('inquiries.customer') }}</th>
        <th>{{ __('properties.owner') }}</th>
        <th>{{ __('messages.status') }}</th>
        <th>{{ __('analytics.created_date') }}</th>
        <th>{{ __('analytics.updated_date') }}</th>
    </x-slot>

    @forelse ($results as $inquiry)
        <tr>
            <td class="font-medium text-text">{{ $inquiry->property->title ?? '—' }}</td>
            <td class="text-text-muted">{{ $inquiry->user->name ?? '—' }}</td>
            <td class="text-text-muted">{{ $inquiry->property->user->name ?? '—' }}</td>
            <td>
                <x-ui.badge :variant="match ($inquiry->status) { 'new' => 'info', 'closed' => 'gray', default => 'primary' }">
                    {{ __('inquiries.status.' . $inquiry->status) }}
                </x-ui.badge>
            </td>
            <td class="text-text-muted">{{ $inquiry->created_at->format('M j, Y') }}</td>
            <td class="text-text-muted">{{ $inquiry->updated_at->format('M j, Y') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6"><x-ui.empty-state :title="__('analytics.no_data')" /></td>
        </tr>
    @endforelse
</x-ui.table>
