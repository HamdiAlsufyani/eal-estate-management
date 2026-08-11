@php
    $routePrefix = $routePrefix ?? 'admin';
    $isCreate = ! isset($property);
    $selectedAmenityIds = old('amenities', $isCreate ? [] : $property->amenities->pluck('id')->all());
    $canAssignOwner = auth()->user()->can('properties.assign_owner');
    $canSetInitialStatus = $isCreate && auth()->user()->can('properties.approve');
    $canFeature = auth()->user()->can('properties.feature');
    $canChangeAvailability = ! $isCreate && $property->status === 'approved' && auth()->user()->can('changeAvailability', $property);
    $availabilityOptions = ($property->purpose ?? 'sale') === 'sale'
        ? ['available' => __('properties.availability.available'), 'reserved' => __('properties.availability.reserved'), 'sold' => __('properties.availability.sold')]
        : ['available' => __('properties.availability.available'), 'reserved' => __('properties.availability.reserved'), 'rented' => __('properties.availability.rented')];
@endphp

<div
    x-data="{
        purpose: @js(old('purpose', $property->purpose ?? 'sale')),
        cityId: @js(old('city_id', $property->city_id ?? '')),
        districts: @js($districts->map(fn ($name, $id) => ['id' => (string) $id, 'name' => $name])->values()),
        districtId: @js((string) old('district_id', $property->district_id ?? '')),
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
        imagePreviews: [],
        onImagesSelected(event) {
            this.imagePreviews = Array.from(event.target.files).map((file) => URL.createObjectURL(file));
        },
    }"
    class="space-y-6"
>
    {{-- Basic Information --}}
    <x-ui.card title="{{ __('properties.basic_information') }}">
        <div class="grid grid-cols-1 gap-5">
            <x-ui.input
                name="title_en"
                label="Title (EN)"
                placeholder="e.g. Modern Apartment in Al Olaya"
                value="{{ old('title_en', $property->title_en ?? '') }}"
                required
            />

            <x-ui.input
                name="title_ar"
                label="Title (AR)"
                placeholder="مثال: شقة حديثة في العليا"
                dir="rtl"
                value="{{ old('title_ar', $property->title_ar ?? '') }}"
            />

            <x-ui.textarea
                name="description_en"
                label="Description (EN)"
                placeholder="Describe the property…"
                :rows="5"
                required
            >{{ old('description_en', $property->description_en ?? '') }}</x-ui.textarea>

            <x-ui.textarea
                name="description_ar"
                label="Description (AR)"
                placeholder="صف العقار…"
                dir="rtl"
                :rows="5"
            >{{ old('description_ar', $property->description_ar ?? '') }}</x-ui.textarea>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <x-ui.select
                    name="property_type_id"
                    label="{{ __('properties.property_type') }}"
                    placeholder="{{ __('properties.select_type') }}"
                    :options="$propertyTypes"
                    :selected="old('property_type_id', $property->property_type_id ?? null)"
                    required
                />

                <x-ui.select
                    name="purpose"
                    label="{{ __('properties.purpose_label') }}"
                    :options="['sale' => __('properties.purpose.sale'), 'rent' => __('properties.purpose.rent')]"
                    :selected="old('purpose', $property->purpose ?? 'sale')"
                    x-model="purpose"
                    required
                />

                <x-ui.input
                    name="price"
                    type="number"
                    step="0.01"
                    min="0"
                    label="{{ __('properties.price') }}"
                    value="{{ old('price', $property->price ?? '') }}"
                    required
                />
            </div>

            <div x-show="purpose === 'rent'" x-cloak class="sm:w-1/3">
                <x-ui.select
                    name="rent_period"
                    label="{{ __('properties.rent_period_label') }}"
                    placeholder="{{ __('properties.select_period') }}"
                    :options="['monthly' => __('properties.rent_period.monthly'), 'yearly' => __('properties.rent_period.yearly')]"
                    :selected="old('rent_period', $property->rent_period ?? null)"
                />
            </div>
        </div>
    </x-ui.card>

    {{-- Location --}}
    <x-ui.card title="{{ __('properties.location') }}">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-ui.select
                name="city_id"
                label="{{ __('properties.city') }}"
                placeholder="{{ __('properties.select_city') }}"
                :options="$cities"
                :selected="old('city_id', $property->city_id ?? null)"
                x-model="cityId"
                @change="districtId = ''; loadDistricts()"
                required
            />

            <div>
                <label class="field-label">{{ __('properties.district') }}</label>
                <select
                    name="district_id"
                    x-model="districtId"
                    :disabled="! cityId || loadingDistricts"
                    class="form-select"
                    required
                >
                    <option value="">{{ $isCreate ? __('properties.select_city_first') : __('properties.select_district') }}</option>
                    <template x-for="district in districts" :key="district.id">
                        <option :value="district.id" x-text="district.name"></option>
                    </template>
                </select>
                @error('district_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-ui.input
                    name="address_en"
                    label="Address (EN)"
                    placeholder="Street, building, landmark…"
                    value="{{ old('address_en', $property->address_en ?? '') }}"
                    required
                />
            </div>

            <div>
                <x-ui.input
                    name="address_ar"
                    label="Address (AR)"
                    placeholder="الشارع، المبنى، معلم مميز…"
                    dir="rtl"
                    value="{{ old('address_ar', $property->address_ar ?? '') }}"
                />
            </div>

            <x-ui.input
                name="latitude"
                type="number"
                step="0.00000001"
                label="{{ __('properties.latitude') }}"
                placeholder="{{ __('properties.latitude_placeholder') }}"
                value="{{ old('latitude', $property->latitude ?? '') }}"
            />

            <x-ui.input
                name="longitude"
                type="number"
                step="0.00000001"
                label="{{ __('properties.longitude') }}"
                placeholder="{{ __('properties.longitude_placeholder') }}"
                value="{{ old('longitude', $property->longitude ?? '') }}"
            />
        </div>
    </x-ui.card>

    {{-- Property Details --}}
    <x-ui.card title="{{ __('properties.property_details') }}">
        <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">
            <x-ui.input name="area" type="number" step="0.01" min="0" label="{{ __('properties.area_m2') }}" value="{{ old('area', $property->area ?? '') }}" required />
            <x-ui.input name="bedrooms" type="number" min="0" label="{{ __('properties.bedrooms') }}" value="{{ old('bedrooms', $property->bedrooms ?? 0) }}" />
            <x-ui.input name="bathrooms" type="number" min="0" label="{{ __('properties.bathrooms') }}" value="{{ old('bathrooms', $property->bathrooms ?? 0) }}" />
            <x-ui.input name="living_rooms" type="number" min="0" label="{{ __('properties.living_rooms') }}" value="{{ old('living_rooms', $property->living_rooms ?? 0) }}" />
            <x-ui.input name="kitchens" type="number" min="0" label="{{ __('properties.kitchens') }}" value="{{ old('kitchens', $property->kitchens ?? 0) }}" />
            <x-ui.input name="floor" type="number" min="0" label="{{ __('properties.floor') }}" value="{{ old('floor', $property->floor ?? '') }}" />
            <x-ui.input name="parking_spaces" type="number" min="0" label="{{ __('properties.parking_spaces') }}" value="{{ old('parking_spaces', $property->parking_spaces ?? 0) }}" />

            <div>
                <label class="field-label">{{ __('properties.furnished') }}</label>
                <label class="flex h-10 items-center gap-2">
                    <input
                        type="hidden" name="furnished" value="0"
                    />
                    <input
                        type="checkbox"
                        name="furnished"
                        value="1"
                        @checked(old('furnished', $property->furnished ?? false))
                        class="h-4 w-4 rounded border-border text-primary focus:ring-primary-hover"
                    />
                    <span class="text-sm text-text">{{ __('messages.yes') }}</span>
                </label>
            </div>
        </div>
    </x-ui.card>

    {{-- Amenities --}}
    <x-ui.card title="{{ __('properties.amenities') }}">
        @if ($amenities->isEmpty())
            <p class="text-sm text-text-muted">{{ __('properties.no_amenities') }}</p>
        @else
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($amenities as $amenityId => $amenityName)
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="amenities[]"
                            value="{{ $amenityId }}"
                            @checked(in_array($amenityId, $selectedAmenityIds))
                            class="h-4 w-4 rounded border-border text-primary focus:ring-primary-hover"
                        />
                        <span class="text-sm text-text">{{ $amenityName }}</span>
                    </label>
                @endforeach
            </div>
        @endif
        @error('amenities')
            <p class="field-error mt-2">{{ $message }}</p>
        @enderror
    </x-ui.card>

    {{-- Images --}}
    <x-ui.card title="{{ __('properties.images') }}">
        @if (! $isCreate && $property->getMedia('property-images')->isNotEmpty())
            <div class="mb-5">
                <p class="field-label">{{ __('properties.current_images') }}</p>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($property->getMedia('property-images') as $index => $media)
                        <div class="group relative overflow-hidden rounded-[var(--radius-control)] border border-border">
                            <img src="{{ $media->getUrl() }}" alt="{{ __('properties.property_image_alt') }}" class="h-28 w-full object-cover" />

                            @if ($index === 0)
                                <span class="absolute left-1.5 top-1.5 rounded-full bg-primary px-2 py-0.5 text-[10px] font-medium text-white">{{ __('properties.cover') }}</span>
                            @endif

                            <form
                                method="POST"
                                action="{{ route($routePrefix.'.properties.images.destroy', [$property, $media]) }}"
                                class="absolute right-1.5 top-1.5"
                                onsubmit="return confirm('{{ __('properties.confirm_remove_image') }}')"
                            >
                                @csrf @method('DELETE')
                                <button type="submit" class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-900/70 text-white hover:bg-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <p class="field-hint">{{ __('properties.cover_hint') }}</p>
            </div>
        @endif

        <label class="field-label">{{ $isCreate ? __('properties.upload_images') : __('properties.add_more_images') }}</label>
        <input
            type="file"
            name="images[]"
            multiple
            accept="image/jpeg,image/png,image/webp"
            @change="onImagesSelected($event)"
            class="form-input"
        />
        <p class="field-hint">{{ __('properties.images_hint') }}</p>
        @error('images')
            <p class="field-error">{{ $message }}</p>
        @enderror
        @error('images.*')
            <p class="field-error">{{ $message }}</p>
        @enderror

        <div x-show="imagePreviews.length > 0" x-cloak class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <template x-for="preview in imagePreviews" :key="preview">
                <img :src="preview" alt="{{ __('properties.preview_alt') }}" class="h-28 w-full rounded-[var(--radius-control)] border border-border object-cover" />
            </template>
        </div>
    </x-ui.card>

    {{-- Additional --}}
    @if ($canFeature || $canAssignOwner || $canSetInitialStatus || $canChangeAvailability)
        <x-ui.card title="{{ __('properties.additional') }}">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                @if ($canAssignOwner)
                    <x-ui.select
                        name="user_id"
                        label="{{ __('properties.owner') }}"
                        placeholder="{{ __('properties.assign_to_myself') }}"
                        :options="$owners"
                        :selected="old('user_id', $property->user_id ?? null)"
                    />
                @endif

                @if ($canSetInitialStatus)
                    <x-ui.select
                        name="status"
                        label="{{ __('properties.initial_status') }}"
                        :options="['pending' => __('properties.status.pending'), 'approved' => __('properties.status.approved'), 'rejected' => __('properties.status.rejected')]"
                        :selected="old('status', 'pending')"
                    />
                @endif

                @if ($canChangeAvailability)
                    <x-ui.select
                        name="availability"
                        label="{{ __('properties.availability_label') }}"
                        :options="$availabilityOptions"
                        :selected="old('availability', $property->availability)"
                    />
                @endif

                @if ($canFeature)
                    <div>
                        <label class="field-label">{{ __('properties.featured') }}</label>
                        <label class="flex h-10 items-center gap-2">
                            <input type="hidden" name="featured" value="0" />
                            <input
                                type="checkbox"
                                name="featured"
                                value="1"
                                @checked(old('featured', $property->featured ?? false))
                                class="h-4 w-4 rounded border-border text-primary focus:ring-primary-hover"
                            />
                            <span class="text-sm text-text">{{ __('properties.show_as_featured') }}</span>
                        </label>
                        <p class="field-hint">{{ __('properties.featured_hint') }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>
    @endif
</div>
