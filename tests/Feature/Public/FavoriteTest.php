<?php

use App\Models\Favorite;

test('guest attempting to favorite a property is redirected to login', function () {
    $property = makeProperty();

    $this->post(route('properties.favorite', $property))->assertRedirect(route('login'));

    expect(Favorite::count())->toBe(0);
});

test('authenticated user can favorite a property', function () {
    $user = activeUser();
    $property = makeProperty();

    $response = $this->actingAs($user)
        ->postJson(route('properties.favorite', $property));

    $response->assertOk()->assertJson(['favorited' => true, 'count' => 1]);
    expect(Favorite::where('user_id', $user->id)->where('property_id', $property->id)->exists())->toBeTrue();
});

test('favoriting an already favorited property removes it', function () {
    $user = activeUser();
    $property = makeProperty();

    $this->actingAs($user)->postJson(route('properties.favorite', $property));
    $response = $this->actingAs($user)->postJson(route('properties.favorite', $property));

    $response->assertOk()->assertJson(['favorited' => false, 'count' => 0]);
    expect(Favorite::where('user_id', $user->id)->where('property_id', $property->id)->exists())->toBeFalse();
});

test('duplicate favorites are prevented at the database level', function () {
    $user = activeUser();
    $property = makeProperty();

    Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

    expect(fn () => Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('a pending or rejected property cannot be favorited', function () {
    $user = activeUser();
    $property = makeProperty(['status' => 'pending']);

    $this->actingAs($user)->postJson(route('properties.favorite', $property))->assertNotFound();
});
