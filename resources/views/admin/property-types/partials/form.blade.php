@php
    $isCreate = ! isset($propertyType);
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
        name="name"
        label="Name"
        placeholder="e.g. Apartment"
        value="{{ old('name', $propertyType->name ?? '') }}"
        @input="onNameInput($event.target.value)"
        required
    />

    <x-ui.input
        x-ref="slug"
        name="slug"
        label="Slug"
        placeholder="e.g. apartment"
        value="{{ old('slug', $propertyType->slug ?? '') }}"
        hint="Auto-generated from the name — edit manually if needed."
        @input="onSlugInput()"
        required
    />

    <x-ui.input
        name="icon"
        label="Icon"
        placeholder="e.g. home, building-office-2"
        value="{{ old('icon', $propertyType->icon ?? '') }}"
        hint="Icon identifier used by the storefront."
    />

    <x-ui.select
        name="is_active"
        label="Status"
        :options="[1 => 'Active', 0 => 'Inactive']"
        :selected="old('is_active', $isCreate ? 1 : (int) $propertyType->is_active)"
        required
    />
</div>
