@php
    $isCreate = ! isset($district);
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
    <x-ui.select
        name="city_id"
        label="{{ __('properties.city') }}"
        placeholder="{{ __('properties.all_cities') }}"
        :options="$cities"
        :selected="old('city_id', $district->city_id ?? null)"
        required
    />

    <div></div>

    <x-ui.input
        name="name_en"
        label="{{ __('properties.name') }} (EN)"
        placeholder="e.g. Al Olaya"
        value="{{ old('name_en', $district->name_en ?? '') }}"
        @input="onNameInput($event.target.value)"
        required
    />

    <x-ui.input
        name="name_ar"
        label="{{ __('properties.name') }} (AR)"
        placeholder="مثال: العليا"
        dir="rtl"
        value="{{ old('name_ar', $district->name_ar ?? '') }}"
    />

    <x-ui.input
        x-ref="slug"
        name="slug"
        label="Slug"
        placeholder="e.g. al-olaya"
        value="{{ old('slug', $district->slug ?? '') }}"
        hint="Auto-generated from the English name — edit manually if needed. Must be unique within the selected city."
        @input="onSlugInput()"
        required
    />
</div>
