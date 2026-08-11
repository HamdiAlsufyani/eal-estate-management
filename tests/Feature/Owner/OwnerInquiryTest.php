<?php

use App\Models\Inquiry;

function makeInquiry(array $overrides = []): Inquiry
{
    $property = $overrides['property_id'] ?? null;

    return Inquiry::create(array_merge([
        'property_id' => $property ? $overrides['property_id'] : makeProperty()->id,
        'user_id' => activeUser()->id,
        'phone' => '0500000000',
        'message' => 'I am interested.',
        'status' => 'new',
    ], $overrides));
}

test('guests are redirected to login', function () {
    $inquiry = makeInquiry();

    $this->get(route('owner.inquiries.show', $inquiry))->assertRedirect(route('login'));
});

test('owner sees only inquiries for their own properties in the index', function () {
    $owner = ownerTierUser();
    $mine = makeProperty(['user_id' => $owner->id]);
    $notMine = makeProperty();

    makeInquiry(['property_id' => $mine->id, 'message' => 'About my listing']);
    makeInquiry(['property_id' => $notMine->id, 'message' => 'About someone elses listing']);

    $this->actingAs($owner)
        ->get(route('owner.inquiries.index'))
        ->assertOk()
        ->assertSee('About my listing')
        ->assertDontSee('About someone elses listing');
});

test('owner can view an inquiry for their own property', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['user_id' => $owner->id]);
    $inquiry = makeInquiry(['property_id' => $property->id]);

    $this->actingAs($owner)
        ->get(route('owner.inquiries.show', $inquiry))
        ->assertOk();
});

test('owner cannot view another owners inquiry by guessing the url', function () {
    $owner = ownerTierUser();
    $inquiry = makeInquiry();

    $this->actingAs($owner)
        ->get(route('owner.inquiries.show', $inquiry))
        ->assertForbidden();
});

test('owner cannot update the status of another owners inquiry', function () {
    $owner = ownerTierUser();
    $inquiry = makeInquiry();

    $this->actingAs($owner)
        ->patch(route('owner.inquiries.status', $inquiry), ['status' => 'closed'])
        ->assertForbidden();

    expect($inquiry->refresh()->status)->toBe('new');
});

test('owner can update the status of an inquiry for their own property', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['user_id' => $owner->id]);
    $inquiry = makeInquiry(['property_id' => $property->id]);

    $this->actingAs($owner)
        ->patch(route('owner.inquiries.status', $inquiry), ['status' => 'read'])
        ->assertRedirect();

    expect($inquiry->refresh()->status)->toBe('read');
});

test('a customer can view their own submitted inquiry via the policy', function () {
    $customer = activeUser();
    $property = makeProperty();
    $inquiry = makeInquiry(['property_id' => $property->id, 'user_id' => $customer->id]);

    expect($customer->can('view', $inquiry))->toBeTrue();
});

test('a customer cannot view another customers inquiry via the policy', function () {
    $customer = activeUser();
    $otherCustomerInquiry = makeInquiry();

    expect($customer->can('view', $otherCustomerInquiry))->toBeFalse();
});
