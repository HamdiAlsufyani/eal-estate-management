@php
    $statusVariants = ['new' => 'info', 'read' => 'gray', 'closed' => 'success'];
@endphp

@if ($inquiries->isEmpty())
    <x-ui.empty-state title="{{ __('inquiries.no_inquiries_found') }}" description="{{ __('messages.try_adjusting_filters') }}" />
@else
    <x-ui.table>
        <x-slot name="head">
            <th>{{ __('inquiries.customer') }}</th>
            <th>{{ __('inquiries.property') }}</th>
            <th class="hidden lg:table-cell">{{ __('properties.owner') }}</th>
            <th class="hidden md:table-cell">{{ __('inquiries.phone') }}</th>
            <th class="hidden xl:table-cell">{{ __('inquiries.message') }}</th>
            <th>{{ __('messages.status') }}</th>
            <th class="hidden lg:table-cell">{{ __('inquiries.date') }}</th>
            <th class="text-right">{{ __('messages.actions') }}</th>
        </x-slot>

        @foreach ($inquiries as $inquiry)
            <tr>
                <td>
                    <span class="block font-medium text-text">{{ $inquiry->user?->name ?? '—' }}</span>
                    <span class="block truncate text-xs text-text-muted">{{ $inquiry->user?->email }}</span>
                </td>
                <td>
                    @if ($inquiry->property)
                        <a href="{{ route('admin.properties.show', $inquiry->property) }}" class="font-medium text-text hover:text-primary">
                            {{ $inquiry->property->title }}
                        </a>
                    @else
                        <span class="text-text-muted">—</span>
                    @endif
                </td>
                <td class="hidden lg:table-cell text-text-muted">{{ $inquiry->property?->user?->name ?? '—' }}</td>
                <td class="hidden md:table-cell text-text-muted">{{ $inquiry->phone }}</td>
                <td class="hidden xl:table-cell max-w-xs truncate text-text-muted">{{ $inquiry->message }}</td>
                <td>
                    <x-ui.badge :variant="$statusVariants[$inquiry->status] ?? 'gray'">{{ __('inquiries.status.'.$inquiry->status) }}</x-ui.badge>
                </td>
                <td class="hidden lg:table-cell text-text-muted">{{ $inquiry->created_at->format('M j, Y') }}</td>
                <td class="text-right">
                    <x-ui.button :href="route('admin.inquiries.show', $inquiry)" variant="outline" size="sm">{{ __('messages.view') }}</x-ui.button>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
@endif
