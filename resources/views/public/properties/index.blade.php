@php
    $pageTitle = match (true) {
        ($filters['purpose'] ?? null) === 'sale' => __('messages.properties_for_sale'),
        ($filters['purpose'] ?? null) === 'rent' => __('messages.properties_for_rent'),
        default => __('properties.all_properties'),
    };

    $sortOptions = [
        'oldest' => __('properties.oldest'),
        'price_asc' => __('properties.price_low_high'),
        'price_desc' => __('properties.price_high_low'),
        'most_viewed' => __('properties.most_viewed'),
    ];
@endphp

<x-public-layout :title="$pageTitle" meta-description="{{ __('properties.browse_meta_description', ['type' => Str::lower($pageTitle)]) }}">
    <div x-data="{ mobileFiltersOpen: false }">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold tracking-tight text-text sm:text-3xl">{{ $pageTitle }}</h1>
                <p class="mt-1 text-sm text-text-muted">{{ trans_choice('properties.property_count_found', $properties->total(), ['count' => $properties->total()]) }}</p>
            </div>

            <div class="flex flex-col gap-8 lg:flex-row">
                {{-- Backdrop (mobile only) --}}
                <div
                    x-show="mobileFiltersOpen"
                    x-transition.opacity
                    @click="mobileFiltersOpen = false"
                    class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"
                    style="display: none;"
                ></div>

                {{-- Filters: off-canvas drawer on mobile, static sidebar on desktop --}}
                <aside
                    :class="mobileFiltersOpen ? 'translate-x-0' : '-translate-x-full'"
                    class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] overflow-y-auto bg-surface p-5 shadow-soft-xl transition-transform duration-200 lg:static lg:z-auto lg:w-72 lg:shrink-0 lg:translate-x-0 lg:overflow-visible lg:bg-transparent lg:p-0 lg:shadow-none lg:transition-none"
                >
                    <div class="mb-4 flex items-center justify-between lg:hidden">
                        <h2 class="text-base font-semibold text-text">{{ __('messages.filters') }}</h2>
                        <button type="button" class="btn-icon" @click="mobileFiltersOpen = false" aria-label="{{ __('messages.close_filters') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="card p-5 lg:sticky lg:top-20">
                        @include('public.properties.partials.filters')
                    </div>
                </aside>

                {{-- Results --}}
                <div class="min-w-0 flex-1">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <button type="button" class="btn btn-outline lg:hidden" @click="mobileFiltersOpen = true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 0-3 0m3 0a1.5 1.5 0 0 1-3 0m-9.75 0h9.75" />
                            </svg>
                            {{ __('messages.filters') }}
                        </button>

                        <div class="ml-auto flex items-center gap-2">
                            <label for="sort" class="hidden text-sm text-text-muted sm:block">{{ __('properties.sort_by') }}</label>
                            <select name="sort" id="sort" form="properties-filters" onchange="this.form.submit()" class="form-select !w-auto">
                                <option value="" @selected(empty($filters['sort']))>{{ __('properties.newest') }}</option>
                                @foreach ($sortOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['sort'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ($properties->isEmpty())
                        <x-ui.empty-state :title="__('properties.no_properties_found')" :description="__('properties.try_different_filters')">
                            <x-slot name="action">
                                <x-ui.button :href="route('properties.index')" variant="outline">{{ __('messages.clear_filters') }}</x-ui.button>
                            </x-slot>
                        </x-ui.empty-state>
                    @else
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($properties as $property)
                                <x-public.property-card :property="$property" />
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $properties->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
