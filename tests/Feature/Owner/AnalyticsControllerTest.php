<?php

use App\Models\Favorite;
use App\Models\Inquiry;
use App\Models\PropertyView;

test('guests are redirected to login', function () {
    $this->get(route('owner.analytics.index'))->assertRedirect(route('login'));
});

test('an owner can access their own analytics page', function () {
    $owner = ownerTierUser();

    $this->actingAs($owner)
        ->get(route('owner.analytics.index'))
        ->assertOk()
        ->assertViewIs('owner.analytics.index');
});

test('owner overview totals are scoped strictly to the authenticated owner', function () {
    $owner = ownerTierUser();
    makeProperty(['status' => 'approved', 'user_id' => $owner->id]);
    makeProperty(['status' => 'pending', 'user_id' => $owner->id]);
    makeProperty(['status' => 'rejected', 'user_id' => $owner->id]);
    // Other owners' properties must never leak into these numbers.
    makeProperty(['status' => 'approved']);
    makeProperty(['status' => 'approved']);

    $response = $this->actingAs($owner)->get(route('owner.analytics.index'));

    $response->assertViewHas('overview', function ($overview) {
        return $overview['total_properties'] === 3
            && $overview['approved_properties'] === 1
            && $overview['pending_properties'] === 1
            && $overview['rejected_properties'] === 1;
    });
});

test('an owner never sees another owners views, favorites or inquiries in their totals', function () {
    $owner = ownerTierUser();
    $otherOwner = ownerTierUser();

    $ownProperty = makeProperty(['user_id' => $owner->id]);
    $otherProperty = makeProperty(['user_id' => $otherOwner->id]);

    PropertyView::factory()->count(2)->create(['property_id' => $ownProperty->id]);
    PropertyView::factory()->count(10)->create(['property_id' => $otherProperty->id]);

    Favorite::factory()->create(['user_id' => activeUser()->id, 'property_id' => $ownProperty->id]);
    Favorite::factory()->create(['user_id' => activeUser()->id, 'property_id' => $otherProperty->id]);

    Inquiry::factory()->create(['property_id' => $ownProperty->id, 'user_id' => activeUser()->id, 'phone' => '0500000001', 'message' => 'hi']);
    Inquiry::factory()->create(['property_id' => $otherProperty->id, 'user_id' => activeUser()->id, 'phone' => '0500000002', 'message' => 'hi']);

    $response = $this->actingAs($owner)->get(route('owner.analytics.index'));

    $response->assertViewHas('overview', function ($overview) {
        return $overview['total_views'] === 2
            && $overview['total_favorites'] === 1
            && $overview['total_inquiries'] === 1;
    });
});

test('an owner never sees another owners properties in their top-performing tables', function () {
    $owner = ownerTierUser();
    $otherOwner = ownerTierUser();

    $ownProperty = makeProperty(['user_id' => $owner->id]);
    $otherProperty = makeProperty(['user_id' => $otherOwner->id]);

    PropertyView::factory()->count(1)->create(['property_id' => $ownProperty->id]);
    PropertyView::factory()->count(50)->create(['property_id' => $otherProperty->id]);

    $response = $this->actingAs($owner)->get(route('owner.analytics.index'));

    $response->assertViewHas('topViewed', function ($topViewed) use ($ownProperty, $otherProperty) {
        return $topViewed->pluck('id')->contains($ownProperty->id)
            && ! $topViewed->pluck('id')->contains($otherProperty->id);
    });
});

test('an admin visiting owner analytics only ever sees their own scoped (possibly empty) data', function () {
    $admin = staffTierUser();
    makeProperty(['status' => 'approved']);
    makeProperty(['status' => 'pending']);

    $response = $this->actingAs($admin)->get(route('owner.analytics.index'));

    $response->assertOk();
    $response->assertViewHas('overview', fn ($overview) => $overview['total_properties'] === 0);
});

test('owner date range filters scope the period stats correctly', function () {
    $owner = ownerTierUser();
    makeProperty(['user_id' => $owner->id, 'created_at' => now()->subDays(2)]);
    makeProperty(['user_id' => $owner->id, 'created_at' => now()->subDays(60)]);

    $response = $this->actingAs($owner)->get(route('owner.analytics.index', ['range' => 'last_7_days']));

    $response->assertViewHas('period', fn ($period) => $period['new_properties'] === 1);
});

test('an invalid custom date range is rejected for owners too', function () {
    $owner = ownerTierUser();

    $this->actingAs($owner)
        ->get(route('owner.analytics.index', ['range' => 'custom', 'from' => now()->toDateString(), 'to' => now()->subDays(3)->toDateString()]))
        ->assertSessionHasErrors('to');
});
