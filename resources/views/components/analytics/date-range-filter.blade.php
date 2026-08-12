@props(['action', 'filters' => []])

<form
    method="GET"
    action="{{ $action }}"
    x-data="{ range: '{{ $filters['range'] ?? 'last_30_days' }}' }"
    class="grid grid-cols-1 gap-3 border-b border-border pb-6 sm:grid-cols-2 lg:grid-cols-12 lg:items-end"
>
    <div class="lg:col-span-3">
        <x-ui.select
            name="range"
            label="{{ __('analytics.period_stats') }}"
            :options="[
                'today' => __('analytics.range.today'),
                'yesterday' => __('analytics.range.yesterday'),
                'last_7_days' => __('analytics.range.last_7_days'),
                'last_30_days' => __('analytics.range.last_30_days'),
                'this_month' => __('analytics.range.this_month'),
                'last_month' => __('analytics.range.last_month'),
                'this_year' => __('analytics.range.this_year'),
                'custom' => __('analytics.range.custom'),
            ]"
            :selected="$filters['range'] ?? 'last_30_days'"
            x-model="range"
        />
    </div>

    <div class="lg:col-span-2" x-show="range === 'custom'" x-cloak>
        <x-ui.input type="date" name="from" label="{{ __('analytics.from') }}" :value="$filters['from'] ?? ''" />
    </div>

    <div class="lg:col-span-2" x-show="range === 'custom'" x-cloak>
        <x-ui.input type="date" name="to" label="{{ __('analytics.to') }}" :value="$filters['to'] ?? ''" />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="group_by"
            label="{{ __('analytics.group_by') }}"
            :options="[
                'day' => __('analytics.group_by_day'),
                'week' => __('analytics.group_by_week'),
                'month' => __('analytics.group_by_month'),
            ]"
            :selected="$filters['group_by'] ?? 'day'"
        />
    </div>

    <div class="flex items-center gap-2 lg:col-span-3">
        <x-ui.button type="submit" variant="primary">{{ __('analytics.apply') }}</x-ui.button>

        @if (array_filter($filters))
            <x-ui.button :href="$action" variant="ghost">{{ __('analytics.reset') }}</x-ui.button>
        @endif
    </div>

    @error('to')
        <p class="field-error lg:col-span-12">{{ __('analytics.invalid_date_range') }}</p>
    @enderror
</form>
