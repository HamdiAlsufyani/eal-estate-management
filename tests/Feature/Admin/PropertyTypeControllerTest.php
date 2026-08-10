<?php

use App\Models\PropertyType;

test('guests are redirected to login', function () {
    $this->get(route('admin.property-types.index'))->assertRedirect(route('login'));
});

test('users without permission cannot access property types', function () {
    $user = activeUser();

    $this->actingAs($user)
        ->get(route('admin.property-types.index'))
        ->assertForbidden();
});

test('admin can view the property types index', function () {
    $admin = adminUser(['property_types.manage']);
    PropertyType::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('admin.property-types.index'))
        ->assertOk()
        ->assertViewIs('admin.property-types.index');
});

test('admin can create a property type with an auto-generated slug', function () {
    $admin = adminUser(['property_types.manage']);

    $response = $this->actingAs($admin)->post(route('admin.property-types.store'), [
        'name' => 'Penthouse Suite',
        'icon' => 'building',
        'is_active' => 1,
    ]);

    $propertyType = PropertyType::firstWhere('name', 'Penthouse Suite');

    $response->assertRedirect(route('admin.property-types.show', $propertyType));
    expect($propertyType->slug)->toBe('penthouse-suite');
});

test('name and slug must be unique when creating', function () {
    $admin = adminUser(['property_types.manage']);
    PropertyType::factory()->create(['name' => 'Apartment', 'slug' => 'apartment']);

    $this->actingAs($admin)
        ->post(route('admin.property-types.store'), [
            'name' => 'Apartment',
            'is_active' => 1,
        ])
        ->assertSessionHasErrors('name');
});

test('admin can update a property type and slug uniqueness ignores itself', function () {
    $admin = adminUser(['property_types.manage']);
    $propertyType = PropertyType::factory()->create(['name' => 'Villa', 'slug' => 'villa']);

    $response = $this->actingAs($admin)->put(route('admin.property-types.update', $propertyType), [
        'name' => 'Villa',
        'slug' => 'villa',
        'is_active' => 0,
    ]);

    $response->assertRedirect(route('admin.property-types.show', $propertyType));
    expect($propertyType->refresh()->is_active)->toBeFalse();
});

test('admin can view a property type with property counts', function () {
    $admin = adminUser(['property_types.manage']);
    $propertyType = PropertyType::factory()->create();
    makeProperty(['property_type_id' => $propertyType->id, 'status' => 'approved']);
    makeProperty(['property_type_id' => $propertyType->id, 'status' => 'approved']);
    makeProperty(['property_type_id' => $propertyType->id, 'status' => 'pending']);

    $this->actingAs($admin)
        ->get(route('admin.property-types.show', $propertyType))
        ->assertOk()
        ->assertViewHas('propertyType', fn ($viewPropertyType) => $viewPropertyType->properties_count === 3
            && $viewPropertyType->active_properties_count === 2);
});

test('admin can delete a property type with no properties', function () {
    $admin = adminUser(['property_types.manage']);
    $propertyType = PropertyType::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.property-types.destroy', $propertyType))
        ->assertRedirect(route('admin.property-types.index'));

    expect(PropertyType::find($propertyType->id))->toBeNull();
});

test('property types with assigned properties cannot be deleted', function () {
    $admin = adminUser(['property_types.manage']);
    $propertyType = PropertyType::factory()->create();
    makeProperty(['property_type_id' => $propertyType->id]);

    $this->actingAs($admin)
        ->delete(route('admin.property-types.destroy', $propertyType))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(PropertyType::find($propertyType->id))->not->toBeNull();
});

test('index supports searching by name or slug', function () {
    $admin = adminUser(['property_types.manage']);
    PropertyType::factory()->create(['name' => 'Warehouse', 'slug' => 'warehouse']);
    PropertyType::factory()->create(['name' => 'Farm', 'slug' => 'farm']);

    $this->actingAs($admin)
        ->get(route('admin.property-types.index', ['search' => 'warehouse']))
        ->assertOk()
        ->assertViewHas('propertyTypes', fn ($propertyTypes) => $propertyTypes->total() === 1);
});

test('active scope only returns active property types', function () {
    PropertyType::factory()->create(['is_active' => true]);
    PropertyType::factory()->inactive()->create();

    expect(PropertyType::active()->count())->toBe(1);
});
