<x-public-layout
    :title="__('properties.find_your_perfect_property')"
    :meta-description="__('properties.home_meta_description')"
>
    {{-- Hero + Search --}}
    <section class="relative overflow-hidden bg-primary">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px); background-size: 28px 28px;"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-5xl">{{ __('properties.find_your_perfect_property') }}</h1>
                <p class="mt-4 text-base text-white/80 sm:text-lg">{{ __('messages.footer_tagline') }}</p>
            </div>

            <form method="GET" action="{{ route('properties.index') }}" class="mx-auto mt-10 max-w-4xl rounded-[var(--radius-card)] bg-surface p-4 shadow-soft-xl sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <x-ui.select name="purpose" label="{{ __('properties.purpose_label') }}" :options="['sale' => __('properties.for_sale'), 'rent' => __('properties.for_rent')]" placeholder="{{ __('messages.any') }}" />

                    <x-ui.select
                        name="property_type"
                        label="{{ __('properties.property_type') }}"
                        :options="\App\Models\PropertyType::active()->orderBy('name_en')->get()->pluck('name', 'id')"
                        placeholder="{{ __('properties.all_types') }}"
                    />

                    <x-ui.select
                        name="city"
                        label="{{ __('properties.city') }}"
                        :options="\App\Models\City::active()->orderBy('name_en')->get()->pluck('name', 'id')"
                        placeholder="{{ __('properties.all_cities') }}"
                    />

                    <x-ui.select
                        name="price_to"
                        label="{{ __('properties.max_price') }}"
                        :options="collect([500000, 1000000, 2000000, 5000000])->mapWithKeys(fn ($amount) => [
                            $amount => __('properties.up_to') . ' ' . number_format($amount) . ' ' . __('messages.currency'),
                        ])"
                        placeholder="{{ __('properties.any_price') }}"
                    />

                    <div class="flex items-end">
                        <x-ui.button type="submit" class="w-full justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            {{ __('messages.search') }}
                        </x-ui.button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- Featured Properties --}}
    @if ($featuredProperties->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight text-text sm:text-3xl">{{ __('properties.featured_properties') }}</h2>
                    <p class="mt-1 text-sm text-text-muted">{{ __('properties.featured_subtitle') }}</p>
                </div>
                <x-ui.button :href="route('properties.index')" variant="outline" size="sm" class="hidden sm:inline-flex">{{ __('properties.view_all') }}</x-ui.button>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredProperties as $property)
                    <x-public.property-card :property="$property" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Latest Properties --}}
    @if ($latestProperties->isNotEmpty())
        <section class="bg-surface py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold tracking-tight text-text sm:text-3xl">{{ __('properties.latest_properties') }}</h2>
                        <p class="mt-1 text-sm text-text-muted">{{ __('properties.latest_subtitle') }}</p>
                    </div>
                    <x-ui.button :href="route('properties.index')" variant="outline" size="sm" class="hidden sm:inline-flex">{{ __('properties.view_all') }}</x-ui.button>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($latestProperties as $property)
                        <x-public.property-card :property="$property" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Property Types --}}
    @if ($propertyTypes->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-2xl font-semibold tracking-tight text-text sm:text-3xl">{{ __('properties.browse_by_type') }}</h2>
                <p class="mt-1 text-sm text-text-muted">{{ __('properties.browse_by_type_subtitle') }}</p>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($propertyTypes as $type)
                    <a
                        href="{{ route('properties.index', ['property_type' => $type->id]) }}"
                        class="card flex flex-col items-center gap-3 px-4 py-6 text-center transition-shadow duration-150 hover:shadow-soft-lg"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                            @if ($type->icon)
                                <span class="text-xl">{{ $type->icon }}</span>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045a1.125 1.125 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-text">{{ $type->name }}</p>
                            <p class="text-xs text-text-muted">{{ trans_choice('properties.property_count', $type->properties_count, ['count' => $type->properties_count]) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Popular Cities --}}
    @if ($cities->isNotEmpty())
        <section class="bg-surface py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-2xl font-semibold tracking-tight text-text sm:text-3xl">{{ __('properties.popular_cities') }}</h2>
                    <p class="mt-1 text-sm text-text-muted">{{ __('properties.popular_cities_subtitle') }}</p>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($cities as $city)
                        <a
                            href="{{ route('properties.index', ['city' => $city->id]) }}"
                            class="card flex flex-col items-center gap-2 px-4 py-8 text-center transition-shadow duration-150 hover:shadow-soft-lg"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 text-secondary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <p class="text-sm font-semibold text-text">{{ $city->name }}</p>
                            <p class="text-xs text-text-muted">{{ trans_choice('properties.property_count', $city->properties_count, ['count' => $city->properties_count]) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Why Choose Us --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-2xl font-semibold tracking-tight text-text sm:text-3xl">{{ __('properties.why_choose_us') }}</h2>
            <p class="mt-1 text-sm text-text-muted">{{ __('properties.why_choose_us_subtitle') }}</p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ([
                ['title' => __('properties.verified_listings'), 'desc' => __('properties.verified_listings_desc'), 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                ['title' => __('properties.trusted_owners'), 'desc' => __('properties.trusted_owners_desc'), 'icon' => 'M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.964 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
                ['title' => __('properties.simple_search'), 'desc' => __('properties.simple_search_desc'), 'icon' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z'],
            ] as $item)
                <div class="card p-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-text">{{ $item['title'] }}</h3>
                    <p class="mt-1 text-sm text-text-muted">{{ $item['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-secondary">
        <div class="mx-auto max-w-4xl px-4 py-14 text-center sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-white sm:text-3xl">{{ __('properties.ready_to_find') }}</h2>
            <p class="mt-2 text-white/90">{{ __('properties.browse_hundreds') }}</p>
            <div class="mt-6">
                <x-ui.button :href="route('properties.index')" class="!bg-primary hover:!bg-primary-hover">
                    {{ __('properties.browse_properties') }}
                </x-ui.button>
            </div>
        </div>
    </section>
</x-public-layout>
