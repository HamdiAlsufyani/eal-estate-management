<?php

use App\Models\Amenity;

test('guests are redirected to login', function () {
    $this->get(route('admin.amenities.index'))->assertRedirect(route('login'));
});

test('users without permission cannot access amenities', function () {
    $user = activeUser();

    $this->actingAs($user)
        ->get(route('admin.amenities.index'))
        ->assertForbidden();
});

test('admin can view the amenities index', function () {
    $admin = adminUser(['amenities.view']);
    Amenity::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('admin.amenities.index'))
        ->assertOk()
        ->assertViewIs('admin.amenities.index');
});

test('admin can create an amenity with an auto-generated slug', function () {
    $admin = adminUser(['amenities.view', 'amenities.create']);

    $response = $this->actingAs($admin)->post(route('admin.amenities.store'), [
        'name' => 'Swimming Pool',
        'icon' => 'pool',
        'is_active' => 1,
    ]);

    $amenity = Amenity::firstWhere('name', 'Swimming Pool');

    $response->assertRedirect(route('admin.amenities.show', $amenity));
    expect($amenity->slug)->toBe('swimming-pool');
});

test('admin can update an amenity', function () {
    $admin = adminUser(['amenities.view', 'amenities.edit']);
    $amenity = Amenity::factory()->create(['name' => 'Gym', 'slug' => 'gym']);

    $response = $this->actingAs($admin)->put(route('admin.amenities.update', $amenity), [
        'name' => 'Gym',
        'slug' => 'gym',
        'is_active' => 0,
    ]);

    $response->assertRedirect(route('admin.amenities.show', $amenity));
    expect($amenity->refresh()->is_active)->toBeFalse();
});

test('admin cannot delete an amenity assigned to properties', function () {
    $admin = adminUser(['amenities.view', 'amenities.delete']);
    $amenity = Amenity::factory()->create();
    $property = makeProperty();
    $property->amenities()->attach($amenity);

    $this->actingAs($admin)
        ->delete(route('admin.amenities.destroy', $amenity))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(Amenity::find($amenity->id))->not->toBeNull();
});

test('admin can delete an amenity with no properties assigned', function () {
    $admin = adminUser(['amenities.view', 'amenities.delete']);
    $amenity = Amenity::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.amenities.destroy', $amenity))
        ->assertRedirect(route('admin.amenities.index'));

    expect(Amenity::find($amenity->id))->toBeNull();
});

test('index supports searching by name or slug', function () {
    $admin = adminUser(['amenities.view']);
    Amenity::factory()->create(['name' => 'Elevator', 'slug' => 'elevator']);
    Amenity::factory()->create(['name' => 'Garden', 'slug' => 'garden']);

    $this->actingAs($admin)
        ->get(route('admin.amenities.index', ['search' => 'elevator']))
        ->assertOk()
        ->assertViewHas('amenities', fn ($amenities) => $amenities->total() === 1);
});

test('index supports filtering by status', function () {
    $admin = adminUser(['amenities.view']);
    Amenity::factory()->create(['is_active' => true]);
    Amenity::factory()->inactive()->create();

    $this->actingAs($admin)
        ->get(route('admin.amenities.index', ['status' => 'inactive']))
        ->assertOk()
        ->assertViewHas('amenities', fn ($amenities) => $amenities->total() === 1);
});

test('property can be assigned amenities via the pivot relationship', function () {
    $amenity = Amenity::factory()->create();
    $property = makeProperty();

    $property->amenities()->attach($amenity);

    expect($property->amenities()->count())->toBe(1);
    expect($amenity->properties()->count())->toBe(1);
});
