@php
    $isCreate = ! isset($amenity);
@endphp

<div
    class="grid grid-cols-1 gap-5 sm:grid-cols-2"
    x-data="{
        slugTouched: {{ $isCreate ? 'false' : 'true' }},
        slugify(value) {
            return value
                .toString()
                .trim()
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-+|-+$)/g, '');
        },
        onNameInput(value) {
            if (! this.slugTouched) {
                $refs.slug.value = this.slugify(value);
            }
        },
        onSlugInput() {
            this.slugTouched = true;
        },
    }"
>
    <x-ui.input
        name="name_en"
        label="{{ __('properties.name') }} (EN)"
        placeholder="e.g. Swimming Pool"
        value="{{ old('name_en', $amenity->name_en ?? '') }}"
        @input="onNameInput($event.target.value)"
        required
    />

    <x-ui.input
        name="name_ar"
        label="{{ __('properties.name') }} (AR)"
        placeholder="مثال: مسبح"
        dir="rtl"
        value="{{ old('name_ar', $amenity->name_ar ?? '') }}"
    />

    <x-ui.input
        x-ref="slug"
        name="slug"
        label="Slug"
        placeholder="e.g. swimming-pool"
        value="{{ old('slug', $amenity->slug ?? '') }}"
        hint="Auto-generated from the English name — edit manually if needed."
        @input="onSlugInput()"
        required
    />

    <x-ui.input
        name="icon"
        label="Icon"
        placeholder="e.g. pool, wifi, shield-check"
        value="{{ old('icon', $amenity->icon ?? '') }}"
        hint="Icon identifier used by the storefront."
    />

    <x-ui.select
        name="is_active"
        label="{{ __('messages.status') }}"
        :options="[1 => __('messages.active'), 0 => __('messages.inactive')]"
        :selected="old('is_active', $isCreate ? 1 : (int) $amenity->is_active)"
        required
    />
</div>
