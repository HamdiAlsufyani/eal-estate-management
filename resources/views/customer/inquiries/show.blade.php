@php
    $statusVariants = ['new' => 'info', 'read' => 'gray', 'closed' => 'success'];
    $property = $inquiry->property;
@endphp

<x-customer-layout title="{{ __('inquiries.inquiry_details') }}" :breadcrumbs="[['label' => __('inquiries.my_inquiries'), 'url' => route('customer.inquiries.index')], ['label' => '#' . $inquiry->id]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('inquiries.inquiry') }} #{{ $inquiry->id }}</h1>
            <p class="text-sm text-text-muted">{{ __('inquiries.submitted_on', ['date' => $inquiry->created_at->format('M j, Y \a\t g:ia')]) }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <x-ui.card title="{{ __('inquiries.property') }}">
                    @if ($property)
                        <div class="flex gap-4">
                            @if ($cover = $property->getFirstMediaUrl('property-images'))
                                <img src="{{ $cover }}" alt="{{ $property->title }}" class="h-20 w-20 shrink-0 rounded-[var(--radius-control)] object-cover" />
                            @else
                                <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-[var(--radius-control)] bg-primary/10 text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 8.69 9.31a2.25 2.25 0 0 1 3.182 0l5.658 5.658m-3-3 1.19-1.19a2.25 2.25 0 0 1 3.182 0l2.678 2.678M3 8.25V18a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 18V8.25m-18 0V6a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 6v2.25m-18 0h18" />
                                    </svg>
                                </span>
                            @endif

                            <div class="min-w-0">
                                <a href="{{ route('properties.show', $property) }}" class="font-medium text-primary hover:underline">
                                    {{ $property->title }}
                                </a>
                                <p class="mt-1 text-sm font-semibold text-text">{{ number_format($property->price, 0) }} {{ __('messages.currency') }}</p>
                                <p class="mt-1 text-sm text-text-muted">
                                    {{ collect([$property->district?->name, $property->city?->name])->filter()->implode(', ') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-text-muted">{{ __('inquiries.property_unavailable') }}</p>
                    @endif
                </x-ui.card>

                <x-ui.card title="{{ __('properties.owner') }}">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-text-muted">{{ __('properties.name') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property?->user?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('inquiries.phone') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $inquiry->phone }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 border-t border-border pt-6">
                        <dt class="text-sm text-text-muted">{{ __('inquiries.message') }}</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-text">{{ $inquiry->message }}</dd>
                    </div>
                </x-ui.card>
            </div>

            <div class="space-y-6">
                <x-ui.card title="{{ __('messages.status') }}">
                    <x-ui.badge :variant="$statusVariants[$inquiry->status] ?? 'gray'">{{ __('inquiries.status.' . $inquiry->status) }}</x-ui.badge>

                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-text-muted">{{ __('inquiries.date') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $inquiry->created_at->format('M j, Y \a\t g:ia') }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.last_updated') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $inquiry->updated_at->format('M j, Y \a\t g:ia') }}</dd>
                        </div>
                    </dl>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-customer-layout>
