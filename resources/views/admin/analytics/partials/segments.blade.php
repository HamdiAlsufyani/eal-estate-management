<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <x-ui.card :title="__('analytics.user_analytics')">
        <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-text-muted">{{ __('analytics.total_admins') }}</span>
                <span class="font-medium text-text">{{ number_format($overview['total_admins']) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-text-muted">{{ __('analytics.total_staff') }}</span>
                <span class="font-medium text-text">{{ number_format($overview['total_staff']) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-text-muted">{{ __('analytics.total_owners') }}</span>
                <span class="font-medium text-text">{{ number_format($overview['total_owners']) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-text-muted">{{ __('analytics.total_customers') }}</span>
                <span class="font-medium text-text">{{ number_format($overview['total_customers']) }}</span>
            </div>

            <div class="my-3 border-t border-border"></div>

            @foreach (['pending' => 'warning', 'active' => 'success', 'rejected' => 'danger'] as $status => $variant)
                <div class="flex items-center justify-between">
                    <x-ui.badge :variant="$variant">{{ __('users.status.' . $status) }}</x-ui.badge>
                    <span class="font-medium text-text">{{ number_format($userStatusBreakdown[$status]) }}</span>
                </div>
            @endforeach
        </div>
    </x-ui.card>

    <x-ui.card :title="__('analytics.inquiry_analytics')">
        <div class="space-y-2 text-sm">
            @foreach (['new' => 'info', 'read' => 'primary', 'closed' => 'gray'] as $status => $variant)
                <div class="flex items-center justify-between">
                    <x-ui.badge :variant="$variant">{{ __('inquiries.status.' . $status) }}</x-ui.badge>
                    <span class="font-medium text-text">{{ number_format($inquiryStatusBreakdown[$status]) }}</span>
                </div>
            @endforeach
        </div>
    </x-ui.card>
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <x-ui.card :title="__('analytics.favorites_by_type')">
        @if ($favoritesByType->isEmpty())
            <x-ui.empty-state :title="__('analytics.no_data')" />
        @else
            <x-ui.table>
                <x-slot name="head">
                    <th>{{ __('analytics.type') }}</th>
                    <th>{{ __('analytics.favorites_count') }}</th>
                </x-slot>

                @foreach ($favoritesByType as $row)
                    <tr>
                        <td class="font-medium text-text">{{ $row['label']->name }}</td>
                        <td>{{ number_format($row['favorites_count']) }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif
    </x-ui.card>

    <x-ui.card :title="__('analytics.favorites_by_city')">
        @if ($favoritesByCity->isEmpty())
            <x-ui.empty-state :title="__('analytics.no_data')" />
        @else
            <x-ui.table>
                <x-slot name="head">
                    <th>{{ __('analytics.city') }}</th>
                    <th>{{ __('analytics.favorites_count') }}</th>
                </x-slot>

                @foreach ($favoritesByCity as $row)
                    <tr>
                        <td class="font-medium text-text">{{ $row['label']->name }}</td>
                        <td>{{ number_format($row['favorites_count']) }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif
    </x-ui.card>
</div>
