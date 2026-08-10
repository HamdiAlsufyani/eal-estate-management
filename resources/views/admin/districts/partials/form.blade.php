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
        label="City"
        placeholder="Select a city"
        :options="$cities"
        :selected="old('city_id', $district->city_id ?? null)"
        required
    />

    <div></div>

    <x-ui.input
        name="name"
        label="Name"
        placeholder="e.g. Al Olaya"
        value="{{ old('name', $district->name ?? '') }}"
        @input="onNameInput($event.target.value)"
        required
    />

    <x-ui.input
        x-ref="slug"
        name="slug"
        label="Slug"
        placeholder="e.g. al-olaya"
        value="{{ old('slug', $district->slug ?? '') }}"
        hint="Auto-generated from the name — edit manually if needed. Must be unique within the selected city."
        @input="onSlugInput()"
        required
    />
</div>
