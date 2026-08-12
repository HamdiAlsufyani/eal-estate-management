<?php

use App\Models\Inquiry;
use App\Models\Property;
use App\Notifications\InquiryCreatedNotification;
use App\Notifications\InquiryStatusUpdatedNotification;
use App\Notifications\PropertyApprovedNotification;
use App\Notifications\PropertyRejectedNotification;
use App\Notifications\PropertySubmittedNotification;
use App\Notifications\UserRegisteredNotification;
use App\Services\InquiryService;
use App\Services\PropertyStatusService;

test('new user registration creates a notification for admins who can approve users', function () {
    $admin = adminUser(['users.approve']);
    $unrelatedStaff = adminUser(['properties.approve']);

    $this->post(route('register'), [
        'name' => 'New Customer',
        'email' => 'newcustomer@example.com',
        'phone' => '0511111111',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect($admin->fresh()->notifications()->where('type', UserRegisteredNotification::class)->count())->toBe(1);
    expect($unrelatedStaff->fresh()->notifications()->count())->toBe(0);

    $data = $admin->fresh()->notifications()->first()->data;
    expect($data['type'])->toBe('user_registered');
    expect($data['user_name'])->toBe('New Customer');
    expect($data['user_email'])->toBe('newcustomer@example.com');
});

test('an owner creating a property notifies staff who can approve properties', function () {
    $staff = adminUser(['properties.approve']);
    $owner = ownerTierUser();

    $this->actingAs($owner)->post(route('owner.properties.store'), propertyPayload(['title' => 'Owner Submitted Villa']));

    $property = Property::where('title_en', 'Owner Submitted Villa')->firstOrFail();

    expect($staff->fresh()->notifications()->where('type', PropertySubmittedNotification::class)->count())->toBe(1);

    $data = $staff->fresh()->notifications()->first()->data;
    expect($data['type'])->toBe('property_submitted');
    expect($data['property_id'])->toBe($property->id);
    expect($data['owner_name'])->toBe($owner->name);
    expect($data['url'])->toBe(route('admin.properties.show', $property));
});

test('admin creating a property directly does not notify anyone', function () {
    $admin = adminUser(['properties.approve', 'properties.view', 'properties.create']);

    $this->actingAs($admin)->post(route('admin.properties.store'), propertyPayload());

    expect($admin->fresh()->notifications()->count())->toBe(0);
});

test('approving a property notifies its owner', function () {
    $admin = adminUser(['properties.approve']);
    $property = makeProperty(['status' => 'pending']);
    $owner = $property->user;

    app(PropertyStatusService::class)->changeStatus($property, 'approved', null, $admin);

    expect($owner->fresh()->notifications()->where('type', PropertyApprovedNotification::class)->count())->toBe(1);

    $data = $owner->fresh()->notifications()->first()->data;
    expect($data['type'])->toBe('property_approved');
    expect($data['property_id'])->toBe($property->id);
    expect($data['url'])->toBe(route('owner.properties.show', $property));
});

test('rejecting a property notifies its owner and includes the rejection reason', function () {
    $admin = adminUser(['properties.approve']);
    $property = makeProperty(['status' => 'pending']);
    $owner = $property->user;

    app(PropertyStatusService::class)->changeStatus($property, 'rejected', 'Missing ownership documents.', $admin);

    $notification = $owner->fresh()->notifications()->where('type', PropertyRejectedNotification::class)->first();

    expect($notification)->not->toBeNull();
    expect($notification->data['reason'])->toBe('Missing ownership documents.');
});

test('a status transition that is not approved or rejected does not notify the owner', function () {
    $admin = adminUser(['properties.approve']);
    $property = makeProperty(['status' => 'rejected']);
    $owner = $property->user;

    app(PropertyStatusService::class)->changeStatus($property, 'pending', null, $admin);

    expect($owner->fresh()->notifications()->count())->toBe(0);
});

test('a new inquiry notifies the property owner but not the customer', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['user_id' => $owner->id, 'status' => 'approved']);
    $customer = activeUser();

    $this->actingAs($customer)->post(route('properties.inquiries.store', $property), [
        'phone' => '0500000000',
        'message' => 'I am interested in this property.',
    ]);

    $inquiry = Inquiry::where('property_id', $property->id)->firstOrFail();

    expect($owner->fresh()->notifications()->where('type', InquiryCreatedNotification::class)->count())->toBe(1);
    expect($customer->fresh()->notifications()->count())->toBe(0);

    $data = $owner->fresh()->notifications()->first()->data;
    expect($data['type'])->toBe('inquiry_created');
    expect($data['customer_name'])->toBe($customer->name);
    expect($data['url'])->toBe(route('owner.inquiries.show', $inquiry));
});

test('changing an inquiry status notifies the customer who submitted it', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['user_id' => $owner->id]);
    $customer = activeUser();
    $inquiry = Inquiry::create([
        'property_id' => $property->id,
        'user_id' => $customer->id,
        'phone' => '0500000000',
        'message' => 'I am interested.',
        'status' => 'new',
    ]);

    app(InquiryService::class)->updateStatus($inquiry, 'closed');

    expect($customer->fresh()->notifications()->where('type', InquiryStatusUpdatedNotification::class)->count())->toBe(1);

    $data = $customer->fresh()->notifications()->first()->data;
    expect($data['type'])->toBe('inquiry_status_updated');
    expect($data['status'])->toBe('closed');
    expect($data['status_label'])->toBe(__('inquiries.status.closed'));
});

test('setting an inquiry to the same status again does not send a duplicate notification', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['user_id' => $owner->id]);
    $customer = activeUser();
    $inquiry = Inquiry::create([
        'property_id' => $property->id,
        'user_id' => $customer->id,
        'phone' => '0500000000',
        'message' => 'I am interested.',
        'status' => 'new',
    ]);

    $service = app(InquiryService::class);
    $service->updateStatus($inquiry, 'new');

    expect($customer->fresh()->notifications()->count())->toBe(0);
});

test('notification content is stored in english when the recipient prefers english', function () {
    $admin = adminUser(['properties.approve']);
    $admin->update(['locale' => 'en']);
    $property = makeProperty(['status' => 'pending', 'title_en' => 'Sunny Apartment']);

    app(PropertyStatusService::class)->changeStatus($property, 'approved', null, $admin);

    $notification = $property->user->fresh()->notifications()->first();

    expect($notification->data['title'])->toBe('Your property has been approved.');
});

test('notification content is stored in arabic when the recipient prefers arabic', function () {
    $owner = ownerTierUser();
    $owner->update(['locale' => 'ar']);
    $property = makeProperty(['status' => 'pending', 'user_id' => $owner->id, 'title_ar' => 'شقة مشمسة']);
    $admin = adminUser(['properties.approve']);

    app(PropertyStatusService::class)->changeStatus($property, 'approved', null, $admin);

    $notification = $owner->fresh()->notifications()->first();

    expect($notification->data['title'])->toBe('تمت الموافقة على عقارك.');
    expect($notification->data['message'])->toContain('شقة مشمسة');
});
