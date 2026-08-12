@props([
    'title' => null,
    'type' => 'bar',
    'labels' => [],
    'series' => [],
    'colors' => null,
])

@php
    $hasData = ! empty($series) && array_sum($series) > 0;
@endphp

<x-ui.card :title="$title">
    @if ($hasData)
        <div
            data-chart="{{ json_encode(['type' => $type, 'labels' => $labels, 'series' => $series, 'colors' => $colors]) }}"
            class="w-full"
        ></div>
    @else
        <x-ui.empty-state :title="__('analytics.no_data')" />
    @endif
</x-ui.card>
