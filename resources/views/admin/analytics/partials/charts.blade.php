<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <x-analytics.chart
        :title="__('analytics.chart_views_over_time')"
        type="line"
        :labels="$charts['views_over_time']['labels']"
        :series="$charts['views_over_time']['data']"
    />

    <x-analytics.chart
        :title="__('analytics.chart_new_properties_over_time')"
        type="line"
        :labels="$charts['new_properties_over_time']['labels']"
        :series="$charts['new_properties_over_time']['data']"
    />

    <x-analytics.chart
        :title="__('analytics.chart_new_users_over_time')"
        type="line"
        :labels="$charts['new_users_over_time']['labels']"
        :series="$charts['new_users_over_time']['data']"
    />

    <x-analytics.chart
        :title="__('analytics.chart_inquiries_over_time')"
        type="line"
        :labels="$charts['inquiries_over_time']['labels']"
        :series="$charts['inquiries_over_time']['data']"
    />

    <x-analytics.chart
        :title="__('analytics.chart_favorites_over_time')"
        type="line"
        :labels="$charts['favorites_over_time']['labels']"
        :series="$charts['favorites_over_time']['data']"
    />

    <x-analytics.chart
        :title="__('analytics.chart_sale_vs_rent')"
        type="donut"
        :labels="[__('properties.purpose.sale'), __('properties.purpose.rent')]"
        :series="[$purposeBreakdown['sale'], $purposeBreakdown['rent']]"
    />

    <x-analytics.chart
        :title="__('analytics.chart_properties_by_type')"
        type="bar"
        :labels="$types->map(fn ($row) => $row['type']->name)->all()"
        :series="$types->map(fn ($row) => $row['properties_count'])->all()"
    />

    <x-analytics.chart
        :title="__('analytics.chart_properties_by_city')"
        type="bar"
        :labels="$cities->map(fn ($row) => $row['city']->name)->all()"
        :series="$cities->map(fn ($row) => $row['properties_count'])->all()"
    />
</div>
