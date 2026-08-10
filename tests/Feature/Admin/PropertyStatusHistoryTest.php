<?php

use App\Models\PropertyStatusHistory;
use App\Services\PropertyStatusService;

test('guests are redirected to login', function () {
    $property = makeProperty();

    $this->post(route('admin.properties.status', $property), ['status' => 'approved'])
        ->assertRedirect(route('login'));
});

test('users without the approve permission cannot change status', function () {
    $user = activeUser();
    $property = makeProperty(['status' => 'pending']);

    $this->actingAs($user)
        ->post(route('admin.properties.status', $property), ['status' => 'approved'])
        ->assertForbidden();

    expect($property->fresh()->status)->toBe('pending');
});

test('admin can change a property status and a history record is created', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty(['status' => 'pending']);

    $this->actingAs($admin)
        ->post(route('admin.properties.status', $property), [
            'status' => 'approved',
            'reason' => 'Documents verified',
        ])
        ->assertRedirect();

    $property->refresh();
    expect($property->status)->toBe('approved');
    expect($property->statusHistories()->count())->toBe(1);

    $history = $property->statusHistories()->first();
    expect($history->old_status)->toBe('pending');
    expect($history->new_status)->toBe('approved');
    expect($history->user_id)->toBe($admin->id);
    expect($history->reason)->toBe('Documents verified');
});

test('rejecting a property requires a reason', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty(['status' => 'pending']);

    $this->actingAs($admin)
        ->post(route('admin.properties.status', $property), ['status' => 'rejected'])
        ->assertSessionHasErrors('reason');

    expect($property->fresh()->status)->toBe('pending');
    expect($property->statusHistories()->count())->toBe(0);
});

test('rejecting a property with a reason records it in the history', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty(['status' => 'pending']);

    $this->actingAs($admin)->post(route('admin.properties.status', $property), [
        'status' => 'rejected',
        'reason' => 'Property documents are incomplete.',
    ])->assertRedirect();

    $history = $property->fresh()->statusHistories()->latest()->first();
    expect($history->new_status)->toBe('rejected');
    expect($history->reason)->toBe('Property documents are incomplete.');
});

test('invalid status transitions are rejected', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty(['status' => 'approved']);

    $this->actingAs($admin)
        ->post(route('admin.properties.status', $property), ['status' => 'pending'])
        ->assertSessionHasErrors('status');

    expect($property->fresh()->status)->toBe('approved');
    expect($property->statusHistories()->count())->toBe(0);
});

test('changing to the same status is rejected', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty(['status' => 'approved']);

    $this->actingAs($admin)
        ->post(route('admin.properties.status', $property), ['status' => 'approved'])
        ->assertSessionHasErrors('status');

    expect($property->statusHistories()->count())->toBe(0);
});

test('rejected properties can be moved back to pending or approved', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty(['status' => 'rejected']);

    $this->actingAs($admin)
        ->post(route('admin.properties.status', $property), ['status' => 'pending'])
        ->assertRedirect();

    expect($property->fresh()->status)->toBe('pending');
});

test('status change and history creation roll back together on failure', function () {
    $property = makeProperty(['status' => 'pending']);
    $actor = activeUser();
    $actorId = $actor->id;
    $actor->delete();

    $service = app(PropertyStatusService::class);

    expect(fn () => $service->changeStatus($property, 'approved', null, $actor))
        ->toThrow(Exception::class);

    expect($property->fresh()->status)->toBe('pending');
    expect(PropertyStatusHistory::where('property_id', $property->id)->count())->toBe(0);
    expect(\App\Models\User::find($actorId))->toBeNull();
});

test('property show page displays the status history', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty(['status' => 'pending']);
    $service = app(PropertyStatusService::class);
    $service->changeStatus($property, 'approved', 'Looks good', $admin);

    $this->actingAs($admin)
        ->get(route('admin.properties.show', $property))
        ->assertOk()
        ->assertViewIs('admin.properties.show')
        ->assertViewHas('statusHistories', fn ($histories) => $histories->total() === 1)
        ->assertSee('Looks good');
});

test('soft deleting a property keeps its status history intact', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty(['status' => 'pending']);
    $service = app(PropertyStatusService::class);
    $service->changeStatus($property, 'approved', null, $admin);

    expect(PropertyStatusHistory::where('property_id', $property->id)->count())->toBe(1);

    $property->delete();

    expect(\App\Models\Property::withTrashed()->find($property->id)->trashed())->toBeTrue();
    expect(PropertyStatusHistory::where('property_id', $property->id)->count())->toBe(1);
});

test('force deleting a property cascades and removes its status history', function () {
    $admin = adminUser(['properties.view', 'properties.approve']);
    $property = makeProperty(['status' => 'pending']);
    $service = app(PropertyStatusService::class);
    $service->changeStatus($property, 'approved', null, $admin);

    expect(PropertyStatusHistory::where('property_id', $property->id)->count())->toBe(1);

    $property->forceDelete();

    expect(PropertyStatusHistory::where('property_id', $property->id)->count())->toBe(0);
});
