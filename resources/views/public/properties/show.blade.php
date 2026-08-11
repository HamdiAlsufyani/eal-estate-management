@php
    $images = $property->getMedia('property-images');
    $location = collect([$property->district?->name, $property->city?->name])->filter()->implode(', ');
    $seoTitle = $property->title . ' - ' . ($property->purpose === 'sale' ? __('properties.for_sale') : __('properties.for_rent')) . ($location ? ' ' . __('messages.in') . ' ' . $location : '');
    $seoDescription = Str::limit(strip_tags($property->description), 155);
    $availabilityVariants = ['available' => 'success', 'reserved' => 'warning', 'sold' => 'gray', 'rented' => 'info'];
@endphp

<x-public-layout :title="$seoTitle" :meta-description="$seoDescription" :og-image="$images->first()?->getUrl()">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <nav class="mb-5 flex items-center gap-1.5 text-sm text-text-muted">
            <a href="{{ route('home') }}" class="hover:text-primary">{{ __('navigation.home') }}</a>
            <span>/</span>
            <a href="{{ route('properties.index') }}" class="hover:text-primary">{{ __('properties.title') }}</a>
            <span>/</span>
            <span class="font-medium text-text">{{ Str::limit($property->title, 40) }}</span>
        </nav>

        {{-- Gallery --}}
        <div x-data="{ active: 0 }" class="mb-8">
            @if ($images->isNotEmpty())
                <div class="aspect-[16/9] overflow-hidden rounded-[var(--radius-card)] bg-background shadow-soft">
                    @foreach ($images as $index => $media)
                        <img
                            x-show="active === {{ $index }}"
                            src="{{ $media->getUrl() }}"
                            alt="{{ $property->title }}"
                            class="h-full w-full object-cover"
                            @if ($index > 0) style="display: none;" @endif
                        />
                    @endforeach
                </div>

                @if ($images->count() > 1)
                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                        @foreach ($images as $index => $media)
                            <button
                                type="button"
                                @click="active = {{ $index }}"
                                :class="active === {{ $index }} ? 'ring-2 ring-primary' : 'ring-1 ring-border opacity-80 hover:opacity-100'"
                                class="h-20 w-28 shrink-0 overflow-hidden rounded-[var(--radius-control)] transition"
                            >
                                <img src="{{ $media->getUrl() }}" alt="{{ $property->title }} thumbnail" class="h-full w-full object-cover" />
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="flex aspect-[16/9] items-center justify-center rounded-[var(--radius-card)] bg-background text-border shadow-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="h-24 w-24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045a1.125 1.125 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                {{-- Header --}}
                <div class="card p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge badge-primary">{{ $property->purpose === 'sale' ? __('properties.for_sale') : __('properties.for_rent') }}</span>
                                @if ($property->featured)
                                    <span class="badge bg-secondary text-white">{{ __('properties.featured') }}</span>
                                @endif
                                <x-ui.badge :variant="$availabilityVariants[$property->availability] ?? 'gray'">{{ __('properties.availability.' . $property->availability) }}</x-ui.badge>
                            </div>
                            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-text sm:text-3xl">{{ $property->title }}</h1>
                            @if ($location || $property->address)
                                <p class="mt-1 flex items-center gap-1.5 text-sm text-text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    {{ $property->address }}{{ $location ? ", {$location}" : '' }}
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <x-public.favorite-button :property="$property" :favorited="$isFavorited" size="lg" class="ring-1 ring-border" />
                        </div>
                    </div>

                    <p class="mt-4 text-3xl font-bold text-primary">
                        {{ number_format($property->price, 0) }} {{ __('messages.currency') }}
                        @if ($property->purpose === 'rent' && $property->rent_period)
                            <span class="text-base font-normal text-text-muted">/ {{ __('properties.rent_period.' . $property->rent_period) }}</span>
                        @endif
                    </p>

                    <div class="mt-5 grid grid-cols-2 gap-4 border-t border-border pt-5 sm:grid-cols-4">
                        <div class="text-center">
                            <p class="text-lg font-semibold text-text">{{ $property->bedrooms }}</p>
                            <p class="text-xs text-text-muted">{{ __('properties.bedrooms') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-semibold text-text">{{ $property->bathrooms }}</p>
                            <p class="text-xs text-text-muted">{{ __('properties.bathrooms') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-semibold text-text">{{ number_format($property->area, 0) }}</p>
                            <p class="text-xs text-text-muted">{{ __('properties.area') }} (m²)</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-semibold text-text">{{ number_format($property->views_count) }}</p>
                            <p class="text-xs text-text-muted">{{ __('properties.views') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <x-ui.card :title="__('properties.property_details')">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-text-muted">{{ __('properties.property_type') }}</dt>
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
                    </dl>

                    @if ($property->description)
                        <div class="mt-6 border-t border-border pt-6">
                            <dt class="text-sm text-text-muted">{{ __('properties.description') }}</dt>
                            <dd class="mt-1 whitespace-pre-line text-sm text-text">{{ $property->description }}</dd>
                        </div>
                    @endif

                    @if ($property->amenities->isNotEmpty())
                        <div class="mt-6 border-t border-border pt-6">
                            <dt class="text-sm text-text-muted">{{ __('properties.amenities') }}</dt>
                            <dd class="mt-2 flex flex-wrap gap-2">
                                @foreach ($property->amenities as $amenity)
                                    <span class="badge badge-gray">{{ $amenity->name }}</span>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </x-ui.card>

                {{-- Location --}}
                <x-ui.card :title="__('properties.location')">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-text-muted">{{ __('properties.city') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->city?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-text-muted">{{ __('properties.district') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->district?->name ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-text-muted">{{ __('properties.address') }}</dt>
                            <dd class="mt-0.5 font-medium text-text">{{ $property->address }}</dd>
                        </div>
                    </dl>

                    @if ($property->latitude && $property->longitude)
                        <div class="mt-5 overflow-hidden rounded-[var(--radius-control)] border border-border">
                            <iframe
                                title="{{ __('properties.location_map_title') }}"
                                class="h-72 w-full"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.openstreetmap.org/export/embed.html?bbox={{ $property->longitude - 0.01 }}%2C{{ $property->latitude - 0.01 }}%2C{{ $property->longitude + 0.01 }}%2C{{ $property->latitude + 0.01 }}&layer=mapnik&marker={{ $property->latitude }}%2C{{ $property->longitude }}"
                            ></iframe>
                        </div>
                    @endif
                </x-ui.card>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <x-ui.card :title="__('properties.listed_by')">
                    <div class="flex items-center gap-3">
                        <x-ui.avatar :user="$property->user" size="lg" />
                        <div>
                            <p class="font-semibold text-text">{{ $property->user?->name ?? __('messages.company_name') }}</p>
                            <p class="text-sm text-text-muted">{{ __('properties.owner') }}</p>
                        </div>
                    </div>

                    <div class="mt-5" x-data="{}">
                        @auth
                            <x-ui.button type="button" class="w-full justify-center" @click="$dispatch('open-modal', 'inquiry-modal')">
                                {{ __('properties.interested') }}
                            </x-ui.button>
                        @else
                            <x-ui.button :href="route('login')" class="w-full justify-center">
                                {{ __('properties.interested') }}
                            </x-ui.button>
                            <p class="mt-2 text-center text-xs text-text-muted">{{ __('properties.login_to_contact') }}</p>
                        @endauth
                    </div>
                </x-ui.card>

                <x-ui.card :title="__('properties.property_summary')">
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-text-muted">{{ __('properties.reference') }}</dt>
                            <dd class="font-medium text-text">#{{ $property->id }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-text-muted">{{ __('properties.listed_on') }}</dt>
                            <dd class="font-medium text-text">{{ ($property->published_at ?? $property->created_at)->format('M j, Y') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-text-muted">{{ __('messages.status') }}</dt>
                            <dd><x-ui.badge :variant="$availabilityVariants[$property->availability] ?? 'gray'">{{ __('properties.availability.' . $property->availability) }}</x-ui.badge></dd>
                        </div>
                    </dl>
                </x-ui.card>
            </div>
        </div>
    </div>

    @auth
        <x-ui.modal name="inquiry-modal" max-width="md" :show="$errors->has('phone') || $errors->has('message')">
            <form method="POST" action="{{ route('properties.inquiries.store', $property) }}" class="p-6">
                @csrf

                <h3 class="text-lg font-semibold text-text">{{ __('properties.interested') }}</h3>
                <p class="mt-1 text-sm text-text-muted">
                    {!! __('properties.send_message_to_owner', ['property' => '<span class="font-medium text-text">' . e($property->title) . '</span>']) !!}
                </p>

                <div class="mt-4 space-y-4">
                    <x-ui.input type="tel" name="phone" label="{{ __('inquiries.phone') }}" placeholder="{{ __('inquiries.phone_placeholder') }}" value="{{ old('phone', auth()->user()->phone) }}" required />
                    <x-ui.textarea name="message" label="{{ __('inquiries.your_message') }}" placeholder="{{ __('inquiries.message_placeholder') }}" required>{{ old('message') }}</x-ui.textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'inquiry-modal')">{{ __('messages.cancel') }}</x-ui.button>
                    <x-ui.button type="submit">{{ __('inquiries.submit_inquiry') }}</x-ui.button>
                </div>
            </form>
        </x-ui.modal>
    @endauth
</x-public-layout>
