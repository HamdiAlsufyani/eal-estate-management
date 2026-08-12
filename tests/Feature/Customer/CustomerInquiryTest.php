<?php

test('guest visiting the customer inquiries index is redirected to login', function () {
    $this->get(route('customer.inquiries.index'))->assertRedirect(route('login'));
});

test('guest visiting a customer inquiry is redirected to login', function () {
    $inquiry = makeInquiry();

    $this->get(route('customer.inquiries.show', $inquiry))->assertRedirect(route('login'));
});

test('customer inquiries index only lists the current customers own inquiries', function () {
    $customer = activeUser();

    makeInquiry(['user_id' => $customer->id, 'message' => 'My own inquiry message']);
    makeInquiry(['message' => 'A different customers inquiry message']);

    $this->actingAs($customer)
        ->get(route('customer.inquiries.index'))
        ->assertOk()
        ->assertSee('My own inquiry message')
        ->assertDontSee('A different customers inquiry message');
});

test('customer inquiries index paginates 15 per page', function () {
    $customer = activeUser();

    foreach (range(1, 16) as $i) {
        makeInquiry(['user_id' => $customer->id]);
    }

    $response = $this->actingAs($customer)->get(route('customer.inquiries.index'))->assertOk();

    expect($response->viewData('inquiries'))->toHaveCount(15);
    expect($response->viewData('inquiries')->hasMorePages())->toBeTrue();
});

test('a customer can view their own inquiry', function () {
    $customer = activeUser();
    $inquiry = makeInquiry(['user_id' => $customer->id]);

    $this->actingAs($customer)
        ->get(route('customer.inquiries.show', $inquiry))
        ->assertOk()
        ->assertViewIs('customer.inquiries.show');
});

test('a customer cannot view another customers inquiry', function () {
    $customer = activeUser();
    $othersInquiry = makeInquiry();

    $this->actingAs($customer)
        ->get(route('customer.inquiries.show', $othersInquiry))
        ->assertForbidden();
});
