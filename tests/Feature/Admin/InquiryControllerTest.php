<?php

use App\Models\Inquiry;

test('guests are redirected to login', function () {
    $this->get(route('admin.inquiries.index'))->assertRedirect(route('login'));
});

test('users without permission cannot access inquiries', function () {
    $user = activeUser();

    $this->actingAs($user)
        ->get(route('admin.inquiries.index'))
        ->assertForbidden();
});

test('admin can view the inquiries index across all owners', function () {
    $admin = adminUser(['inquiries.view']);
    $ownerA = activeUser();
    $ownerB = activeUser();
    $propertyA = makeProperty(['user_id' => $ownerA->id]);
    $propertyB = makeProperty(['user_id' => $ownerB->id]);

    Inquiry::create(['property_id' => $propertyA->id, 'user_id' => activeUser()->id, 'phone' => '0500000001', 'message' => 'From property A', 'status' => 'new']);
    Inquiry::create(['property_id' => $propertyB->id, 'user_id' => activeUser()->id, 'phone' => '0500000002', 'message' => 'From property B', 'status' => 'new']);

    $this->actingAs($admin)
        ->get(route('admin.inquiries.index'))
        ->assertOk()
        ->assertSee('From property A')
        ->assertSee('From property B');
});

test('admin can search inquiries by phone', function () {
    $admin = adminUser(['inquiries.view']);
    $property = makeProperty();

    Inquiry::create(['property_id' => $property->id, 'user_id' => activeUser()->id, 'phone' => '0511112222', 'message' => 'Findable', 'status' => 'new']);
    Inquiry::create(['property_id' => $property->id, 'user_id' => activeUser()->id, 'phone' => '0599998888', 'message' => 'Not findable', 'status' => 'new']);

    $this->actingAs($admin)
        ->get(route('admin.inquiries.index', ['search' => '0511112222']))
        ->assertOk()
        ->assertSee('Findable')
        ->assertDontSee('Not findable');
});

test('admin can filter inquiries by status', function () {
    $admin = adminUser(['inquiries.view']);
    $property = makeProperty();

    Inquiry::create(['property_id' => $property->id, 'user_id' => activeUser()->id, 'phone' => '0500000000', 'message' => 'New one', 'status' => 'new']);
    Inquiry::create(['property_id' => $property->id, 'user_id' => activeUser()->id, 'phone' => '0500000000', 'message' => 'Closed one', 'status' => 'closed']);

    $this->actingAs($admin)
        ->get(route('admin.inquiries.index', ['status' => 'closed']))
        ->assertOk()
        ->assertSee('Closed one')
        ->assertDontSee('New one');
});

test('inquiries index paginates at 15 per page', function () {
    $admin = adminUser(['inquiries.view']);
    $property = makeProperty();

    foreach (range(1, 16) as $i) {
        Inquiry::create(['property_id' => $property->id, 'user_id' => activeUser()->id, 'phone' => '0500000000', 'message' => "Message {$i}", 'status' => 'new']);
    }

    $response = $this->actingAs($admin)->get(route('admin.inquiries.index'));

    $response->assertOk()->assertViewHas('inquiries', fn ($inquiries) => $inquiries->count() === 15 && $inquiries->total() === 16);
});

test('admin without inquiries.manage cannot update status', function () {
    $admin = adminUser(['inquiries.view']);
    $property = makeProperty();
    $inquiry = Inquiry::create(['property_id' => $property->id, 'user_id' => activeUser()->id, 'phone' => '0500000000', 'message' => 'Hello', 'status' => 'new']);

    $this->actingAs($admin)
        ->patch(route('admin.inquiries.status', $inquiry), ['status' => 'closed'])
        ->assertForbidden();
});

test('admin with inquiries.manage can update status', function () {
    $admin = adminUser(['inquiries.view', 'inquiries.manage']);
    $property = makeProperty();
    $inquiry = Inquiry::create(['property_id' => $property->id, 'user_id' => activeUser()->id, 'phone' => '0500000000', 'message' => 'Hello', 'status' => 'new']);

    $this->actingAs($admin)
        ->patch(route('admin.inquiries.status', $inquiry), ['status' => 'closed'])
        ->assertRedirect();

    expect($inquiry->refresh()->status)->toBe('closed');
});
