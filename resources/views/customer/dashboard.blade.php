@php
    $statusVariants = ['new' => 'info', 'read' => 'gray', 'closed' => 'success'];
@endphp

<x-customer-layout title="{{ __('dashboard.customer_dashboard') }}">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}</h1>
            <p class="text-sm text-text-muted">{{ __('dashboard.customer_overview_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <x-ui.stat-card label="{{ __('dashboard.total_favorites') }}" :value="number_format($stats['total_favorites'])" variant="primary">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.active_inquiries') }}" :value="number_format($stats['active_inquiries'])" variant="warning">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.completed_inquiries') }}" :value="number_format($stats['completed_inquiries'])" variant="success">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.recently_viewed') }}" :value="number_format($stats['recently_viewed'])" variant="secondary">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.unread_notifications') }}" :value="number_format($stats['unread_notifications'])" variant="info">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>
        </div>

        <x-ui.card title="{{ __('dashboard.recent_favorites') }}">
            <x-slot name="actions">
                <x-ui.button :href="route('favorites.index')" variant="ghost" size="sm">{{ __('customer.view_all_favorites') }}</x-ui.button>
            </x-slot>

            @if ($recentFavorites->isEmpty())
                <x-ui.empty-state title="{{ __('customer.no_favorites') }}" description="{{ __('favorites.no_favorites_hint') }}">
                    <x-slot name="action">
                        <x-ui.button :href="route('properties.index')" variant="primary">{{ __('properties.browse_properties') }}</x-ui.button>
                    </x-slot>
                </x-ui.empty-state>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($recentFavorites as $favorite)
                        @php $property = $favorite->property; @endphp

                        @if ($property && $property->status === 'approved' && ! $property->trashed())
                            <x-public.property-card :property="$property" />
                        @else
                            <div class="card flex flex-col gap-3 p-4">
                                <p class="line-clamp-1 font-semibold text-text">{{ $property?->title ?? __('favorites.property_unavailable') }}</p>
                                <span class="badge badge-gray w-fit">
                                    {{ $property?->trashed() ? __('favorites.property_removed') : __('favorites.currently_unavailable') }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        <x-ui.card title="{{ __('dashboard.recent_inquiries') }}">
            <x-slot name="actions">
                <x-ui.button :href="route('customer.inquiries.index')" variant="ghost" size="sm">{{ __('properties.view_all') }}</x-ui.button>
            </x-slot>

            @if ($recentInquiries->isEmpty())
                <x-ui.empty-state title="{{ __('customer.no_inquiries') }}" description="{{ __('inquiries.no_inquiries_hint') }}">
                    <x-slot name="action">
                        <x-ui.button :href="route('properties.index')" variant="primary">{{ __('properties.browse_properties') }}</x-ui.button>
                    </x-slot>
                </x-ui.empty-state>
            @else
                <x-ui.table>
                    <x-slot name="head">
                        <th class="w-14">{{ __('properties.image') }}</th>
                        <th>{{ __('inquiries.property') }}</th>
                        <th class="hidden md:table-cell">{{ __('properties.owner') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th class="hidden lg:table-cell">{{ __('inquiries.date') }}</th>
                        <th class="text-right">{{ __('messages.actions') }}</th>
                    </x-slot>

                    @foreach ($recentInquiries as $inquiry)
                        <tr>
                            <td>
                                @if ($inquiry->property && $cover = $inquiry->property->getFirstMediaUrl('property-images'))
                                    <img src="{{ $cover }}" alt="{{ $inquiry->property->title }}" class="h-10 w-10 rounded-[var(--radius-control)] object-cover" />
                                @else
                                    <span class="flex h-10 w-10 items-center justify-center rounded-[var(--radius-control)] bg-primary/10 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 8.69 9.31a2.25 2.25 0 0 1 3.182 0l5.658 5.658m-3-3 1.19-1.19a2.25 2.25 0 0 1 3.182 0l2.678 2.678M3 8.25V18a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 18V8.25m-18 0V6a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 6v2.25m-18 0h18" />
                                        </svg>
                                    </span>
                                @endif
                            </td>
                            <td class="font-medium text-text">{{ $inquiry->property?->title ?? __('inquiries.property_unavailable') }}</td>
                            <td class="hidden md:table-cell text-text-muted">{{ $inquiry->property?->user?->name ?? '—' }}</td>
                            <td><x-ui.badge :variant="$statusVariants[$inquiry->status] ?? 'gray'">{{ __('inquiries.status.' . $inquiry->status) }}</x-ui.badge></td>
                            <td class="hidden lg:table-cell text-text-muted">{{ $inquiry->created_at->format('M j, Y') }}</td>
                            <td class="text-right">
                                <x-ui.button :href="route('customer.inquiries.show', $inquiry)" variant="outline" size="sm">{{ __('messages.view') }}</x-ui.button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.card>

        <x-ui.card title="{{ __('messages.notifications') }}">
            <x-slot name="actions">
                <x-ui.button :href="route('notifications.index')" variant="ghost" size="sm">{{ __('customer.view_all_notifications') }}</x-ui.button>
            </x-slot>

            @if ($latestNotifications->isEmpty())
                <x-ui.empty-state title="{{ __('customer.no_notifications') }}" />
            @else
                <div class="-mx-6 -my-6 divide-y divide-border">
                    @foreach ($latestNotifications as $notification)
                        <x-notifications.item :notification="$notification" />
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
</x-customer-layout>
