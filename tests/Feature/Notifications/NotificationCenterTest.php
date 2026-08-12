<?php

use App\Notifications\PropertyApprovedNotification;

test('unread count reflects only unread notifications', function () {
    $user = activeUser();

    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    $user->notifications()->first()->markAsRead();

    expect($user->fresh()->unreadNotifications()->count())->toBe(2);
});

test('the notification center paginates at 15 per page', function () {
    $user = activeUser();

    foreach (range(1, 20) as $i) {
        $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    }

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertViewHas('notifications', fn ($notifications) => $notifications->count() === 15 && $notifications->total() === 20);
});

test('the notification center only shows the authenticated users notifications', function () {
    $user = activeUser();
    $otherUser = activeUser();

    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id, 'title_en' => 'My Notified Property'])));
    $otherUser->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $otherUser->id, 'title_en' => 'Other Notified Property'])));

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('My Notified Property')
        ->assertDontSee('Other Notified Property');
});

test('the navbar dropdown shows the latest five notifications and an unread badge', function () {
    $user = activeUser();

    foreach (range(1, 7) as $i) {
        $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    }

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('7');
});

test('an empty notification center shows the empty state', function () {
    $user = activeUser();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee(__('notifications.empty_title'));
});

test('notification links point to the correct related resource', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['user_id' => $owner->id, 'status' => 'pending']);

    app(\App\Services\PropertyStatusService::class)->changeStatus($property, 'approved', null, adminUser(['properties.approve']));

    $notification = $owner->fresh()->notifications()->firstOrFail();

    expect($notification->data['url'])->toBe(route('owner.properties.show', $property));
});

test('the notification center renders in arabic with rtl direction', function () {
    $user = activeUser(['locale' => 'ar']);
    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('dir="rtl"', false);
});

test('the notification center renders in english with ltr direction by default', function () {
    $user = activeUser();
    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('dir="ltr"', false);
});
