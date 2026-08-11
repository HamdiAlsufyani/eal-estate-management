<?php

use App\Models\City;
use App\Models\PropertyType;

test('guest can access the homepage', function () {
    $this->get(route('home'))->assertOk();
});

test('homepage only shows approved featured and latest properties', function () {
    $approved = makeProperty(['title' => 'Approved Home', 'status' => 'approved', 'featured' => true, 'published_at' => now()]);
    makeProperty(['title' => 'Pending Home', 'status' => 'pending', 'featured' => true]);
    makeProperty(['title' => 'Rejected Home', 'status' => 'rejected', 'featured' => true]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Approved Home')
        ->assertDontSee('Pending Home')
        ->assertDontSee('Rejected Home');
});

test('homepage only lists active property types and cities with approved properties', function () {
    $activeType = PropertyType::factory()->create(['name' => 'Villa', 'is_active' => true]);
    $inactiveType = PropertyType::factory()->create(['name' => 'Barn', 'is_active' => false]);

    $cityWithProperty = City::factory()->create(['name' => 'Dammam', 'is_active' => true]);
    $cityWithoutProperty = City::factory()->create(['name' => 'Empty City', 'is_active' => true]);

    makeProperty(['property_type_id' => $activeType->id, 'city_id' => $cityWithProperty->id, 'status' => 'approved']);

    $response = $this->get(route('home'))->assertOk();

    $response->assertViewHas('propertyTypes', fn ($types) => $types->pluck('name')->contains('Villa') && ! $types->pluck('name')->contains('Barn'));
    $response->assertViewHas('cities', fn ($cities) => $cities->pluck('name')->contains('Dammam') && ! $cities->pluck('name')->contains('Empty City'));
});
