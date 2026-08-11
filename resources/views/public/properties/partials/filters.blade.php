@php
    $bedroomOptions = [1 => '1+', 2 => '2+', 3 => '3+', 4 => '4+', 5 => '5+'];
    $selectedAmenities = array_map('intval', (array) ($filters['amenities'] ?? []));
@endphp

<form
    id="properties-filters"
    method="GET"
    action="{{ route('properties.index') }}"
    x-data="{
        cityId: '{{ $filters['city'] ?? '' }}',
        districts: @js($districts->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->values()),
        selectedDistrict: '{{ $filters['district'] ?? '' }}',
        loadDistricts() {
            if (! this.cityId) { this.districts = []; this.selectedDistrict = ''; return; }
            fetch(`/cities/${this.cityId}/districts`, { headers: { Accept: 'application/json' } })
                .then(r => r.json())
                .then(data => { this.districts = data; });
        },
    }"
    class="space-y-5"
>
    <x-ui.input type="text" name="search" label="{{ __('messages.search') }}" placeholder="{{ __('properties.search_placeholder') }}" value="{{ $filters['search'] ?? '' }}" />

    <x-ui.select name="purpose" label="{{ __('properties.purpose_label') }}" placeholder="{{ __('messages.all') }}" :selected="$filters['purpose'] ?? null" :options="['sale' => __('properties.for_sale'), 'rent' => __('properties.for_rent')]" />

    <x-ui.select name="property_type" label="{{ __('properties.property_type') }}" placeholder="{{ __('properties.all_types') }}" :selected="$filters['property_type'] ?? null" :options="$propertyTypes->pluck('name', 'id')" />

    <div>
        <label for="city" class="field-label">{{ __('properties.city') }}</label>
        <select name="city" id="city" x-model="cityId" @change="loadDistricts()" class="form-select">
            <option value="">{{ __('properties.all_cities') }}</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}" @selected((string) ($filters['city'] ?? '') === (string) $city->id)>{{ $city->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="district" class="field-label">{{ __('properties.district') }}</label>
        <select name="district" id="district" x-model="selectedDistrict" class="form-select" :disabled="districts.length === 0">
            <option value="">{{ __('properties.all_districts') }}</option>
            <template x-for="district in districts" :key="district.id">
                <option :value="district.id" x-text="district.name" :selected="String(district.id) === String(selectedDistrict)"></option>
            </template>
        </select>
    </div>

    <div>
        <p class="field-label">{{ __('properties.price_range', ['currency' => __('messages.currency')]) }}</p>
        <div class="grid grid-cols-2 gap-2">
            <x-ui.input type="number" min="0" name="price_from" placeholder="{{ __('properties.min_price') }}" value="{{ $filters['price_from'] ?? '' }}" />
            <x-ui.input type="number" min="0" name="price_to" placeholder="{{ __('properties.max_price') }}" value="{{ $filters['price_to'] ?? '' }}" />
        </div>
    </div>

    <div>
        <p class="field-label">{{ __('properties.area') }} (m²)</p>
        <div class="grid grid-cols-2 gap-2">
            <x-ui.input type="number" min="0" name="area_from" placeholder="{{ __('properties.min_area') }}" value="{{ $filters['area_from'] ?? '' }}" />
            <x-ui.input type="number" min="0" name="area_to" placeholder="{{ __('properties.max_area') }}" value="{{ $filters['area_to'] ?? '' }}" />
        </div>
    </div>

    <x-ui.select name="bedrooms" label="{{ __('properties.bedrooms') }}" placeholder="{{ __('messages.any') }}" :selected="$filters['bedrooms'] ?? null" :options="$bedroomOptions" />

    <x-ui.select name="bathrooms" label="{{ __('properties.bathrooms') }}" placeholder="{{ __('messages.any') }}" :selected="$filters['bathrooms'] ?? null" :options="$bedroomOptions" />

    <x-ui.select name="furnished" label="{{ __('properties.furnished') }}" placeholder="{{ __('messages.any') }}" :selected="$filters['furnished'] ?? null" :options="[1 => __('properties.furnished'), 0 => __('properties.unfurnished')]" />

    @if ($amenities->isNotEmpty())
        <div>
            <p class="field-label">{{ __('properties.amenities') }}</p>
            <div class="max-h-48 space-y-2 overflow-y-auto pr-1">
                @foreach ($amenities as $amenity)
                    <label class="flex items-center gap-2 text-sm text-text">
                        <input
                            type="checkbox"
                            name="amenities[]"
                            value="{{ $amenity->id }}"
                            @checked(in_array($amenity->id, $selectedAmenities, true))
                            class="h-4 w-4 rounded border-border text-primary focus:ring-primary-hover/30"
                        />
                        {{ $amenity->name }}
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    <div class="flex items-center gap-3 pt-2">
        <x-ui.button type="submit" class="flex-1 justify-center">{{ __('properties.apply_filters') }}</x-ui.button>
        <x-ui.button :href="route('properties.index')" variant="outline" class="flex-1 justify-center">{{ __('messages.clear_filters') }}</x-ui.button>
    </div>
</form>
