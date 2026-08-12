@php
    $statusVariants = ['new' => 'info', 'read' => 'gray', 'closed' => 'success'];
@endphp

<x-customer-layout title="{{ __('inquiries.my_inquiries') }}" :breadcrumbs="[['label' => __('inquiries.my_inquiries')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('inquiries.my_inquiries') }}</h1>
            <p class="text-sm text-text-muted">{{ __('customer.my_inquiries_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card>
            @if ($inquiries->isEmpty())
                <x-ui.empty-state title="{{ __('customer.no_inquiries') }}" description="{{ __('inquiries.no_inquiries_hint') }}">
                    <x-slot name="action">
                        <x-ui.button :href="route('properties.index')" variant="primary">{{ __('properties.browse_properties') }}</x-ui.button>
                    </x-slot>
                </x-ui.empty-state>
            @else
                <x-ui.table>
                    <x-slot name="head">
                        <th>{{ __('inquiries.property') }}</th>
                        <th>{{ __('properties.owner') }}</th>
                        <th class="hidden md:table-cell">{{ __('inquiries.message') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th class="hidden lg:table-cell">{{ __('inquiries.date') }}</th>
                        <th class="text-right">{{ __('messages.actions') }}</th>
                    </x-slot>

                    @foreach ($inquiries as $inquiry)
                        <tr>
                            <td>
                                @if ($inquiry->property)
                                    <span class="font-medium text-text">{{ $inquiry->property->title }}</span>
                                @else
                                    <span class="text-text-muted">{{ __('inquiries.property_unavailable') }}</span>
                                @endif
                            </td>
                            <td class="text-text-muted">{{ $inquiry->property?->user?->name ?? '—' }}</td>
                            <td class="hidden md:table-cell max-w-xs truncate text-text-muted">{{ $inquiry->message }}</td>
                            <td>
                                <x-ui.badge :variant="$statusVariants[$inquiry->status] ?? 'gray'">{{ __('inquiries.status.' . $inquiry->status) }}</x-ui.badge>
                            </td>
                            <td class="hidden lg:table-cell text-text-muted">{{ $inquiry->created_at->format('M j, Y') }}</td>
                            <td class="text-right">
                                <x-ui.button :href="route('customer.inquiries.show', $inquiry)" variant="outline" size="sm">{{ __('messages.view') }}</x-ui.button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.card>

        @if ($inquiries->hasPages())
            <x-ui.card class="!shadow-none">
                {{ $inquiries->links() }}
            </x-ui.card>
        @endif
    </div>
</x-customer-layout>
