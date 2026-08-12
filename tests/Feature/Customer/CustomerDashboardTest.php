<?php

use App\Models\Favorite;
use App\Models\PropertyView;

test('guest visiting the customer dashboard is redirected to login', function () {
    $this->get(route('customer.dashboard'))->assertRedirect(route('login'));
});

test('a plain customer can access the customer dashboard', function () {
    $customer = activeUser();

    $this->actingAs($customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertViewIs('customer.dashboard');
});

test('the centralized dashboard route redirects a plain customer to the customer dashboard', function () {
    $customer = activeUser();

    $this->actingAs($customer)
        ->get(route('dashboard'))
        ->assertRedirect(route('customer.dashboard'));
});

test('dashboard statistics are scoped to the authenticated customer only', function () {
    $customer = activeUser();
    $otherCustomer = activeUser();

    Favorite::create(['user_id' => $customer->id, 'property_id' => makeProperty()->id]);
    Favorite::create(['user_id' => $customer->id, 'property_id' => makeProperty()->id]);
    Favorite::create(['user_id' => $otherCustomer->id, 'property_id' => makeProperty()->id]);

    makeInquiry(['user_id' => $customer->id, 'status' => 'new']);
    makeInquiry(['user_id' => $customer->id, 'status' => 'read']);
    makeInquiry(['user_id' => $customer->id, 'status' => 'closed']);
    makeInquiry(['user_id' => $otherCustomer->id, 'status' => 'closed']);

    $viewedProperty = makeProperty();
    PropertyView::create(['user_id' => $customer->id, 'property_id' => $viewedProperty->id]);
    PropertyView::create(['user_id' => $otherCustomer->id, 'property_id' => makeProperty()->id]);

    $customer->notify(new \App\Notifications\PropertyApprovedNotification(makeProperty(['user_id' => $customer->id])));

    $this->actingAs($customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertViewHas('stats', function ($stats) {
            return $stats['total_favorites'] === 2
                && $stats['active_inquiries'] === 2
                && $stats['completed_inquiries'] === 1
                && $stats['recently_viewed'] === 1
                && $stats['unread_notifications'] === 1;
        });
});

test('recent favorites section only shows the current customers favorites', function () {
    $customer = activeUser();
    $mine = makeProperty(['title' => 'My Favorite Villa']);
    $notMine = makeProperty(['title' => 'Someone Elses Villa']);

    Favorite::create(['user_id' => $customer->id, 'property_id' => $mine->id]);
    Favorite::create(['user_id' => activeUser()->id, 'property_id' => $notMine->id]);

    $this->actingAs($customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('My Favorite Villa')
        ->assertDontSee('Someone Elses Villa');
});

test('recent inquiries section only shows the current customers inquiries', function () {
    $customer = activeUser();
    $mine = makeProperty(['title' => 'My Inquired Property']);
    $notMine = makeProperty(['title' => 'Someone Elses Inquired Property']);

    makeInquiry(['user_id' => $customer->id, 'property_id' => $mine->id]);
    makeInquiry(['property_id' => $notMine->id]);

    $this->actingAs($customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('My Inquired Property')
        ->assertDontSee('Someone Elses Inquired Property');
});

test('notifications section only shows the current customers notifications', function () {
    $customer = activeUser();
    $otherCustomer = activeUser();

    $customer->notify(new \App\Notifications\PropertyApprovedNotification(makeProperty(['user_id' => $customer->id])));
    $otherCustomer->notify(new \App\Notifications\PropertyApprovedNotification(makeProperty(['user_id' => $otherCustomer->id])));

    $response = $this->actingAs($customer)->get(route('customer.dashboard'))->assertOk();

    expect($response->viewData('latestNotifications'))->toHaveCount(1);
    expect($response->viewData('latestNotifications')->first()->notifiable_id)->toBe($customer->id);
});

test('dashboard shows empty states when the customer has no data yet', function () {
    $customer = activeUser();

    $this->actingAs($customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee(__('customer.no_favorites'))
        ->assertSee(__('customer.no_inquiries'))
        ->assertSee(__('customer.no_notifications'))
        ->assertSee(__('customer.no_recently_viewed'));
});

test('recently viewed section on the dashboard only shows the current customers own views', function () {
    $customer = activeUser();
    $mine = makeProperty(['title' => 'Viewed From Dashboard By Me']);
    $notMine = makeProperty(['title' => 'Viewed From Dashboard By Someone Else']);

    PropertyView::create(['user_id' => $customer->id, 'property_id' => $mine->id]);
    PropertyView::create(['user_id' => activeUser()->id, 'property_id' => $notMine->id]);

    $this->actingAs($customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('Viewed From Dashboard By Me')
        ->assertDontSee('Viewed From Dashboard By Someone Else');
});

test('customer sidebar never exposes admin or owner only routes', function () {
    $customer = activeUser();

    $response = $this->actingAs($customer)->get(route('customer.dashboard'));

    $response->assertDontSee(route('admin.users.index'));
    $response->assertDontSee(route('owner.properties.index'));
});
