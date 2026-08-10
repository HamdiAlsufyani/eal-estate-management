<form
    method="GET"
    action="{{ route('admin.amenities.index') }}"
    x-data="{ loading: false }"
    @submit="loading = true"
    class="mb-6 grid grid-cols-1 gap-3 border-b border-border pb-6 sm:grid-cols-2 lg:grid-cols-12 lg:items-end"
>
    <div class="lg:col-span-5">
        <x-ui.input
            name="search"
            label="Search"
            placeholder="Name or slug…"
            :value="$filters['search'] ?? ''"
        />
    </div>

    <div class="lg:col-span-3">
        <x-ui.select
            name="status"
            label="Status"
            placeholder="All Statuses"
            :options="['active' => 'Active', 'inactive' => 'Inactive']"
            :selected="$filters['status'] ?? null"
        />
    </div>

    <div class="lg:col-span-2">
        <x-ui.select
            name="sort"
            label="Sort By"
            :options="['newest' => 'Newest', 'oldest' => 'Oldest', 'name_asc' => 'Name A-Z', 'name_desc' => 'Name Z-A']"
            :selected="$filters['sort'] ?? 'newest'"
        />
    </div>

    <div class="flex items-center gap-2 lg:col-span-2">
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
            <x-ui.button :href="route('admin.amenities.index')" variant="ghost" aria-label="Reset filters">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </x-ui.button>
        @endif
    </div>
</form>
