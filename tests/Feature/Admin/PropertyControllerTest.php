<?php

test('guests are redirected to login', function () {
    $this->get(route('admin.properties.index'))->assertRedirect(route('login'));
});

test('users without permission cannot view properties', function () {
    $user = activeUser();

    $this->actingAs($user)
        ->get(route('admin.properties.index'))
        ->assertForbidden();
});

test('admin can view the properties index', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    makeProperty(['title' => 'Seaside Villa']);
    makeProperty(['title' => 'Downtown Apartment']);

    $this->actingAs($admin)
        ->get(route('admin.properties.index'))
        ->assertOk()
        ->assertViewIs('admin.properties.index');
});

test('index supports searching by title', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    makeProperty(['title' => 'Seaside Villa']);
    makeProperty(['title' => 'Downtown Apartment']);

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['search' => 'Seaside']))
        ->assertOk()
        ->assertViewHas('properties', fn ($properties) => $properties->total() === 1);
});

test('index supports filtering by status', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    makeProperty(['status' => 'pending']);
    makeProperty(['status' => 'approved']);

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['status' => 'pending']))
        ->assertOk()
        ->assertViewHas('properties', fn ($properties) => $properties->total() === 1);
});

test('admin can view a property show page', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty();

    $this->actingAs($admin)
        ->get(route('admin.properties.show', $property))
        ->assertOk()
        ->assertViewIs('admin.properties.show')
        ->assertSee($property->title);
});
