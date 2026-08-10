@php
    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
@endphp

<x-owner-layout title="Dashboard">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-text-muted">Here's an overview of your properties.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.stat-card label="Total Properties" :value="number_format($stats['total_properties'])" variant="primary">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="Pending" :value="number_format($stats['pending_properties'])" variant="warning">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="Approved" :value="number_format($stats['approved_properties'])" variant="success">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="Rejected" :value="number_format($stats['rejected_properties'])" variant="danger">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75 14.25 14.25M14.25 9.75 9.75 14.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="Available" :value="number_format($stats['available_properties'])" variant="success">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="Sold" :value="number_format($stats['sold_properties'])" variant="secondary">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M4.5 3h6l.75 18h-7.5L4.5 3ZM12.75 21V9.75l3.75-1.5v12.75M16.5 21V6l4.5-1.5v16.5" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="Rented" :value="number_format($stats['rented_properties'])" variant="info">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="Total Views" :value="number_format($stats['total_views'])" variant="primary">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>
        </div>

        <x-ui.card title="Recent Properties">
            <x-slot name="actions">
                @can('create', \App\Models\Property::class)
                    <x-ui.button :href="route('owner.properties.create')" variant="primary" size="sm">Add Property</x-ui.button>
                @endcan
            </x-slot>

            @if ($recentProperties->isEmpty())
                <x-ui.empty-state title="No properties yet." description="Start by listing your first property.">
                    <x-slot name="action">
                        <x-ui.button :href="route('owner.properties.create')" variant="primary">Add Your First Property</x-ui.button>
                    </x-slot>
                </x-ui.empty-state>
            @else
                <x-ui.table>
                    <x-slot name="head">
                        <th class="w-14">Image</th>
                        <th>Title</th>
                        <th class="hidden md:table-cell">Type</th>
                        <th class="hidden md:table-cell">City</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="hidden lg:table-cell">Availability</th>
                        <th class="hidden lg:table-cell">Created</th>
                        <th class="text-right">Actions</th>
                    </x-slot>

                    @foreach ($recentProperties as $property)
                        <tr>
                            <td>
                                @if ($cover = $property->getFirstMediaUrl('property-images'))
                                    <img src="{{ $cover }}" alt="{{ $property->title }}" class="h-10 w-10 rounded-[var(--radius-control)] object-cover" />
                                @else
                                    <span class="flex h-10 w-10 items-center justify-center rounded-[var(--radius-control)] bg-primary/10 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 8.69 9.31a2.25 2.25 0 0 1 3.182 0l5.658 5.658m-3-3 1.19-1.19a2.25 2.25 0 0 1 3.182 0l2.678 2.678M3 8.25V18a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 18V8.25m-18 0V6a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 6v2.25m-18 0h18" />
                                        </svg>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('owner.properties.show', $property) }}" class="font-medium text-text hover:text-primary">{{ $property->title }}</a>
                            </td>
                            <td class="hidden md:table-cell text-text-muted">{{ $property->propertyType?->name ?? '—' }}</td>
                            <td class="hidden md:table-cell text-text-muted">{{ $property->city?->name ?? '—' }}</td>
                            <td class="text-text-muted">{{ number_format($property->price, 0) }}</td>
                            <td><x-ui.badge :variant="$statusVariants[$property->status] ?? 'gray'">{{ ucfirst($property->status) }}</x-ui.badge></td>
                            <td class="hidden lg:table-cell text-text-muted">{{ ucfirst($property->availability) }}</td>
                            <td class="hidden lg:table-cell text-text-muted">{{ $property->created_at->format('M j, Y') }}</td>
                            <td class="text-right">
                                <x-ui.button :href="route('owner.properties.show', $property)" variant="outline" size="sm">View</x-ui.button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>

                <div class="mt-4 text-right">
                    <x-ui.button :href="route('owner.properties.index')" variant="ghost" size="sm">View All Properties</x-ui.button>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-owner-layout>
