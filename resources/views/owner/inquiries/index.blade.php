@php
    $statusVariants = ['new' => 'info', 'read' => 'gray', 'closed' => 'success'];
@endphp

<x-owner-layout title="Inquiries" :breadcrumbs="[['label' => 'Inquiries']]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Inquiries</h1>
            <p class="text-sm text-text-muted">Messages from people interested in your properties.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card>
            @if ($inquiries->isEmpty())
                <x-ui.empty-state title="No inquiries yet" description="Inquiries about your properties will appear here." />
            @else
                <x-ui.table>
                    <x-slot name="head">
                        <th>Property</th>
                        <th>From</th>
                        <th class="hidden md:table-cell">Phone</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th class="hidden lg:table-cell">Date</th>
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
                                <x-ui.badge :variant="$statusVariants[$inquiry->status] ?? 'gray'">{{ ucfirst($inquiry->status) }}</x-ui.badge>
                            </td>
                            <td class="hidden lg:table-cell text-text-muted">{{ $inquiry->created_at->format('M j, Y') }}</td>
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
