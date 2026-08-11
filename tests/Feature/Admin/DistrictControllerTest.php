<?php

use App\Models\City;
use App\Models\District;

test('guests are redirected to login', function () {
    $this->get(route('admin.districts.index'))->assertRedirect(route('login'));
});

test('users without permission cannot access districts', function () {
    $user = activeUser();

    $this->actingAs($user)
        ->get(route('admin.districts.index'))
        ->assertForbidden();
});

test('admin can create a district belonging to a city', function () {
    $admin = adminUser(['districts.view', 'districts.create']);
    $city = City::factory()->create(['name' => 'Riyadh']);

    $response = $this->actingAs($admin)->post(route('admin.districts.store'), [
        'city_id' => $city->id,
        'name_en' => 'Al Olaya',
    ]);

    $district = District::firstWhere('name_en', 'Al Olaya');

    $response->assertRedirect(route('admin.districts.show', $district));
    expect($district->city_id)->toBe($city->id);
    expect($district->slug)->toBe('al-olaya');
});

test('duplicate district name in the same city is rejected', function () {
    $admin = adminUser(['districts.view', 'districts.create']);
    $city = City::factory()->create();
    District::factory()->create(['city_id' => $city->id, 'name' => 'Al Olaya', 'slug' => 'al-olaya']);

    $this->actingAs($admin)
        ->post(route('admin.districts.store'), [
            'city_id' => $city->id,
            'name_en' => 'Al Olaya',
        ])
        ->assertSessionHasErrors('name_en');

    expect(District::where('city_id', $city->id)->where('name', 'Al Olaya')->count())->toBe(1);
});

test('same district name in different cities is allowed', function () {
    $admin = adminUser(['districts.view', 'districts.create']);
    $riyadh = City::factory()->create();
    $jeddah = City::factory()->create();
    District::factory()->create(['city_id' => $riyadh->id, 'name' => 'Al Olaya', 'slug' => 'al-olaya']);

    $this->actingAs($admin)
        ->post(route('admin.districts.store'), [
            'city_id' => $jeddah->id,
            'name_en' => 'Al Olaya',
        ])
        ->assertSessionHasNoErrors();

    expect(District::where('name', 'Al Olaya')->count())->toBe(2);
});

test('admin cannot delete a district with properties', function () {
    $admin = adminUser(['districts.view', 'districts.delete']);
    $district = District::factory()->create();
    makeProperty(['city_id' => $district->city_id, 'district_id' => $district->id]);

    $this->actingAs($admin)
        ->delete(route('admin.districts.destroy', $district))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(District::find($district->id))->not->toBeNull();
});

test('admin can delete a district with no properties', function () {
    $admin = adminUser(['districts.view', 'districts.delete']);
    $district = District::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.districts.destroy', $district))
        ->assertRedirect(route('admin.districts.index'));

    expect(District::find($district->id))->toBeNull();
});

test('index supports searching by name or slug', function () {
    $admin = adminUser(['districts.view']);
    District::factory()->create(['name' => 'Al Malqa', 'slug' => 'al-malqa']);
    District::factory()->create(['name' => 'Al Naseem', 'slug' => 'al-naseem']);

    $this->actingAs($admin)
        ->get(route('admin.districts.index', ['search' => 'malqa']))
        ->assertOk()
        ->assertViewHas('districts', fn ($districts) => $districts->total() === 1);
});

test('only active cities are selectable when creating a district', function () {
    $admin = adminUser(['districts.view', 'districts.create']);
    $active = City::factory()->create(['name' => 'Active City']);
    $inactive = City::factory()->inactive()->create(['name' => 'Inactive City']);

    $this->actingAs($admin)
        ->get(route('admin.districts.create'))
        ->assertOk()
        ->assertViewHas('cities', function ($cities) use ($active, $inactive) {
            return $cities->has($active->id) && ! $cities->has($inactive->id);
        });
});
