@php
    $statusVariants = ['new' => 'info', 'read' => 'gray', 'closed' => 'success'];
@endphp

<x-owner-layout title="{{ __('navigation.inquiries') }}" :breadcrumbs="[['label' => __('navigation.inquiries')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('navigation.inquiries') }}</h1>
            <p class="text-sm text-text-muted">{{ __('inquiries.owner_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card>
            @if ($inquiries->isEmpty())
                <x-ui.empty-state title="{{ __('inquiries.no_inquiries') }}" description="{{ __('inquiries.no_inquiries_hint') }}" />
            @else
                <x-ui.table>
                    <x-slot name="head">
                        <th>{{ __('inquiries.property') }}</th>
                        <th>{{ __('inquiries.from') }}</th>
                        <th class="hidden md:table-cell">{{ __('inquiries.phone') }}</th>
                        <th>{{ __('inquiries.message') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th class="hidden lg:table-cell">{{ __('inquiries.date') }}</th>
                        <th class="text-right">{{ __('messages.actions') }}</th>
                    </x-slot>

                    @foreach ($inquiries as $inquiry)
                        <tr>
                            <td>
                                @if ($inquiry->property)
                                    <a href="{{ route('owner.properties.show', $inquiry->property) }}" class="font-medium text-text hover:text-primary">
                                        {{ $inquiry->property->title }}
                                    </a>
                                @else
                                    <span class="text-text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-text-muted">{{ $inquiry->user?->name ?? '—' }}</td>
                            <td class="hidden md:table-cell text-text-muted">{{ $inquiry->phone }}</td>
                            <td class="max-w-xs truncate text-text-muted">{{ $inquiry->message }}</td>
                            <td>
                                <x-ui.badge :variant="$statusVariants[$inquiry->status] ?? 'gray'">{{ __('inquiries.status.' . $inquiry->status) }}</x-ui.badge>
                            </td>
                            <td class="hidden lg:table-cell text-text-muted">{{ $inquiry->created_at->format('M j, Y') }}</td>
                            <td class="text-right">
                                <x-ui.button :href="route('owner.inquiries.show', $inquiry)" variant="outline" size="sm">{{ __('messages.view') }}</x-ui.button>
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
</x-owner-layout>
