<x-ui.card :title="__('analytics.property_analytics')">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div>
            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-text-muted">{{ __('messages.status') }}</h3>
            <div class="space-y-2">
                @foreach (['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'] as $status => $variant)
                    <div class="flex items-center justify-between text-sm">
                        <x-ui.badge :variant="$variant">{{ __('properties.status.' . $status) }}</x-ui.badge>
                        <span class="font-medium text-text">{{ number_format($statusBreakdown[$status]) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-text-muted">{{ __('properties.availability_label') }}</h3>
            <div class="space-y-2">
                @foreach (['available' => 'success', 'reserved' => 'warning', 'sold' => 'secondary', 'rented' => 'info'] as $availability => $variant)
                    <div class="flex items-center justify-between text-sm">
                        <x-ui.badge :variant="$variant">{{ __('properties.availability.' . $availability) }}</x-ui.badge>
                        <span class="font-medium text-text">{{ number_format($availabilityBreakdown[$availability]) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-text-muted">{{ __('analytics.property_purpose') }}</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-text-muted">{{ __('properties.purpose.sale') }}</span>
                    <span class="font-medium text-text">{{ number_format($purposeBreakdown['sale']) }} ({{ $purposePercentages['sale'] }}%)</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-border">
                    <div class="h-full bg-primary" style="width: {{ $purposePercentages['sale'] }}%"></div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-text-muted">{{ __('properties.purpose.rent') }}</span>
                    <span class="font-medium text-text">{{ number_format($purposeBreakdown['rent']) }} ({{ $purposePercentages['rent'] }}%)</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-border">
                    <div class="h-full bg-secondary" style="width: {{ $purposePercentages['rent'] }}%"></div>
                </div>
            </div>
        </div>
    </div>
</x-ui.card>
