<?php

use Illuminate\Support\Facades\Route;

test('owner can access the owner dashboard', function () {
    $owner = ownerTierUser();

    $this->actingAs($owner)
        ->get(route('owner.dashboard'))
        ->assertOk()
        ->assertViewIs('owner.dashboard');
});

test('guests are redirected to login', function () {
    $this->get(route('owner.dashboard'))->assertRedirect(route('login'));
});

test('the centralized dashboard route redirects owner-tier users to the owner dashboard', function () {
    $owner = ownerTierUser();

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertRedirect(route('owner.dashboard'));
});

test('the centralized dashboard route keeps showing the admin dashboard for staff-tier users', function () {
    $admin = staffTierUser();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewIs('dashboard');
});

test('admin visiting the owner dashboard only sees their own scoped stats, never global data', function () {
    $admin = staffTierUser();
    makeProperty(['status' => 'pending']);
    makeProperty(['status' => 'approved']);
    $ownProperty = makeProperty(['status' => 'pending', 'user_id' => $admin->id]);

    $this->actingAs($admin)
        ->get(route('owner.dashboard'))
        ->assertOk()
        ->assertViewHas('stats', function ($stats) use ($ownProperty) {
            return $stats['total_properties'] === 1
                && $stats['pending_properties'] === 1
                && $stats['approved_properties'] === 0;
        });
});

test('dashboard statistics are scoped to the authenticated owner only', function () {
    $owner = ownerTierUser();
    makeProperty(['status' => 'pending', 'user_id' => $owner->id]);
    makeProperty(['status' => 'approved', 'user_id' => $owner->id, 'availability' => 'sold']);
    makeProperty(['status' => 'rejected', 'user_id' => $owner->id]);
    // Other owners' properties must never affect these numbers.
    makeProperty(['status' => 'pending']);
    makeProperty(['status' => 'approved']);

    $this->actingAs($owner)
        ->get(route('owner.dashboard'))
        ->assertOk()
        ->assertViewHas('stats', function ($stats) {
            return $stats['total_properties'] === 3
                && $stats['pending_properties'] === 1
                && $stats['approved_properties'] === 1
                && $stats['rejected_properties'] === 1
                && $stats['sold_properties'] === 1;
        });
});

test('owner sidebar never exposes admin-only routes', function () {
    $owner = ownerTierUser();

    $response = $this->actingAs($owner)->get(route('owner.dashboard'));

    $response->assertDontSee(route('admin.users.index'));
    $response->assertDontSee(route('admin.cities.index'));
    $response->assertDontSee(route('admin.property-types.index'));
});

test('there is no owner-facing route for changing property status or availability', function () {
    expect(Route::has('owner.properties.status'))->toBeFalse();
    expect(Route::has('owner.properties.availability'))->toBeFalse();
});
