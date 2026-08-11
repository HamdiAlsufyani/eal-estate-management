@props(['property'])

@php
    $image = $property->getFirstMediaUrl('property-images');
    $isFavorited = $property->relationLoaded('favorites') && $property->favorites->isNotEmpty();
    $location = collect([$property->district?->name, $property->city?->name])->filter()->implode(', ');
@endphp

<div {{ $attributes->class(['group card overflow-hidden transition-shadow duration-150 hover:shadow-soft-lg']) }}>
    <a href="{{ route('properties.show', $property) }}" class="relative block aspect-[4/3] overflow-hidden bg-background">
        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $property->title }}"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
        @else
            <div class="flex h-full w-full items-center justify-center bg-background text-border">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="h-16 w-16">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045a1.125 1.125 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            </div>
        @endif

        <div class="absolute left-3 top-3 flex flex-wrap items-center gap-1.5">
            <span class="badge bg-white/90 text-primary backdrop-blur">
                {{ $property->purpose === 'sale' ? __('properties.for_sale') : __('properties.for_rent') }}
            </span>

            @if ($property->featured)
                <span class="badge bg-secondary text-white">{{ __('properties.featured') }}</span>
            @endif
        </div>

        <div class="absolute right-3 top-3">
            <x-public.favorite-button :property="$property" :favorited="$isFavorited" />
        </div>
    </a>

    <div class="flex flex-col gap-3 p-4">
        <div>
            <a href="{{ route('properties.show', $property) }}" class="line-clamp-1 text-base font-semibold text-text transition-colors hover:text-primary">
                {{ $property->title }}
            </a>

            @if ($location)
                <p class="mt-1 flex items-center gap-1 text-sm text-text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span class="line-clamp-1">{{ $location }}</span>
                </p>
            @endif
        </div>

        <p class="text-lg font-semibold text-primary">
            {{ number_format($property->price, 0) }} {{ __('messages.currency') }}
            @if ($property->purpose === 'rent' && $property->rent_period)
                <span class="text-sm font-normal text-text-muted">/ {{ __('properties.rent_period_short.' . $property->rent_period) }}</span>
            @endif
        </p>

        <div class="flex items-center justify-between border-t border-border pt-3 text-xs text-text-muted">
            <span>{{ $property->propertyType?->name ?? '—' }}</span>

            <div class="flex items-center gap-3">
                @if ($property->bedrooms)
                    <span class="flex items-center gap-1" title="{{ __('properties.bedrooms') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 18v-8.25a2.25 2.25 0 0 1 2.25-2.25h12a2.25 2.25 0 0 1 2.25 2.25V18M3.75 18h16.5M3.75 18v1.5m16.5-1.5v1.5M6 9.75V6.375c0-.621.504-1.125 1.125-1.125h2.25A1.125 1.125 0 0 1 10.5 6.375V9.75m6 0V6.375c0-.621.504-1.125 1.125-1.125h2.25A1.125 1.125 0 0 1 21 6.375V9.75" />
                        </svg>
                        {{ $property->bedrooms }}
                    </span>
                @endif

                @if ($property->bathrooms)
                    <span class="flex items-center gap-1" title="{{ __('properties.bathrooms') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 12 17.25 16.5 21M3 9.75h18M3.75 9.75V6.75a3 3 0 0 1 3-3h.75m0 0V6a3 3 0 0 0 3 3h3a3 3 0 0 0 3-3v-.75m0 0h.75a3 3 0 0 1 3 3v3M4.5 21h15a1.5 1.5 0 0 0 1.5-1.5v-6.75a1.5 1.5 0 0 0-1.5-1.5h-15a1.5 1.5 0 0 0-1.5 1.5v6.75A1.5 1.5 0 0 0 4.5 21Z" />
                        </svg>
                        {{ $property->bathrooms }}
                    </span>
                @endif

                <span class="flex items-center gap-1" title="{{ __('properties.area') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15m11.25 5.25v-4.5m0 4.5h-4.5m4.5 0L15 15" />
                    </svg>
                    {{ number_format($property->area, 0) }} m²
                </span>
            </div>
        </div>

        <x-ui.button :href="route('properties.show', $property)" variant="outline" size="sm" class="w-full justify-center">
            {{ __('properties.view_details') }}
        </x-ui.button>
    </div>
</div>
