<form
    method="GET"
    action="{{ route('admin.properties.index') }}"
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
            label="Search"
            placeholder="Title, description, address, owner…"
            :value="$filters['search'] ?? ''"
        />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="purpose"
            label="Purpose"
            placeholder="All"
            :options="['sale' => 'Sale', 'rent' => 'Rent']"
            :selected="$filters['purpose'] ?? null"
        />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="status"
            label="Status"
            placeholder="All"
            :options="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']"
            :selected="$filters['status'] ?? null"
        />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="availability"
            label="Availability"
            placeholder="All"
            :options="['available' => 'Available', 'reserved' => 'Reserved', 'sold' => 'Sold', 'rented' => 'Rented']"
            :selected="$filters['availability'] ?? null"
        />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="featured"
            label="Featured"
            placeholder="All"
            :options="['featured' => 'Featured', 'not_featured' => 'Not Featured']"
            :selected="$filters['featured'] ?? null"
        />
    </div>

    <div class="lg:col-span-3">
        <x-ui.select
            name="property_type"
            label="Property Type"
            placeholder="All Types"
            :options="$propertyTypes"
            :selected="$filters['property_type'] ?? null"
        />
    </div>

    <div class="lg:col-span-3">
        <x-ui.select
            name="city"
            label="City"
            placeholder="All Cities"
            :options="$cities"
            :selected="$filters['city'] ?? null"
            x-model="cityId"
            @change="districtId = ''; loadDistricts()"
        />
    </div>

    <div class="lg:col-span-3">
        <label class="field-label">District</label>
        <select name="district" x-model="districtId" :disabled="! cityId || loadingDistricts" class="form-select">
            <option value="">All Districts</option>
            <template x-for="district in districts" :key="district.id">
                <option :value="district.id" x-text="district.name"></option>
            </template>
        </select>
    </div>

    @if ($canManageAll)
        <div class="lg:col-span-3">
            <x-ui.select
                name="owner"
                label="Owner"
                placeholder="All Owners"
                :options="$owners"
                :selected="$filters['owner'] ?? null"
            />
        </div>
    @endif

    <div class="lg:col-span-3">
        <x-ui.select
            name="sort"
            label="Sort By"
            :options="[
                'newest' => 'Newest',
                'oldest' => 'Oldest',
                'price_asc' => 'Price Low to High',
                'price_desc' => 'Price High to Low',
                'most_viewed' => 'Most Viewed',
                'title_asc' => 'Title A-Z',
                'title_desc' => 'Title Z-A',
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
            Filter
        </x-ui.button>

        @if (array_filter($filters))
            <x-ui.button :href="route('admin.properties.index')" variant="ghost" aria-label="Reset filters">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </x-ui.button>
        @endif
    </div>
</form>
