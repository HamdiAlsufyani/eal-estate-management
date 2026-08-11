<?php

use App\Models\City;
use App\Models\District;

test('the districts by city endpoint only returns districts for that city', function () {
    $cityA = City::factory()->create();
    $cityB = City::factory()->create();
    $districtA = District::factory()->create(['city_id' => $cityA->id]);
    District::factory()->create(['city_id' => $cityB->id]);

    $response = $this->getJson(route('properties.cities.districts', $cityA));

    $response->assertOk();
    $response->assertJsonCount(1);
    $response->assertJsonFragment(['id' => $districtA->id]);
});

test('registering redirects to the public home page instead of the internal dashboard', function () {
    $response = $this->post(route('register'), [
        'name' => 'New Visitor',
        'email' => 'new-visitor@example.com',
        'phone' => '0512345678',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('home', absolute: false));
    $this->assertAuthenticated();
});
