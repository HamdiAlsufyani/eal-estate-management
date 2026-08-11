@php
    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
    $availabilityVariants = ['available' => 'success', 'reserved' => 'warning', 'sold' => 'gray', 'rented' => 'info'];
    $images = $property->getMedia('property-images');
    $latestRejection = $property->status === 'rejected'
        ? $property->statusHistories()->where('new_status', 'rejected')->latest()->first()
        : null;
@endphp

<x-owner-layout title="{{ $property->title }}" :breadcrumbs="[['label' => __('navigation.my_properties'), 'url' => route('owner.properties.index')], ['label' => $property->title]]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl font-semibold text-text">{{ $property->title }}</h1>
                    <x-ui.badge :variant="$statusVariants[$property->status] ?? 'gray'">{{ __('properties.status.' . $property->status) }}</x-ui.badge>
                    @if ($property->featured)
                        <x-ui.badge variant="secondary">{{ __('properties.featured') }}</x-ui.badge>
                    @endif
                </div>
                <p class="text-sm text-text-muted">{{ __('properties.details_and_status_subtitle') }}</p>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                @can('update', $property)
                    <x-ui.button :href="route('owner.properties.edit', $property)" variant="outline" size="sm">{{ __('messages.edit') }}</x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div x-data="{}" class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="danger" dismissible>{{ session('error') }}</x-ui.alert>
        @endif

        @if ($property->status === 'pending')
            <x-ui.alert variant="warning">{{ __('properties.pending_review_notice') }}</x-ui.alert>
        @elseif ($property->status === 'rejected' && $latestRejection)
            <x-ui.alert variant="danger">
                <p class="font-semibold">{{ __('properties.rejected_notice_title') }}</p>
                <p class="mt-1">{{ __('properties.reason') }}: {{ $latestRejection->reason ?? __('properties.no_reason_provided') }}</p>
                <p class="mt-1 text-xs opacity-80">{{ __('properties.rejected_on', ['date' => $latestRejection->created_at->format('M j, Y g:ia')]) }}</p>
            </x-ui.alert>
        @endif

        {{-- Gallery --}}
        @if ($images->isNotEmpty())
            <x-ui.card title="{{ __('properties.gallery') }}">
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                    @foreach ($images as $index => $media)
                        <a href="{{ $media->getUrl() }}" target="_blank" class="group relative block overflow-hidden rounded-[var(--radius-control)]">
                            <img src="{{ $media->getUrl() }}" alt="{{ $property->title }}" class="h-32 w-full object-cover transition-transform group-hover:scale-105" />
                            @if ($index === 0)
                                <span class="absolute left-1.5 top-1.5 rounded-full bg-primary px-2 py-0.5 text-[10px] font-medium text-white">{{ __('properties.cover') }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </x-ui.card>
        @else
            <x-ui.empty-state title="{{ __('properties.no_images') }}" description="{{ __('properties.no_images_hint') }}" />
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <x-ui.card title="{{ __('properties.property_details') }}">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-text-muted">{{ __('properties.type') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->propertyType?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.purpose_label') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">
                                {{ __('properties.purpose.' . $property->purpose) }}
                                @if ($property->rent_period) ({{ __('properties.rent_period.' . $property->rent_period) }}) @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.price') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ number_format($property->price, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.area') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ number_format($property->area, 2) }} m²</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.city') }} / {{ __('properties.district') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->city?->name ?? '—' }} / {{ $property->district?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.address') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->address }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.bedrooms') }} / {{ __('properties.bathrooms') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->bedrooms }} / {{ $property->bathrooms }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.living_rooms') }} / {{ __('properties.kitchens') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->living_rooms }} / {{ $property->kitchens }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.floor') }} / {{ __('properties.parking_spaces') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->floor ?? '—' }} / {{ $property->parking_spaces }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.furnished') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->furnished ? __('messages.yes') : __('messages.no') }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.availability_label') }}</dt>
                            <dd class="mt-0.5">
                                <x-ui.badge :variant="$availabilityVariants[$property->availability] ?? 'gray'">{{ __('properties.availability.' . $property->availability) }}</x-ui.badge>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.views') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ number_format($property->views_count) }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.created') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->created_at->format('M j, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.last_updated') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->updated_at->format('M j, Y') }}</dd>
                        </div>
                    </dl>

                    @if ($property->description)
                        <div class="mt-6 border-t border-border pt-6">
                            <dt class="text-sm text-text-muted">{{ __('properties.description') }}</dt>
                            <dd class="mt-1 text-sm text-text">{{ $property->description }}</dd>
                        </div>
                    @endif

                    @if ($property->amenities->isNotEmpty())
                        <div class="mt-6 border-t border-border pt-6">
                            <dt class="text-sm text-text-muted">{{ __('properties.amenities') }}</dt>
                            <dd class="mt-2 flex flex-wrap gap-2">
                                @foreach ($property->amenities as $amenity)
                                    <x-ui.badge variant="gray">{{ $amenity->name }}</x-ui.badge>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </x-ui.card>

                {{-- Status History --}}
                <x-ui.card title="{{ __('properties.status_history') }}">
                    @if ($statusHistories->isEmpty())
                        <x-ui.empty-state title="{{ __('properties.no_status_changes') }}" description="{{ __('properties.no_status_changes_hint') }}" />
                    @else
                        <x-ui.table>
                            <x-slot name="head">
                                <th>{{ __('properties.previous_status') }}</th>
                                <th>{{ __('properties.new_status') }}</th>
                                <th>{{ __('properties.reason') }}</th>
                                <th>{{ __('properties.date') }}</th>
                            </x-slot>

                            @foreach ($statusHistories as $history)
                                <tr>
                                    <td>
                                        @if ($history->old_status)
                                            <x-ui.badge :variant="$statusVariants[$history->old_status] ?? 'gray'">{{ __('properties.status.' . $history->old_status) }}</x-ui.badge>
                                        @else
                                            <span class="text-text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <x-ui.badge :variant="$statusVariants[$history->new_status] ?? 'gray'">{{ __('properties.status.' . $history->new_status) }}</x-ui.badge>
                                    </td>
                                    <td class="max-w-xs text-text-muted">{{ $history->reason ?? '—' }}</td>
                                    <td class="text-text-muted">{{ $history->created_at->format('M j, Y g:ia') }}</td>
                                </tr>
                            @endforeach
                        </x-ui.table>

                        @if ($statusHistories->hasPages())
                            <div class="mt-4">
                                {{ $statusHistories->links() }}
                            </div>
                        @endif
                    @endif
                </x-ui.card>
            </div>

            {{-- Actions --}}
            <div class="space-y-6">
                @can('update', $property)
                    <x-ui.card title="{{ __('messages.actions') }}">
                        <div class="flex flex-col gap-3">
                            <x-ui.button :href="route('owner.properties.edit', $property)" variant="outline" class="w-full justify-center">{{ __('properties.edit_property') }}</x-ui.button>

                            @can('delete', $property)
                                <x-ui.button
                                    type="button"
                                    variant="danger"
                                    class="w-full justify-center"
                                    @click="$dispatch('open-modal', 'delete-property')"
                                >
                                    {{ __('properties.delete_property') }}
                                </x-ui.button>
                            @endcan
                        </div>
                    </x-ui.card>
                @endcan
            </div>
        </div>

        @can('delete', $property)
            <x-ui.modal name="delete-property" max-width="md">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-text">{{ __('properties.delete_property') }}</h3>
                    <p class="mt-2 text-sm text-text-muted">
                        {!! __('properties.delete_confirm', ['title' => '<span class="font-medium text-text">' . e($property->title) . '</span>']) !!}
                    </p>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-property')">{{ __('messages.cancel') }}</x-ui.button>
                        <form method="POST" action="{{ route('owner.properties.destroy', $property) }}">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" variant="danger">{{ __('messages.delete') }}</x-ui.button>
                        </form>
                    </div>
                </div>
            </x-ui.modal>
        @endcan
    </div>
</x-owner-layout>
