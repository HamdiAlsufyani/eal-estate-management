<?php

use App\Models\City;

test('guests are redirected to login', function () {
    $this->get(route('admin.cities.index'))->assertRedirect(route('login'));
});

test('users without permission cannot access cities', function () {
    $user = activeUser();

    $this->actingAs($user)
        ->get(route('admin.cities.index'))
        ->assertForbidden();
});

test('admin can view the cities index', function () {
    $admin = adminUser(['cities.view']);
    City::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('admin.cities.index'))
        ->assertOk()
        ->assertViewIs('admin.cities.index');
});

test('admin can create a city with an auto-generated slug', function () {
    $admin = adminUser(['cities.view', 'cities.create']);

    $response = $this->actingAs($admin)->post(route('admin.cities.store'), [
        'name' => 'Dammam Central',
        'is_active' => 1,
    ]);

    $city = City::firstWhere('name', 'Dammam Central');

    $response->assertRedirect(route('admin.cities.show', $city));
    expect($city->slug)->toBe('dammam-central');
});

test('admin can update a city', function () {
    $admin = adminUser(['cities.view', 'cities.edit']);
    $city = City::factory()->create(['name' => 'Riyadh', 'slug' => 'riyadh']);

    $response = $this->actingAs($admin)->put(route('admin.cities.update', $city), [
        'name' => 'Riyadh',
        'slug' => 'riyadh',
        'is_active' => 0,
    ]);

    $response->assertRedirect(route('admin.cities.show', $city));
    expect($city->refresh()->is_active)->toBeFalse();
});

test('admin cannot delete a city with districts', function () {
    $admin = adminUser(['cities.view', 'cities.delete']);
    $city = City::factory()->create();
    \App\Models\District::factory()->create(['city_id' => $city->id]);

    $this->actingAs($admin)
        ->delete(route('admin.cities.destroy', $city))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(City::find($city->id))->not->toBeNull();
});

test('admin cannot delete a city with properties', function () {
    $admin = adminUser(['cities.view', 'cities.delete']);
    $city = City::factory()->create();
    makeProperty(['city_id' => $city->id]);

    $this->actingAs($admin)
        ->delete(route('admin.cities.destroy', $city))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(City::find($city->id))->not->toBeNull();
});

test('admin can delete a city with no districts or properties', function () {
    $admin = adminUser(['cities.view', 'cities.delete']);
    $city = City::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.cities.destroy', $city))
        ->assertRedirect(route('admin.cities.index'));

    expect(City::find($city->id))->toBeNull();
});

test('index supports searching by name or slug', function () {
    $admin = adminUser(['cities.view']);
    City::factory()->create(['name' => 'Jeddah', 'slug' => 'jeddah']);
    City::factory()->create(['name' => 'Khobar', 'slug' => 'khobar']);

    $this->actingAs($admin)
        ->get(route('admin.cities.index', ['search' => 'jeddah']))
        ->assertOk()
        ->assertViewHas('cities', fn ($cities) => $cities->total() === 1);
});

test('index supports filtering by status', function () {
    $admin = adminUser(['cities.view']);
    City::factory()->create(['is_active' => true]);
    City::factory()->inactive()->create();

    $this->actingAs($admin)
        ->get(route('admin.cities.index', ['status' => 'inactive']))
        ->assertOk()
        ->assertViewHas('cities', fn ($cities) => $cities->total() === 1);
});
