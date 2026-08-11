<?php

use App\Models\Favorite;

test('guest visiting favorites page is redirected to login', function () {
    $this->get(route('favorites.index'))->assertRedirect(route('login'));
});

test('favorites page only shows the current users favorites', function () {
    $user = activeUser();
    $mine = makeProperty(['title' => 'My Saved Property']);
    $someoneElses = makeProperty(['title' => 'Not Mine']);

    Favorite::create(['user_id' => $user->id, 'property_id' => $mine->id]);
    Favorite::create(['user_id' => activeUser()->id, 'property_id' => $someoneElses->id]);

    $this->actingAs($user)
        ->get(route('favorites.index'))
        ->assertOk()
        ->assertSee('My Saved Property')
        ->assertDontSee('Not Mine');
});

test('favorites page shows the empty state when there are no favorites', function () {
    $user = activeUser();

    $this->actingAs($user)
        ->get(route('favorites.index'))
        ->assertOk()
        ->assertSee('No favorite properties yet.');
});

test('a favorite whose property was later rejected still renders gracefully', function () {
    $user = activeUser();
    $property = makeProperty(['status' => 'approved']);
    Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

    $property->update(['status' => 'rejected']);

    $this->actingAs($user)
        ->get(route('favorites.index'))
        ->assertOk()
        ->assertSee('Currently unavailable');
});

test('a favorite whose property was soft deleted still renders gracefully', function () {
    $user = activeUser();
    $property = makeProperty();
    Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

    $property->delete();

    $this->actingAs($user)
        ->get(route('favorites.index'))
        ->assertOk()
        ->assertSee('Removed');
});

test('a favorite can still be removed after its property becomes unavailable', function () {
    $user = activeUser();
    $property = makeProperty(['status' => 'approved']);
    Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

    $property->update(['status' => 'rejected']);

    $this->actingAs($user)
        ->postJson(route('properties.favorite', $property))
        ->assertOk()
        ->assertJson(['favorited' => false]);

    expect(Favorite::where('user_id', $user->id)->where('property_id', $property->id)->exists())->toBeFalse();
});

test('a favorite can still be removed after its property is soft deleted', function () {
    $user = activeUser();
    $property = makeProperty();
    Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

    $property->delete();

    $this->actingAs($user)
        ->postJson(route('properties.favorite', $property))
        ->assertOk()
        ->assertJson(['favorited' => false]);

    expect(Favorite::where('user_id', $user->id)->where('property_id', $property->id)->exists())->toBeFalse();
});
