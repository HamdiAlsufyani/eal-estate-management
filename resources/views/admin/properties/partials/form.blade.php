@php
    $routePrefix = $routePrefix ?? 'admin';
    $isCreate = ! isset($property);
    $selectedAmenityIds = old('amenities', $isCreate ? [] : $property->amenities->pluck('id')->all());
    $canAssignOwner = auth()->user()->can('properties.assign_owner');
    $canSetInitialStatus = $isCreate && auth()->user()->can('properties.approve');
    $canFeature = auth()->user()->can('properties.feature');
    $canChangeAvailability = ! $isCreate && $property->status === 'approved' && auth()->user()->can('changeAvailability', $property);
    $availabilityOptions = ($property->purpose ?? 'sale') === 'sale'
        ? ['available' => 'Available', 'reserved' => 'Reserved', 'sold' => 'Sold']
        : ['available' => 'Available', 'reserved' => 'Reserved', 'rented' => 'Rented'];
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
    <x-ui.card title="Basic Information">
        <div class="grid grid-cols-1 gap-5">
            <x-ui.input
                name="title"
                label="Title"
                placeholder="e.g. Modern Apartment in Al Olaya"
                value="{{ old('title', $property->title ?? '') }}"
                required
            />

            <x-ui.textarea
                name="description"
                label="Description"
                placeholder="Describe the property…"
                :rows="5"
                required
            >{{ old('description', $property->description ?? '') }}</x-ui.textarea>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <x-ui.select
                    name="property_type_id"
                    label="Property Type"
                    placeholder="Select a type"
                    :options="$propertyTypes"
                    :selected="old('property_type_id', $property->property_type_id ?? null)"
                    required
                />

                <x-ui.select
                    name="purpose"
                    label="Purpose"
                    :options="['sale' => 'Sale', 'rent' => 'Rent']"
                    :selected="old('purpose', $property->purpose ?? 'sale')"
                    x-model="purpose"
                    required
                />

                <x-ui.input
                    name="price"
                    type="number"
                    step="0.01"
                    min="0"
                    label="Price"
                    value="{{ old('price', $property->price ?? '') }}"
                    required
                />
            </div>

            <div x-show="purpose === 'rent'" x-cloak class="sm:w-1/3">
                <x-ui.select
                    name="rent_period"
                    label="Rent Period"
                    placeholder="Select a period"
                    :options="['monthly' => 'Monthly', 'yearly' => 'Yearly']"
                    :selected="old('rent_period', $property->rent_period ?? null)"
                />
            </div>
        </div>
    </x-ui.card>

    {{-- Location --}}
    <x-ui.card title="Location">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-ui.select
                name="city_id"
                label="City"
                placeholder="Select a city"
                :options="$cities"
                :selected="old('city_id', $property->city_id ?? null)"
                x-model="cityId"
                @change="districtId = ''; loadDistricts()"
                required
            />

            <div>
                <label class="field-label">District</label>
                <select
                    name="district_id"
                    x-model="districtId"
                    :disabled="! cityId || loadingDistricts"
                    class="form-select"
                    required
                >
                    <option value="">{{ $isCreate ? 'Select a city first' : 'Select a district' }}</option>
                    <template x-for="district in districts" :key="district.id">
                        <option :value="district.id" x-text="district.name"></option>
                    </template>
                </select>
                @error('district_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <x-ui.input
                    name="address"
                    label="Address"
                    placeholder="Street, building, landmark…"
                    value="{{ old('address', $property->address ?? '') }}"
                    required
                />
            </div>

            <x-ui.input
                name="latitude"
                type="number"
                step="0.00000001"
                label="Latitude"
                placeholder="e.g. 24.7136"
                value="{{ old('latitude', $property->latitude ?? '') }}"
            />

            <x-ui.input
                name="longitude"
                type="number"
                step="0.00000001"
                label="Longitude"
                placeholder="e.g. 46.6753"
                value="{{ old('longitude', $property->longitude ?? '') }}"
            />
        </div>
    </x-ui.card>

    {{-- Property Details --}}
    <x-ui.card title="Property Details">
        <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">
            <x-ui.input name="area" type="number" step="0.01" min="0" label="Area (m²)" value="{{ old('area', $property->area ?? '') }}" required />
            <x-ui.input name="bedrooms" type="number" min="0" label="Bedrooms" value="{{ old('bedrooms', $property->bedrooms ?? 0) }}" />
            <x-ui.input name="bathrooms" type="number" min="0" label="Bathrooms" value="{{ old('bathrooms', $property->bathrooms ?? 0) }}" />
            <x-ui.input name="living_rooms" type="number" min="0" label="Living Rooms" value="{{ old('living_rooms', $property->living_rooms ?? 0) }}" />
            <x-ui.input name="kitchens" type="number" min="0" label="Kitchens" value="{{ old('kitchens', $property->kitchens ?? 0) }}" />
            <x-ui.input name="floor" type="number" min="0" label="Floor" value="{{ old('floor', $property->floor ?? '') }}" />
            <x-ui.input name="parking_spaces" type="number" min="0" label="Parking Spaces" value="{{ old('parking_spaces', $property->parking_spaces ?? 0) }}" />

            <div>
                <label class="field-label">Furnished</label>
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
                    <span class="text-sm text-text">Yes</span>
                </label>
            </div>
        </div>
    </x-ui.card>

    {{-- Amenities --}}
    <x-ui.card title="Amenities">
        @if ($amenities->isEmpty())
            <p class="text-sm text-text-muted">No active amenities are available yet.</p>
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
    <x-ui.card title="Images">
        @if (! $isCreate && $property->getMedia('property-images')->isNotEmpty())
            <div class="mb-5">
                <p class="field-label">Current Images</p>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($property->getMedia('property-images') as $index => $media)
                        <div class="group relative overflow-hidden rounded-[var(--radius-control)] border border-border">
                            <img src="{{ $media->getUrl() }}" alt="Property image" class="h-28 w-full object-cover" />

                            @if ($index === 0)
                                <span class="absolute left-1.5 top-1.5 rounded-full bg-primary px-2 py-0.5 text-[10px] font-medium text-white">Cover</span>
                            @endif

                            <form
                                method="POST"
                                action="{{ route($routePrefix.'.properties.images.destroy', [$property, $media]) }}"
                                class="absolute right-1.5 top-1.5"
                                onsubmit="return confirm('Remove this image?')"
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
                <p class="field-hint">The first image is used as the cover photo. Remove images to reorder which one is first.</p>
            </div>
        @endif

        <label class="field-label">{{ $isCreate ? 'Upload Images' : 'Add More Images' }}</label>
        <input
            type="file"
            name="images[]"
            multiple
            accept="image/jpeg,image/png,image/webp"
            @change="onImagesSelected($event)"
            class="form-input"
        />
        <p class="field-hint">JPG, PNG or WEBP. Up to 10 images, 5MB each.</p>
        @error('images')
            <p class="field-error">{{ $message }}</p>
        @enderror
        @error('images.*')
            <p class="field-error">{{ $message }}</p>
        @enderror

        <div x-show="imagePreviews.length > 0" x-cloak class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <template x-for="preview in imagePreviews" :key="preview">
                <img :src="preview" alt="Preview" class="h-28 w-full rounded-[var(--radius-control)] border border-border object-cover" />
            </template>
        </div>
    </x-ui.card>

    {{-- Additional --}}
    @if ($canFeature || $canAssignOwner || $canSetInitialStatus || $canChangeAvailability)
        <x-ui.card title="Additional">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                @if ($canAssignOwner)
                    <x-ui.select
                        name="user_id"
                        label="Owner"
                        placeholder="Assign to myself"
                        :options="$owners"
                        :selected="old('user_id', $property->user_id ?? null)"
                    />
                @endif

                @if ($canSetInitialStatus)
                    <x-ui.select
                        name="status"
                        label="Initial Status"
                        :options="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']"
                        :selected="old('status', 'pending')"
                    />
                @endif

                @if ($canChangeAvailability)
                    <x-ui.select
                        name="availability"
                        label="Availability"
                        :options="$availabilityOptions"
                        :selected="old('availability', $property->availability)"
                    />
                @endif

                @if ($canFeature)
                    <div>
                        <label class="field-label">Featured</label>
                        <label class="flex h-10 items-center gap-2">
                            <input type="hidden" name="featured" value="0" />
                            <input
                                type="checkbox"
                                name="featured"
                                value="1"
                                @checked(old('featured', $property->featured ?? false))
                                class="h-4 w-4 rounded border-border text-primary focus:ring-primary-hover"
                            />
                            <span class="text-sm text-text">Show this property as featured</span>
                        </label>
                        <p class="field-hint">Only approved properties can be featured.</p>
                    </div>
                @endif
            </div>
        </x-ui.card>
    @endif
</div>
