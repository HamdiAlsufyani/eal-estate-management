<?php

use App\Models\PropertyView;

test('guest visiting recently viewed is redirected to login', function () {
    $this->get(route('customer.recently-viewed'))->assertRedirect(route('login'));
});

test('guest cannot remove or clear recently viewed', function () {
    $property = makeProperty();

    $this->delete(route('customer.recently-viewed.destroy', $property))->assertRedirect(route('login'));
    $this->delete(route('customer.recently-viewed.clear'))->assertRedirect(route('login'));
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

test('recently viewed page collapses multiple view records for the same property, using the latest timestamp', function () {
    $customer = activeUser();
    $property = makeProperty();

    PropertyView::create(['user_id' => $customer->id, 'property_id' => $property->id, 'updated_at' => now()->subDays(3)]);
    PropertyView::create(['user_id' => $customer->id, 'property_id' => $property->id, 'updated_at' => now()->subDays(2)]);
    $latest = PropertyView::create(['user_id' => $customer->id, 'property_id' => $property->id, 'updated_at' => now()->subDay()]);

    $response = $this->actingAs($customer)->get(route('customer.recently-viewed'))->assertOk();
    $properties = $response->viewData('properties');

    expect($properties)->toHaveCount(1);
    expect($properties->first()->last_viewed_at->timestamp)->toBe($latest->updated_at->timestamp);
});

test('viewing the same property again within the dedupe window updates the timestamp instead of duplicating', function () {
    $customer = activeUser();
    $property = makeProperty();

    $view = PropertyView::create([
        'user_id' => $customer->id,
        'property_id' => $property->id,
        'updated_at' => now()->subHours(2),
    ]);

    $this->actingAs($customer)->get(route('properties.show', $property))->assertOk();

    expect(PropertyView::where('user_id', $customer->id)->where('property_id', $property->id)->count())->toBe(1);
    expect($view->fresh()->updated_at->greaterThan(now()->subMinute()))->toBeTrue();
});

test('recently viewed properties are ordered by the most recently viewed first', function () {
    $customer = activeUser();
    $older = makeProperty();
    $newer = makeProperty();

    PropertyView::create(['user_id' => $customer->id, 'property_id' => $older->id, 'updated_at' => now()->subDays(5)]);
    PropertyView::create(['user_id' => $customer->id, 'property_id' => $newer->id, 'updated_at' => now()->subDay()]);

    $response = $this->actingAs($customer)->get(route('customer.recently-viewed'))->assertOk();
    $properties = $response->viewData('properties');

    expect($properties->first()->id)->toBe($newer->id);
    expect($properties->last()->id)->toBe($older->id);
});

test('recently viewed paginates 20 per page', function () {
    $customer = activeUser();

    foreach (range(1, 25) as $i) {
        PropertyView::create(['user_id' => $customer->id, 'property_id' => makeProperty()->id]);
    }

    $response = $this->actingAs($customer)->get(route('customer.recently-viewed'))->assertOk();
    $properties = $response->viewData('properties');

    expect($properties)->toHaveCount(20);
    expect($properties->hasMorePages())->toBeTrue();

    $page2 = $this->actingAs($customer)->get(route('customer.recently-viewed', ['page' => 2]))->assertOk();
    expect($page2->viewData('properties'))->toHaveCount(5);
});

test('recently viewed can be filtered by purpose', function () {
    $customer = activeUser();
    $forSale = makeProperty(['purpose' => 'sale', 'title' => 'Sale Property']);
    $forRent = makeProperty(['purpose' => 'rent', 'title' => 'Rent Property']);

    PropertyView::create(['user_id' => $customer->id, 'property_id' => $forSale->id]);
    PropertyView::create(['user_id' => $customer->id, 'property_id' => $forRent->id]);

    $this->actingAs($customer)
        ->get(route('customer.recently-viewed', ['purpose' => 'sale']))
        ->assertOk()
        ->assertSee('Sale Property')
        ->assertDontSee('Rent Property');
});

test('recently viewed shows an empty state when the customer has no views yet', function () {
    $customer = activeUser();

    $this->actingAs($customer)
        ->get(route('customer.recently-viewed'))
        ->assertOk()
        ->assertSee(__('customer.no_recently_viewed'));
});

test('viewing a pending property does not create a property view', function () {
    $customer = activeUser();
    $property = makeProperty(['status' => 'pending']);

    $this->actingAs($customer)->get(route('properties.show', $property))->assertNotFound();

    expect(PropertyView::where('property_id', $property->id)->exists())->toBeFalse();
});

test('viewing a rejected property does not create a property view', function () {
    $customer = activeUser();
    $property = makeProperty(['status' => 'rejected']);

    $this->actingAs($customer)->get(route('properties.show', $property))->assertNotFound();

    expect(PropertyView::where('property_id', $property->id)->exists())->toBeFalse();
});

test('a trashed property in recently viewed shows a no longer available placeholder', function () {
    $customer = activeUser();
    $property = makeProperty();
    PropertyView::create(['user_id' => $customer->id, 'property_id' => $property->id]);
    $property->delete();

    $this->actingAs($customer)
        ->get(route('customer.recently-viewed'))
        ->assertOk()
        ->assertSee(__('customer.property_no_longer_available'));
});

test('a customer can remove a property from recently viewed', function () {
    $customer = activeUser();
    $property = makeProperty();
    PropertyView::create(['user_id' => $customer->id, 'property_id' => $property->id]);

    $this->actingAs($customer)
        ->delete(route('customer.recently-viewed.destroy', $property))
        ->assertRedirect();

    expect(PropertyView::where('user_id', $customer->id)->where('property_id', $property->id)->exists())->toBeFalse();
});

test('a customer cannot remove another customers recently viewed property', function () {
    $customer = activeUser();
    $otherCustomer = activeUser();
    $property = makeProperty();
    PropertyView::create(['user_id' => $otherCustomer->id, 'property_id' => $property->id]);

    $this->actingAs($customer)
        ->delete(route('customer.recently-viewed.destroy', $property))
        ->assertRedirect();

    expect(PropertyView::where('user_id', $otherCustomer->id)->where('property_id', $property->id)->exists())->toBeTrue();
});

test('removing a soft deleted property from recently viewed still works', function () {
    $customer = activeUser();
    $property = makeProperty();
    PropertyView::create(['user_id' => $customer->id, 'property_id' => $property->id]);
    $property->delete();

    $this->actingAs($customer)
        ->delete(route('customer.recently-viewed.destroy', $property))
        ->assertRedirect();

    expect(PropertyView::where('user_id', $customer->id)->where('property_id', $property->id)->exists())->toBeFalse();
});

test('a customer can clear all of their recently viewed properties', function () {
    $customer = activeUser();
    PropertyView::create(['user_id' => $customer->id, 'property_id' => makeProperty()->id]);
    PropertyView::create(['user_id' => $customer->id, 'property_id' => makeProperty()->id]);

    $this->actingAs($customer)
        ->delete(route('customer.recently-viewed.clear'))
        ->assertRedirect(route('customer.recently-viewed'));

    expect(PropertyView::where('user_id', $customer->id)->count())->toBe(0);
});

test('clearing recently viewed only affects the authenticated customer', function () {
    $customer = activeUser();
    $otherCustomer = activeUser();
    PropertyView::create(['user_id' => $customer->id, 'property_id' => makeProperty()->id]);
    PropertyView::create(['user_id' => $otherCustomer->id, 'property_id' => makeProperty()->id]);

    $this->actingAs($customer)->delete(route('customer.recently-viewed.clear'));

    expect(PropertyView::where('user_id', $otherCustomer->id)->count())->toBe(1);
});
