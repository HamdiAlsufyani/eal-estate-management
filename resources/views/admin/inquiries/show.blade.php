@php
    $statusVariants = ['new' => 'info', 'read' => 'gray', 'closed' => 'success'];
@endphp

<x-admin-layout title="{{ __('inquiries.inquiry_details') }}" :breadcrumbs="[['label' => __('inquiries.title'), 'url' => route('admin.inquiries.index')], ['label' => '#' . $inquiry->id]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('inquiries.inquiry') }} #{{ $inquiry->id }}</h1>
            <p class="text-sm text-text-muted">{{ __('inquiries.submitted_on', ['date' => $inquiry->created_at->format('M j, Y \a\t g:ia')]) }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <x-ui.card title="{{ __('inquiries.property') }}">
                    @if ($inquiry->property)
                        <a href="{{ route('admin.properties.show', $inquiry->property) }}" class="font-medium text-primary hover:underline">
                            {{ $inquiry->property->title }}
                        </a>
                        <p class="mt-1 text-sm text-text-muted">{{ __('properties.owner') }}: {{ $inquiry->property->user?->name ?? '—' }}</p>
                    @else
                        <p class="text-sm text-text-muted">{{ __('inquiries.property_unavailable') }}</p>
                    @endif
                </x-ui.card>

                <x-ui.card title="{{ __('inquiries.customer') }}">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-text-muted">{{ __('users.name') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $inquiry->user?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('users.email') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $inquiry->user?->email ?? '—' }}</dd>
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
                    <x-ui.badge :variant="$statusVariants[$inquiry->status] ?? 'gray'">{{ __('inquiries.status.'.$inquiry->status) }}</x-ui.badge>

                    @can('update', $inquiry)
                        <form method="POST" action="{{ route('admin.inquiries.status', $inquiry) }}" class="mt-4 space-y-3">
                            @csrf
                            @method('PATCH')
                            <x-ui.select
                                name="status"
                                :options="['new' => __('inquiries.status.new'), 'read' => __('inquiries.status.read'), 'closed' => __('inquiries.status.closed')]"
                                :selected="$inquiry->status"
                            />
                            <x-ui.button type="submit" variant="outline" class="w-full justify-center">{{ __('inquiries.update_status') }}</x-ui.button>
                        </form>
                    @endcan
                </x-ui.card>
            </div>
        </div>
    </div>
</x-admin-layout>
