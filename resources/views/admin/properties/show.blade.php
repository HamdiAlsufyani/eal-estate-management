@php
    $statusVariants = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
    $availabilityVariants = ['available' => 'success', 'reserved' => 'warning', 'sold' => 'gray', 'rented' => 'info'];
    $availabilityOptions = $property->purpose === 'sale'
        ? ['available' => __('properties.availability.available'), 'reserved' => __('properties.availability.reserved'), 'sold' => __('properties.availability.sold')]
        : ['available' => __('properties.availability.available'), 'reserved' => __('properties.availability.reserved'), 'rented' => __('properties.availability.rented')];
    $images = $property->getMedia('property-images');
@endphp

<x-admin-layout title="{{ $property->title }}" :breadcrumbs="[['label' => __('properties.title'), 'url' => route('admin.properties.index')], ['label' => $property->title]]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl font-semibold text-text">{{ $property->title }}</h1>
                    <x-ui.badge :variant="$statusVariants[$property->status] ?? 'gray'">{{ __('properties.status.'.$property->status) }}</x-ui.badge>
                    @if ($property->featured)
                        <x-ui.badge variant="secondary">{{ __('properties.featured') }}</x-ui.badge>
                    @endif
                </div>
                <p class="text-sm text-text-muted">{{ __('properties.details_subtitle') }}</p>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                @can('update', $property)
                    <x-ui.button :href="route('admin.properties.edit', $property)" variant="outline" size="sm">{{ __('messages.edit') }}</x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div
        x-data="{}"
        @if ($errors->has('reason')) x-init="$dispatch('open-modal', 'reject-property')" @endif
        class="space-y-6"
    >
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="danger" dismissible>{{ session('error') }}</x-ui.alert>
        @endif

        @if ($errors->any())
            <x-ui.alert variant="danger" dismissible>{{ $errors->first() }}</x-ui.alert>
        @endif

        {{-- Gallery --}}
        @if ($images->isNotEmpty())
            <x-ui.card title="{{ __('properties.gallery') }}">
                <div class="grid grid-cols-2 gap-2 p-2 sm:grid-cols-4">
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
            {{-- Details --}}
            <div class="space-y-6 lg:col-span-2">
                <x-ui.card title="{{ __('properties.property_details') }}">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-text-muted">{{ __('properties.property_type') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->propertyType?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.purpose_label') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">
                                {{ __('properties.purpose.'.$property->purpose) }}
                                @if ($property->rent_period) ({{ __('properties.rent_period.'.$property->rent_period) }}) @endif
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
                            <dt class="text-text-muted">{{ __('properties.city_district') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->city?->name ?? '—' }} / {{ $property->district?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.address') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->address }}</dd>
                        </div>
                        @if ($property->latitude && $property->longitude)
                            <div>
                                <dt class="text-text-muted">{{ __('properties.coordinates') }}</dt>
                                <dd class="mt-0.5 font-medium text-text">{{ $property->latitude }}, {{ $property->longitude }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-text-muted">{{ __('properties.bedrooms_bathrooms') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->bedrooms }} / {{ $property->bathrooms }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.living_rooms_kitchens') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->living_rooms }} / {{ $property->kitchens }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.floor_parking') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->floor ?? '—' }} / {{ $property->parking_spaces }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.furnished') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->furnished ? __('messages.yes') : __('messages.no') }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.availability_label') }}</dt>
                            <dd class="mt-0.5">
                                <x-ui.badge :variant="$availabilityVariants[$property->availability] ?? 'gray'">{{ __('properties.availability.'.$property->availability) }}</x-ui.badge>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.owner') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->user?->name ?? '—' }}</dd>
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
                                <th>{{ __('properties.changed_by') }}</th>
                                <th>{{ __('properties.reason') }}</th>
                                <th>{{ __('inquiries.date') }}</th>
                            </x-slot>

                            @foreach ($statusHistories as $history)
                                <tr>
                                    <td>
                                        @if ($history->old_status)
                                            <x-ui.badge :variant="$statusVariants[$history->old_status] ?? 'gray'">{{ __('properties.status.'.$history->old_status) }}</x-ui.badge>
                                        @else
                                            <span class="text-text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <x-ui.badge :variant="$statusVariants[$history->new_status] ?? 'gray'">{{ __('properties.status.'.$history->new_status) }}</x-ui.badge>
                                    </td>
                                    <td class="text-text-muted">{{ $history->user?->name ?? __('messages.system') }}</td>
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
                @can('changeStatus', $property)
                    <x-ui.card title="{{ __('properties.approval') }}">
                        <div class="flex flex-col gap-3">
                            @if (in_array('approved', $availableTransitions, true))
                                <form method="POST" action="{{ route('admin.properties.status', $property) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved" />
                                    <x-ui.button type="submit" variant="success" class="w-full justify-center">{{ __('messages.approve') }}</x-ui.button>
                                </form>
                            @endif

                            @if (in_array('rejected', $availableTransitions, true))
                                <x-ui.button
                                    type="button"
                                    variant="danger"
                                    class="w-full justify-center"
                                    @click="$dispatch('open-modal', 'reject-property')"
                                >
                                    {{ __('messages.reject') }}
                                </x-ui.button>
                            @endif

                            @if (in_array('pending', $availableTransitions, true))
                                <form method="POST" action="{{ route('admin.properties.status', $property) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="pending" />
                                    <x-ui.button type="submit" variant="outline" class="w-full justify-center">{{ __('properties.move_to_pending') }}</x-ui.button>
                                </form>
                            @endif

                            @if (empty($availableTransitions))
                                <p class="text-sm text-text-muted">{{ __('properties.no_further_status_changes') }}</p>
                            @endif
                        </div>
                    </x-ui.card>
                @endcan

                @can('changeAvailability', $property)
                    @if ($property->status === 'approved')
                        <x-ui.card title="{{ __('properties.availability_label') }}">
                            <form method="POST" action="{{ route('admin.properties.availability', $property) }}" class="space-y-3">
                                @csrf
                                <x-ui.select name="availability" :options="$availabilityOptions" :selected="$property->availability" />
                                <x-ui.button type="submit" variant="outline" class="w-full justify-center">{{ __('properties.update_availability') }}</x-ui.button>
                            </form>
                        </x-ui.card>
                    @endif
                @endcan

                @can('delete', $property)
                    <x-ui.card title="{{ __('properties.danger_zone') }}">
                        <p class="text-sm text-text-muted">{{ __('properties.delete_warning') }}</p>
                        <x-ui.button
                            type="button"
                            variant="danger"
                            class="mt-4 w-full justify-center"
                            @click="$dispatch('open-modal', 'delete-property')"
                        >
                            {{ __('properties.delete_property') }}
                        </x-ui.button>
                    </x-ui.card>
                @endcan
            </div>
        </div>

        @can('changeStatus', $property)
            <x-ui.modal name="reject-property" max-width="md">
                <form method="POST" action="{{ route('admin.properties.status', $property) }}">
                    @csrf
                    <input type="hidden" name="status" value="rejected" />

                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-text">{{ __('properties.reject_property') }}</h3>
                        <p class="mt-2 text-sm text-text-muted">
                            {!! __('properties.reject_reason_prompt', ['title' => '<span class="font-medium text-text">'.e($property->title).'</span>']) !!}
                        </p>

                        <div class="mt-4">
                            <x-ui.textarea
                                name="reason"
                                label="{{ __('properties.reason') }}"
                                placeholder="{{ __('properties.reject_reason_placeholder') }}"
                                required
                            >{{ old('reason') }}</x-ui.textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'reject-property')">{{ __('messages.cancel') }}</x-ui.button>
                            <x-ui.button type="submit" variant="danger">{{ __('properties.reject_property') }}</x-ui.button>
                        </div>
                    </div>
                </form>
            </x-ui.modal>
        @endcan

        @can('delete', $property)
            <x-ui.modal name="delete-property" max-width="md">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-text">{{ __('properties.delete_property') }}</h3>
                    <p class="mt-2 text-sm text-text-muted">
                        {!! __('properties.delete_confirm', ['title' => '<span class="font-medium text-text">'.e($property->title).'</span>']) !!}
                    </p>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-property')">{{ __('messages.cancel') }}</x-ui.button>
                        <form method="POST" action="{{ route('admin.properties.destroy', $property) }}">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" variant="danger">{{ __('messages.delete') }}</x-ui.button>
                        </form>
                    </div>
                </div>
            </x-ui.modal>
        @endcan
    </div>
</x-admin-layout>
