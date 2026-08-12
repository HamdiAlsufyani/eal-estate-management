<?php

use App\Models\City;
use App\Models\Favorite;
use App\Models\Inquiry;
use App\Models\PropertyView;
use Database\Seeders\RolePermissionSeeder;

test('guests are redirected to login', function () {
    $this->get(route('admin.analytics.index'))->assertRedirect(route('login'));
});

test('customers cannot access admin analytics', function () {
    $customer = activeUser();

    $this->actingAs($customer)->get(route('admin.analytics.index'))->assertForbidden();
});

test('owners cannot access admin analytics', function () {
    $owner = ownerTierUser();

    $this->actingAs($owner)->get(route('admin.analytics.index'))->assertForbidden();
});

test('staff without the analytics permission cannot access admin analytics', function () {
    $staff = staffTierUser();

    $this->actingAs($staff)->get(route('admin.analytics.index'))->assertForbidden();
});

test('staff granted the analytics permission can access admin analytics', function () {
    $staff = adminUser(['analytics.view']);

    $this->actingAs($staff)
        ->get(route('admin.analytics.index'))
        ->assertOk()
        ->assertViewIs('admin.analytics.index');
});

test('admin role users can access admin analytics', function () {
    test()->seed(RolePermissionSeeder::class);
    $admin = activeUser();
    $admin->assignRole('Admin');

    $this->actingAs($admin)->get(route('admin.analytics.index'))->assertOk();
});

test('lifetime overview totals are correct and reports are not scoped to any owner', function () {
    $staff = adminUser(['analytics.view']);
    makeProperty(['status' => 'approved']);
    makeProperty(['status' => 'pending']);
    makeProperty(['status' => 'rejected']);
    makeProperty(['status' => 'approved']);

    $response = $this->actingAs($staff)->get(route('admin.analytics.index'));

    $response->assertOk();
    $response->assertViewHas('overview', function ($overview) {
        return $overview['total_properties'] === 4
            && $overview['approved_properties'] === 2
            && $overview['pending_properties'] === 1
            && $overview['rejected_properties'] === 1;
    });
});

test('sale vs rent purpose breakdown is correct', function () {
    $staff = adminUser(['analytics.view']);
    makeProperty(['purpose' => 'sale']);
    makeProperty(['purpose' => 'sale']);
    makeProperty(['purpose' => 'rent']);

    $response = $this->actingAs($staff)->get(route('admin.analytics.index'));

    $response->assertViewHas('purposeBreakdown', fn ($breakdown) => $breakdown['sale'] === 2 && $breakdown['rent'] === 1);
});

test('view, favorite and inquiry totals are correct', function () {
    $staff = adminUser(['analytics.view']);
    $property = makeProperty();

    PropertyView::factory()->count(3)->create(['property_id' => $property->id]);
    Favorite::factory()->create(['user_id' => activeUser()->id, 'property_id' => $property->id]);
    Inquiry::factory()->create([
        'property_id' => $property->id,
        'user_id' => activeUser()->id,
        'phone' => '0500000001',
        'message' => 'Interested',
    ]);

    $response = $this->actingAs($staff)->get(route('admin.analytics.index'));

    $response->assertViewHas('overview', function ($overview) {
        return $overview['total_property_views'] === 3
            && $overview['total_favorites'] === 1
            && $overview['total_inquiries'] === 1;
    });
});

test('most viewed properties are ranked correctly', function () {
    $staff = adminUser(['analytics.view']);
    $popular = makeProperty(['title' => 'Popular Villa']);
    $quiet = makeProperty(['title' => 'Quiet Villa']);

    PropertyView::factory()->count(5)->create(['property_id' => $popular->id]);
    PropertyView::factory()->count(1)->create(['property_id' => $quiet->id]);

    $response = $this->actingAs($staff)->get(route('admin.analytics.index'));

    $response->assertViewHas('topViewed', function ($topViewed) use ($popular) {
        return $topViewed->first()->id === $popular->id && $topViewed->first()->views_count === 5;
    });
});

test('city analytics are correct', function () {
    $staff = adminUser(['analytics.view']);
    $city = City::factory()->create();
    $property = makeProperty(['city_id' => $city->id]);
    makeProperty(['city_id' => $city->id]);

    PropertyView::factory()->count(2)->create(['property_id' => $property->id]);

    $response = $this->actingAs($staff)->get(route('admin.analytics.index'));

    $response->assertViewHas('cities', function ($cities) use ($city) {
        $row = $cities->firstWhere(fn ($row) => $row['city']->id === $city->id);

        return $row && $row['properties_count'] === 2 && $row['views_count'] === 2;
    });
});

test('date range presets scope the period stats without affecting lifetime totals', function () {
    $staff = adminUser(['analytics.view']);
    makeProperty(['created_at' => now()->subDays(2)]);
    makeProperty(['created_at' => now()->subDays(60)]);

    $response = $this->actingAs($staff)->get(route('admin.analytics.index', ['range' => 'last_7_days']));

    $response->assertViewHas('period', fn ($period) => $period['new_properties'] === 1);
    $response->assertViewHas('overview', fn ($overview) => $overview['total_properties'] === 2);
});

test('a valid custom date range is accepted', function () {
    $staff = adminUser(['analytics.view']);

    $this->actingAs($staff)
        ->get(route('admin.analytics.index', ['range' => 'custom', 'from' => now()->subDays(5)->toDateString(), 'to' => now()->toDateString()]))
        ->assertOk();
});

test('an invalid custom date range is rejected', function () {
    $staff = adminUser(['analytics.view']);

    $this->actingAs($staff)
        ->get(route('admin.analytics.index', ['range' => 'custom', 'from' => now()->toDateString(), 'to' => now()->subDays(5)->toDateString()]))
        ->assertSessionHasErrors('to');
});

test('the analytics page renders correctly in arabic with rtl direction', function () {
    $staff = adminUser(['analytics.view']);
    $staff->update(['locale' => 'ar']);

    $response = $this->actingAs($staff)->get(route('admin.analytics.index'));

    $response->assertOk();
    $response->assertSee('dir="rtl"', false);
    $response->assertSee(__('analytics.title', [], 'ar'));
});
