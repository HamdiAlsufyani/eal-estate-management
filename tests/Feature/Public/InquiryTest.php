<?php

use App\Models\Inquiry;

test('guest attempting to submit an inquiry is redirected to login', function () {
    $property = makeProperty();

    $this->post(route('properties.inquiries.store', $property), [
        'phone' => '0500000000',
        'message' => 'I am interested.',
    ])->assertRedirect(route('login'));

    expect(Inquiry::count())->toBe(0);
});

test('authenticated user can submit an inquiry for an approved property', function () {
    $user = activeUser();
    $property = makeProperty();

    $this->actingAs($user)->post(route('properties.inquiries.store', $property), [
        'phone' => '0500000000',
        'message' => 'I am interested in this property.',
    ])->assertRedirect();

    $inquiry = Inquiry::first();
    expect($inquiry)->not->toBeNull();
    expect($inquiry->user_id)->toBe($user->id);
    expect($inquiry->property_id)->toBe($property->id);
    expect($inquiry->status)->toBe('new');
});

test('inquiry requires phone and message', function () {
    $user = activeUser();
    $property = makeProperty();

    $this->actingAs($user)
        ->post(route('properties.inquiries.store', $property), [])
        ->assertSessionHasErrors(['phone', 'message']);
});

test('the inquiry property id always comes from the route, not client input', function () {
    $user = activeUser();
    $property = makeProperty();
    $otherProperty = makeProperty();

    $this->actingAs($user)->post(route('properties.inquiries.store', $property), [
        'phone' => '0500000000',
        'message' => 'Please contact me.',
        'property_id' => $otherProperty->id,
    ]);

    $inquiry = Inquiry::first();
    expect($inquiry->property_id)->toBe($property->id);
});

test('an inquiry cannot be submitted for a pending or rejected property', function () {
    $user = activeUser();
    $property = makeProperty(['status' => 'rejected']);

    $this->actingAs($user)->post(route('properties.inquiries.store', $property), [
        'phone' => '0500000000',
        'message' => 'Please contact me.',
    ])->assertNotFound();

    expect(Inquiry::count())->toBe(0);
});

test('a duplicate active inquiry for the same property is blocked', function () {
    $user = activeUser();
    $property = makeProperty();

    Inquiry::create([
        'user_id' => $user->id,
        'property_id' => $property->id,
        'phone' => '0500000000',
        'message' => 'First message.',
        'status' => 'new',
    ]);

    $this->actingAs($user)->post(route('properties.inquiries.store', $property), [
        'phone' => '0500000000',
        'message' => 'Second message.',
    ])->assertRedirect();

    expect(Inquiry::count())->toBe(1);
});

test('a new inquiry is allowed once the previous one is closed', function () {
    $user = activeUser();
    $property = makeProperty();

    Inquiry::create([
        'user_id' => $user->id,
        'property_id' => $property->id,
        'phone' => '0500000000',
        'message' => 'First message.',
        'status' => 'closed',
    ]);

    $this->actingAs($user)->post(route('properties.inquiries.store', $property), [
        'phone' => '0500000000',
        'message' => 'Second message.',
    ])->assertRedirect();

    expect(Inquiry::count())->toBe(2);
});
