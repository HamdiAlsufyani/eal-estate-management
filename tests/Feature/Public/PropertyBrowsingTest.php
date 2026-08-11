<?php

use App\Models\Amenity;
use App\Models\City;
use App\Models\District;
use App\Models\Property;
use App\Models\PropertyType;

test('guest can browse the properties index', function () {
    makeProperty(['title' => 'Browsable Listing']);

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertSee('Browsable Listing');
});

test('only approved properties are listed publicly', function () {
    makeProperty(['title' => 'Approved Listing', 'status' => 'approved']);
    makeProperty(['title' => 'Pending Listing', 'status' => 'pending']);
    makeProperty(['title' => 'Rejected Listing', 'status' => 'rejected']);

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertSee('Approved Listing')
        ->assertDontSee('Pending Listing')
        ->assertDontSee('Rejected Listing');
});

test('guest can search properties by title, description or address', function () {
    makeProperty(['title' => 'Unique Skyline Loft', 'address' => '1 Main St']);
    makeProperty(['title' => 'Something Else', 'address' => '99 Rare Boulevard']);

    $this->get(route('properties.index', ['search' => 'Skyline']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);

    $this->get(route('properties.index', ['search' => 'Rare Boulevard']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);
});

test('guest can filter properties by purpose, type, city and district', function () {
    $city = City::factory()->create();
    $district = District::factory()->create(['city_id' => $city->id]);
    $type = PropertyType::factory()->create();

    $match = makeProperty(['purpose' => 'rent', 'property_type_id' => $type->id, 'city_id' => $city->id, 'district_id' => $district->id]);
    makeProperty(['purpose' => 'sale']);

    $this->get(route('properties.index', ['purpose' => 'rent']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1 && $p->first()->is($match));

    $this->get(route('properties.index', ['property_type' => $type->id]))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1 && $p->first()->is($match));

    $this->get(route('properties.index', ['city' => $city->id]))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1 && $p->first()->is($match));

    $this->get(route('properties.index', ['district' => $district->id]))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1 && $p->first()->is($match));
});

test('a district filter must belong to the selected city', function () {
    $cityA = City::factory()->create();
    $cityB = City::factory()->create();
    $districtOfB = District::factory()->create(['city_id' => $cityB->id]);

    makeProperty(['city_id' => $cityB->id, 'district_id' => $districtOfB->id]);

    $this->get(route('properties.index', ['city' => $cityA->id, 'district' => $districtOfB->id]))
        ->assertViewHas('properties', fn ($p) => $p->total() === 0);
});

test('guest can filter by price range', function () {
    $cheap = makeProperty(['price' => 100000]);
    $expensive = makeProperty(['price' => 900000]);

    $this->get(route('properties.index', ['price_from' => 500000]))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1 && $p->first()->is($expensive));

    $this->get(route('properties.index', ['price_to' => 500000]))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1 && $p->first()->is($cheap));
});

test('guest can filter by amenities', function () {
    $wanted = Amenity::factory()->create(['is_active' => true]);
    $other = Amenity::factory()->create(['is_active' => true]);

    $withAmenity = makeProperty();
    $withAmenity->amenities()->attach($wanted->id);

    $withoutAmenity = makeProperty();
    $withoutAmenity->amenities()->attach($other->id);

    $this->get(route('properties.index', ['amenities' => [$wanted->id]]))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1 && $p->first()->is($withAmenity));
});

test('only active property types and cities appear in the filter options', function () {
    $activeType = PropertyType::factory()->create(['is_active' => true]);
    $inactiveType = PropertyType::factory()->create(['is_active' => false]);
    $activeCity = City::factory()->create(['is_active' => true]);
    $inactiveCity = City::factory()->create(['is_active' => false]);

    $response = $this->get(route('properties.index'))->assertOk();

    $response->assertViewHas('propertyTypes', fn ($types) => $types->contains('id', $activeType->id) && ! $types->contains('id', $inactiveType->id));
    $response->assertViewHas('cities', fn ($cities) => $cities->contains('id', $activeCity->id) && ! $cities->contains('id', $inactiveCity->id));
});

test('guest can sort properties by price', function () {
    $cheap = makeProperty(['price' => 1000]);
    $expensive = makeProperty(['price' => 9000]);

    $this->get(route('properties.index', ['sort' => 'price_asc']))
        ->assertViewHas('properties', fn ($p) => $p->first()->is($cheap));

    $this->get(route('properties.index', ['sort' => 'price_desc']))
        ->assertViewHas('properties', fn ($p) => $p->first()->is($expensive));
});

test('guest can sort properties by most viewed', function () {
    $popular = makeProperty();
    $unpopular = makeProperty();

    \App\Models\PropertyView::factory()->count(3)->create(['property_id' => $popular->id]);
    \App\Models\PropertyView::factory()->count(1)->create(['property_id' => $unpopular->id]);

    $this->get(route('properties.index', ['sort' => 'most_viewed']))
        ->assertViewHas('properties', fn ($p) => $p->first()->is($popular));
});

test('an invalid sort value is ignored rather than trusted directly', function () {
    makeProperty();

    $this->get(route('properties.index', ['sort' => 'id ASC; DROP TABLE properties;']))
        ->assertOk();

    expect(Property::count())->toBeGreaterThan(0);
});

test('the properties index paginates 12 per page and preserves query parameters across pages', function () {
    collect(range(1, 15))->each(fn () => makeProperty(['purpose' => 'rent']));

    $response = $this->get(route('properties.index', ['purpose' => 'rent', 'page' => 2]));

    $response->assertOk();
    $response->assertViewHas('properties', fn ($properties) => $properties->perPage() === 12 && $properties->currentPage() === 2);
    $response->assertSee('purpose=rent', false);
});

test('guest can view an approved property by its slug', function () {
    $property = makeProperty(['title' => 'Slug Routed Property', 'slug' => 'slug-routed-property']);

    $this->get(route('properties.show', $property))
        ->assertOk()
        ->assertSee('Slug Routed Property');

    $this->get('/properties/slug-routed-property')->assertOk();
});

test('guest receives 404 for a pending property', function () {
    $property = makeProperty(['status' => 'pending']);

    $this->get(route('properties.show', $property))->assertNotFound();
});

test('guest receives 404 for a rejected property', function () {
    $property = makeProperty(['status' => 'rejected']);

    $this->get(route('properties.show', $property))->assertNotFound();
});

test('guest receives 404 for an unknown property slug', function () {
    $this->get('/properties/does-not-exist')->assertNotFound();
});
