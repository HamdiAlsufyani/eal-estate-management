<x-ui.table>
    <x-slot name="head">
        <th>{{ __('users.user') }}</th>
        <th>{{ __('users.role') }}</th>
        <th>{{ __('messages.status') }}</th>
        <th>{{ __('analytics.properties_count') }}</th>
        <th>{{ __('analytics.inquiries_count') }}</th>
        <th>{{ __('analytics.favorites_count') }}</th>
        <th>{{ __('analytics.registration_date') }}</th>
    </x-slot>

    @forelse ($results as $user)
        <tr>
            <td>
                <p class="font-medium text-text">{{ $user->name }}</p>
                <p class="text-xs text-text-muted">{{ $user->email }}</p>
            </td>
            <td class="text-text-muted">{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
            <td>
                <x-ui.badge :variant="match ($user->status) { 'active' => 'success', 'rejected' => 'danger', default => 'warning' }">
                    {{ __('users.status.' . $user->status) }}
                </x-ui.badge>
            </td>
            <td>{{ number_format($user->properties_count) }}</td>
            <td>{{ number_format($user->inquiries_count) }}</td>
            <td>{{ number_format($user->favorites_count) }}</td>
            <td class="text-text-muted">{{ $user->created_at->format('M j, Y') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7"><x-ui.empty-state :title="__('analytics.no_data')" /></td>
        </tr>
    @endforelse
</x-ui.table>
