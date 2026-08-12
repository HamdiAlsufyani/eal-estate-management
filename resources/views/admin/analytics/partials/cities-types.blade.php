<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <x-ui.card :title="__('analytics.cities')">
        @if ($cities->isEmpty())
            <x-ui.empty-state :title="__('analytics.no_data')" />
        @else
            <x-ui.table>
                <x-slot name="head">
                    <th>{{ __('analytics.city') }}</th>
                    <th>{{ __('analytics.properties_count') }}</th>
                    <th>{{ __('analytics.views_count') }}</th>
                    <th>{{ __('analytics.inquiries_count') }}</th>
                </x-slot>

                @foreach ($cities as $row)
                    <tr>
                        <td class="font-medium text-text">{{ $row['city']->name }}</td>
                        <td>{{ number_format($row['properties_count']) }}</td>
                        <td>{{ number_format($row['views_count']) }}</td>
                        <td>{{ number_format($row['inquiries_count']) }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif
    </x-ui.card>

    <x-ui.card :title="__('analytics.property_types')">
        @if ($types->isEmpty())
            <x-ui.empty-state :title="__('analytics.no_data')" />
        @else
            <x-ui.table>
                <x-slot name="head">
                    <th>{{ __('analytics.type') }}</th>
                    <th>{{ __('analytics.properties_count') }}</th>
                </x-slot>

                @foreach ($types as $row)
                    <tr>
                        <td class="font-medium text-text">{{ $row['type']->name }}</td>
                        <td>{{ number_format($row['properties_count']) }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif
    </x-ui.card>
</div>
