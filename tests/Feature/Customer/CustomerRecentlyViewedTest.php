<?php

use App\Models\PropertyView;

test('guest visiting recently viewed is redirected to login', function () {
    $this->get(route('customer.recently-viewed'))->assertRedirect(route('login'));
});

test('recently viewed only shows the current customers own views', function () {
    $customer = activeUser();
    $mine = makeProperty(['title' => 'Viewed By Me']);
    $notMine = makeProperty(['title' => 'Viewed By Someone Else']);

    PropertyView::create(['user_id' => $customer->id, 'property_id' => $mine->id]);
    PropertyView::create(['user_id' => activeUser()->id, 'property_id' => $notMine->id]);

    $this->actingAs($customer)
        ->get(route('customer.recently-viewed'))
        ->assertOk()
        ->assertSee('Viewed By Me')
        ->assertDontSee('Viewed By Someone Else');
});

test('a property viewed multiple times only appears once, using the latest view time', function () {
    $customer = activeUser();
    $property = makeProperty();

    PropertyView::create(['user_id' => $customer->id, 'property_id' => $property->id, 'created_at' => now()->subDays(3)]);
    PropertyView::create(['user_id' => $customer->id, 'property_id' => $property->id, 'created_at' => now()->subDays(2)]);
    $latest = PropertyView::create(['user_id' => $customer->id, 'property_id' => $property->id, 'created_at' => now()->subDay()]);

    $response = $this->actingAs($customer)->get(route('customer.recently-viewed'))->assertOk();

    $properties = $response->viewData('properties');
    $lastViewedAt = $response->viewData('lastViewedAt');

    expect($properties)->toHaveCount(1);
    expect($lastViewedAt[$property->id]->timestamp)->toBe($latest->created_at->timestamp);
});

test('recently viewed properties are ordered by the most recently viewed first', function () {
    $customer = activeUser();
    $older = makeProperty();
    $newer = makeProperty();

    PropertyView::create(['user_id' => $customer->id, 'property_id' => $older->id, 'created_at' => now()->subDays(5)]);
    PropertyView::create(['user_id' => $customer->id, 'property_id' => $newer->id, 'created_at' => now()->subDay()]);

    $response = $this->actingAs($customer)->get(route('customer.recently-viewed'))->assertOk();

    $properties = $response->viewData('properties');

    expect($properties->first()->id)->toBe($newer->id);
    expect($properties->last()->id)->toBe($older->id);
});

test('recently viewed is capped at 20 properties', function () {
    $customer = activeUser();

    foreach (range(1, 25) as $i) {
        PropertyView::create(['user_id' => $customer->id, 'property_id' => makeProperty()->id]);
    }

    $response = $this->actingAs($customer)->get(route('customer.recently-viewed'))->assertOk();

    expect($response->viewData('properties'))->toHaveCount(20);
});

test('recently viewed shows an empty state when the customer has no views yet', function () {
    $customer = activeUser();

    $this->actingAs($customer)
        ->get(route('customer.recently-viewed'))
        ->assertOk()
        ->assertSee(__('customer.no_recently_viewed'));
});
