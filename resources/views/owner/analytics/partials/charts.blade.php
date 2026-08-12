<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <x-analytics.chart
        :title="__('analytics.chart_views_over_time')"
        type="line"
        :labels="$charts['views_over_time']['labels']"
        :series="$charts['views_over_time']['data']"
    />

    <x-analytics.chart
        :title="__('analytics.chart_favorites_over_time')"
        type="line"
        :labels="$charts['favorites_over_time']['labels']"
        :series="$charts['favorites_over_time']['data']"
    />

    <x-analytics.chart
        :title="__('analytics.chart_inquiries_over_time')"
        type="line"
        :labels="$charts['inquiries_over_time']['labels']"
        :series="$charts['inquiries_over_time']['data']"
    />

    <x-analytics.chart
        :title="__('analytics.chart_properties_by_status')"
        type="donut"
        :labels="[__('properties.status.pending'), __('properties.status.approved'), __('properties.status.rejected')]"
        :series="[$statusBreakdown['pending'], $statusBreakdown['approved'], $statusBreakdown['rejected']]"
    />

    <x-analytics.chart
        :title="__('analytics.chart_properties_by_availability')"
        type="donut"
        :labels="[__('properties.availability.available'), __('properties.availability.reserved'), __('properties.availability.sold'), __('properties.availability.rented')]"
        :series="[$availabilityBreakdown['available'], $availabilityBreakdown['reserved'], $availabilityBreakdown['sold'], $availabilityBreakdown['rented']]"
    />
</div>
