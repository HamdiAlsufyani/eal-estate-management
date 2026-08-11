<?php

use App\Models\PropertyView;

test('viewing a property records a property view for a guest', function () {
    $property = makeProperty();

    $this->get(route('properties.show', $property))->assertOk();

    expect(PropertyView::where('property_id', $property->id)->count())->toBe(1);
    expect(PropertyView::first()->user_id)->toBeNull();
    expect(PropertyView::first()->ip_address)->not->toBeNull();
});

test('viewing a property records the authenticated user', function () {
    $user = activeUser();
    $property = makeProperty();

    $this->actingAs($user)->get(route('properties.show', $property))->assertOk();

    expect(PropertyView::where('property_id', $property->id)->where('user_id', $user->id)->count())->toBe(1);
});

test('refreshing a property page does not create duplicate views for the same visitor', function () {
    $property = makeProperty();

    $this->get(route('properties.show', $property));
    $this->get(route('properties.show', $property));
    $this->get(route('properties.show', $property));

    expect(PropertyView::where('property_id', $property->id)->count())->toBe(1);
});

test('different guests viewing the same property each get counted', function () {
    $property = makeProperty();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])->get(route('properties.show', $property));
    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])->get(route('properties.show', $property));

    expect(PropertyView::where('property_id', $property->id)->count())->toBe(2);
});
