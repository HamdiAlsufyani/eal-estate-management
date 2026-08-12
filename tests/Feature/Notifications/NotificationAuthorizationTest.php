<?php

use App\Models\User;
use App\Notifications\PropertyApprovedNotification;
use Illuminate\Notifications\DatabaseNotification;

test('guests cannot access the notification center', function () {
    $this->get(route('notifications.index'))->assertRedirect(route('login'));
});

test('guests cannot mark a notification as read or delete it', function () {
    $user = activeUser();
    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    $notification = $user->notifications()->firstOrFail();

    $this->post(route('notifications.read', $notification))->assertRedirect(route('login'));
    $this->delete(route('notifications.destroy', $notification))->assertRedirect(route('login'));
});

test('a notification belongs to the recipient it was sent to', function () {
    $user = activeUser();
    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    $notification = $user->notifications()->firstOrFail();

    expect($notification->notifiable_id)->toBe($user->id);
    expect($notification->notifiable_type)->toBe(User::class);
});

test('a user cannot mark another users notification as read', function () {
    $owner = activeUser();
    $intruder = activeUser();
    $owner->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $owner->id])));
    $notification = $owner->notifications()->firstOrFail();

    $this->actingAs($intruder)
        ->post(route('notifications.read', $notification))
        ->assertForbidden();

    expect($notification->fresh()->read_at)->toBeNull();
});

test('a user cannot delete another users notification', function () {
    $owner = activeUser();
    $intruder = activeUser();
    $owner->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $owner->id])));
    $notification = $owner->notifications()->firstOrFail();

    $this->actingAs($intruder)
        ->delete(route('notifications.destroy', $notification))
        ->assertForbidden();

    expect(DatabaseNotification::find($notification->id))->not->toBeNull();
});

test('a user can mark their own notification as read and gets redirected to its url', function () {
    $user = activeUser();
    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    $notification = $user->notifications()->firstOrFail();

    $this->actingAs($user)
        ->post(route('notifications.read', $notification))
        ->assertRedirect($notification->data['url']);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('a user can delete their own notification', function () {
    $user = activeUser();
    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    $notification = $user->notifications()->firstOrFail();

    $this->actingAs($user)
        ->delete(route('notifications.destroy', $notification))
        ->assertRedirect();

    expect(DatabaseNotification::find($notification->id))->toBeNull();
});

test('a user can mark all of their own notifications as read without affecting others', function () {
    $user = activeUser();
    $otherUser = activeUser();

    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    $user->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $user->id])));
    $otherUser->notify(new PropertyApprovedNotification(makeProperty(['user_id' => $otherUser->id])));
    $otherNotification = $otherUser->notifications()->firstOrFail();

    $this->actingAs($user)->post(route('notifications.read-all'))->assertRedirect();

    expect($user->fresh()->unreadNotifications()->count())->toBe(0);
    expect($otherNotification->fresh()->read_at)->toBeNull();
});
