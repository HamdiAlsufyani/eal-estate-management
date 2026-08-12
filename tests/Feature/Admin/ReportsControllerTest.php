<?php

use App\Models\Inquiry;

test('guests are redirected to login', function () {
    $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
});

test('users without the analytics permission cannot access reports', function () {
    $owner = ownerTierUser();

    $this->actingAs($owner)->get(route('admin.reports.index'))->assertForbidden();
});

test('staff granted the analytics permission can access reports', function () {
    $staff = adminUser(['analytics.view']);

    $this->actingAs($staff)
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertViewIs('admin.reports.index')
        ->assertViewHas('type', 'properties');
});

test('each report type renders successfully', function (string $type) {
    $staff = adminUser(['analytics.view']);
    makeProperty();

    $this->actingAs($staff)
        ->get(route('admin.reports.index', ['type' => $type]))
        ->assertOk()
        ->assertViewHas('type', $type);
})->with(['properties', 'users', 'inquiries', 'views', 'favorites']);

test('the properties report can be filtered by owner', function () {
    $staff = adminUser(['analytics.view']);
    $owner = ownerTierUser();
    $ownedProperty = makeProperty(['user_id' => $owner->id]);
    makeProperty();

    $response = $this->actingAs($staff)->get(route('admin.reports.index', ['type' => 'properties', 'owner' => $owner->id]));

    $response->assertViewHas('results', function ($results) use ($ownedProperty) {
        return $results->total() === 1 && $results->first()->id === $ownedProperty->id;
    });
});

test('the inquiries report can be filtered by status', function () {
    $staff = adminUser(['analytics.view']);
    $property = makeProperty();

    Inquiry::factory()->create(['property_id' => $property->id, 'user_id' => activeUser()->id, 'phone' => '0500000001', 'message' => 'a', 'status' => 'new']);
    Inquiry::factory()->create(['property_id' => $property->id, 'user_id' => activeUser()->id, 'phone' => '0500000002', 'message' => 'b', 'status' => 'closed']);

    $response = $this->actingAs($staff)->get(route('admin.reports.index', ['type' => 'inquiries', 'status' => 'closed']));

    $response->assertViewHas('results', fn ($results) => $results->total() === 1 && $results->first()->status === 'closed');
});
