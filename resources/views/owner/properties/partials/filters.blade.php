<form
    method="GET"
    action="{{ route('owner.properties.index') }}"
    x-data="{
        loading: false,
        cityId: @js($filters['city'] ?? ''),
        districts: @js($districts->map(fn ($name, $id) => ['id' => (string) $id, 'name' => $name])->values()),
        districtId: @js((string) ($filters['district'] ?? '')),
        loadingDistricts: false,
        async loadDistricts() {
            if (! this.cityId) {
                this.districts = [];
                return;
            }

            this.loadingDistricts = true;

            try {
                const response = await fetch(`{{ url('admin/cities') }}/${this.cityId}/districts`, {
                    headers: { 'Accept': 'application/json' },
                });
                this.districts = await response.json();
            } finally {
                this.loadingDistricts = false;
            }
        },
    }"
    @submit="loading = true"
    class="mb-6 grid grid-cols-1 gap-3 border-b border-border pb-6 sm:grid-cols-2 lg:grid-cols-12 lg:items-end"
>
    <div class="sm:col-span-2 lg:col-span-4">
        <x-ui.input
            name="search"
            label="{{ __('messages.search') }}"
            placeholder="{{ __('properties.search_placeholder') }}"
            :value="$filters['search'] ?? ''"
        />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="purpose"
            label="{{ __('properties.purpose_label') }}"
            placeholder="{{ __('messages.all') }}"
            :options="['sale' => __('properties.purpose.sale'), 'rent' => __('properties.purpose.rent')]"
            :selected="$filters['purpose'] ?? null"
        />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="status"
            label="{{ __('messages.status') }}"
            placeholder="{{ __('messages.all') }}"
            :options="['pending' => __('properties.status.pending'), 'approved' => __('properties.status.approved'), 'rejected' => __('properties.status.rejected')]"
            :selected="$filters['status'] ?? null"
        />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="availability"
            label="{{ __('properties.availability_label') }}"
            placeholder="{{ __('messages.all') }}"
            :options="['available' => __('properties.availability.available'), 'reserved' => __('properties.availability.reserved'), 'sold' => __('properties.availability.sold'), 'rented' => __('properties.availability.rented')]"
            :selected="$filters['availability'] ?? null"
        />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="property_type"
            label="{{ __('properties.property_type') }}"
            placeholder="{{ __('properties.all_types') }}"
            :options="$propertyTypes"
            :selected="$filters['property_type'] ?? null"
        />
    </div>

    <div class="lg:col-span-3">
        <x-ui.select
            name="city"
            label="{{ __('properties.city') }}"
            placeholder="{{ __('properties.all_cities') }}"
            :options="$cities"
            :selected="$filters['city'] ?? null"
            x-model="cityId"
            @change="districtId = ''; loadDistricts()"
        />
    </div>

    <div class="lg:col-span-3">
        <label class="field-label">{{ __('properties.district') }}</label>
        <select name="district" x-model="districtId" :disabled="! cityId || loadingDistricts" class="form-select">
            <option value="">{{ __('properties.all_districts') }}</option>
            <template x-for="district in districts" :key="district.id">
                <option :value="district.id" x-text="district.name"></option>
            </template>
        </select>
    </div>

    <div class="lg:col-span-3">
        <x-ui.select
            name="sort"
            label="{{ __('properties.sort_by') }}"
            :options="[
                'newest' => __('properties.newest'),
                'oldest' => __('properties.oldest'),
                'price_asc' => __('properties.price_low_high'),
                'price_desc' => __('properties.price_high_low'),
                'most_viewed' => __('properties.most_viewed'),
                'title_asc' => __('properties.title_asc'),
            ]"
            :selected="$filters['sort'] ?? 'newest'"
        />
    </div>

    <div class="flex items-center gap-2 lg:col-span-3">
        <x-ui.button type="submit" variant="primary" class="w-full justify-center">
            <svg x-show="!loading" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <svg x-show="loading" x-cloak class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z"></path>
            </svg>
            {{ __('messages.filter') }}
        </x-ui.button>

        @if (array_filter($filters))
            <x-ui.button :href="route('owner.properties.index')" variant="ghost" aria-label="{{ __('messages.reset_filters') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </x-ui.button>
        @endif
    </div>
</form>
