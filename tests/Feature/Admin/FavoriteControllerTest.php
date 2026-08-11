<?php

use App\Models\Favorite;

test('guests are redirected to login', function () {
    $this->get(route('admin.favorites.index'))->assertRedirect(route('login'));
});

test('users without permission cannot access favorites', function () {
    $user = activeUser();

    $this->actingAs($user)
        ->get(route('admin.favorites.index'))
        ->assertForbidden();
});

test('admin can view favorites across all users', function () {
    $admin = adminUser(['favorites.view']);
    $propertyA = makeProperty(['title' => 'Favorite Listing A']);
    $propertyB = makeProperty(['title' => 'Favorite Listing B']);

    Favorite::create(['user_id' => activeUser()->id, 'property_id' => $propertyA->id]);
    Favorite::create(['user_id' => activeUser()->id, 'property_id' => $propertyB->id]);

    $this->actingAs($admin)
        ->get(route('admin.favorites.index'))
        ->assertOk()
        ->assertSee('Favorite Listing A')
        ->assertSee('Favorite Listing B');
});
