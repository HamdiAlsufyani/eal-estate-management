<?php

use App\Models\Amenity;
use App\Models\City;
use App\Models\District;
use App\Models\Property;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests are redirected to login', function () {
    $this->get(route('owner.properties.index'))->assertRedirect(route('login'));
});

test('unauthorized users receive 403', function () {
    $user = activeUser();

    $this->actingAs($user)->get(route('owner.properties.index'))->assertForbidden();
    $this->actingAs($user)->get(route('owner.properties.create'))->assertForbidden();
});

test('owner sees only their own properties in the list', function () {
    $owner = ownerTierUser();
    $mine = makeProperty(['user_id' => $owner->id, 'title' => 'My Own Listing']);
    makeProperty(['title' => 'Someone Elses Listing']);

    $this->actingAs($owner)
        ->get(route('owner.properties.index'))
        ->assertOk()
        ->assertViewHas('properties', fn ($properties) => $properties->total() === 1 && $properties->first()->is($mine))
        ->assertSee('My Own Listing')
        ->assertDontSee('Someone Elses Listing');
});

test('owner cannot view another owners property', function () {
    $owner = ownerTierUser();
    $other = makeProperty();

    $this->actingAs($owner)
        ->get(route('owner.properties.show', $other))
        ->assertForbidden();
});

test('owner cannot edit another owners property even by guessing the id in the url', function () {
    $owner = ownerTierUser();
    $other = makeProperty(['status' => 'pending']);
    $originalTitle = $other->title;

    $this->actingAs($owner)->get(route('owner.properties.edit', $other))->assertForbidden();
    $this->actingAs($owner)->put(route('owner.properties.update', $other), propertyPayload(['title' => 'Hijacked Title']))->assertForbidden();

    expect($other->fresh()->title)->toBe($originalTitle);
});

test('owner cannot delete another owners property', function () {
    $owner = ownerTierUser();
    $other = makeProperty(['status' => 'pending']);

    $this->actingAs($owner)
        ->delete(route('owner.properties.destroy', $other))
        ->assertForbidden();

    expect(Property::find($other->id))->not->toBeNull();
});

test('owner can create a property and it is assigned to them', function () {
    $owner = ownerTierUser();

    $response = $this->actingAs($owner)->post(route('owner.properties.store'), propertyPayload(['title' => 'Owner Created Listing']));

    $property = Property::firstWhere('title', 'Owner Created Listing');
    $response->assertRedirect(route('owner.properties.show', $property));
    expect($property->user_id)->toBe($owner->id);
});

test('owner created property always starts as pending', function () {
    $owner = ownerTierUser();

    $this->actingAs($owner)->post(route('owner.properties.store'), propertyPayload(['status' => 'approved']));

    $property = Property::where('user_id', $owner->id)->latest()->first();
    expect($property->status)->toBe('pending');
});

test('owner cannot approve a property', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'pending', 'user_id' => $owner->id]);

    $this->actingAs($owner)
        ->post(route('admin.properties.status', $property), ['status' => 'approved'])
        ->assertForbidden();

    expect($property->fresh()->status)->toBe('pending');
});

test('owner cannot reject a property', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'pending', 'user_id' => $owner->id]);

    $this->actingAs($owner)
        ->post(route('admin.properties.status', $property), ['status' => 'rejected', 'reason' => 'test'])
        ->assertForbidden();

    expect($property->fresh()->status)->toBe('pending');
});

test('owner cannot change status manually via any field tampering on update', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'pending', 'user_id' => $owner->id]);

    $this->actingAs($owner)->put(route('owner.properties.update', $property), propertyPayload([
        'city_id' => $property->city_id,
        'district_id' => $property->district_id,
        'status' => 'approved',
    ]));

    expect($property->fresh()->status)->toBe('pending');
});

test('owner cannot assign another user as the property owner', function () {
    $owner = ownerTierUser();
    $otherUser = activeUser();
    $property = makeProperty(['status' => 'pending', 'user_id' => $owner->id]);

    $this->actingAs($owner)->put(route('owner.properties.update', $property), propertyPayload([
        'city_id' => $property->city_id,
        'district_id' => $property->district_id,
        'user_id' => $otherUser->id,
    ]));

    expect($property->fresh()->user_id)->toBe($owner->id);
});

test('owner can update their own pending property', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'pending', 'user_id' => $owner->id, 'title' => 'Before Update']);

    $this->actingAs($owner)
        ->put(route('owner.properties.update', $property), propertyPayload([
            'title' => 'After Update',
            'city_id' => $property->city_id,
            'district_id' => $property->district_id,
        ]))
        ->assertRedirect(route('owner.properties.show', $property));

    expect($property->fresh()->title)->toBe('After Update');
});

test('owner can update their own rejected property', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'rejected', 'user_id' => $owner->id, 'title' => 'Before Update']);

    $this->actingAs($owner)
        ->put(route('owner.properties.update', $property), propertyPayload([
            'title' => 'After Update',
            'city_id' => $property->city_id,
            'district_id' => $property->district_id,
        ]))
        ->assertRedirect(route('owner.properties.show', $property));

    expect($property->fresh()->title)->toBe('After Update');
});

test('owner cannot edit their own approved property', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'approved', 'user_id' => $owner->id]);

    $this->actingAs($owner)->get(route('owner.properties.edit', $property))->assertForbidden();
});

test('owner can delete their own pending property', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'pending', 'user_id' => $owner->id]);

    $this->actingAs($owner)
        ->delete(route('owner.properties.destroy', $property))
        ->assertRedirect(route('owner.properties.index'));

    expect(Property::find($property->id))->toBeNull();
});

test('owner can view the status history of their own property', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'pending', 'user_id' => $owner->id]);
    app(\App\Services\PropertyStatusService::class)->changeStatus($property, 'approved', 'Looks great', staffTierUser());

    $this->actingAs($owner)
        ->get(route('owner.properties.show', $property))
        ->assertOk()
        ->assertViewHas('statusHistories', fn ($histories) => $histories->total() === 1)
        ->assertSee('Looks great');
});

test('owner can upload images for their own property', function () {
    Storage::fake('public');
    $owner = ownerTierUser();
    $image = UploadedFile::fake()->image('cover.jpg', 800, 600)->size(500);

    $this->actingAs($owner)->post(route('owner.properties.store'), propertyPayload([
        'title' => 'Property With Photo',
        'images' => [$image],
    ]));

    $property = Property::firstWhere('title', 'Property With Photo');
    expect($property->getMedia('property-images'))->toHaveCount(1);
});

test('owner cannot delete images belonging to another owners property', function () {
    Storage::fake('public');
    $owner = ownerTierUser();
    $other = makeProperty(['status' => 'pending']);
    $other->addMedia(UploadedFile::fake()->image('photo.jpg'))->toMediaCollection('property-images');
    $media = $other->getMedia('property-images')->first();

    $this->actingAs($owner)
        ->delete(route('owner.properties.images.destroy', [$other, $media]))
        ->assertForbidden();

    expect($other->getMedia('property-images'))->toHaveCount(1);
});

test('owner can attach amenities to their own property', function () {
    $owner = ownerTierUser();
    $amenity = Amenity::factory()->create();

    $this->actingAs($owner)->post(route('owner.properties.store'), propertyPayload([
        'title' => 'Amenity Owned Property',
        'amenities' => [$amenity->id],
    ]));

    $property = Property::firstWhere('title', 'Amenity Owned Property');
    expect($property->amenities()->pluck('amenities.id')->all())->toBe([$amenity->id]);
});

test('inactive amenities cannot be selected by an owner', function () {
    $owner = ownerTierUser();
    $inactive = Amenity::factory()->inactive()->create();

    $this->actingAs($owner)
        ->post(route('owner.properties.store'), propertyPayload(['amenities' => [$inactive->id]]))
        ->assertSessionHasErrors('amenities.0');
});

test('a district must belong to the selected city for an owner too', function () {
    $owner = ownerTierUser();
    $cityA = City::factory()->create();
    $cityB = City::factory()->create();
    $districtOfB = District::factory()->create(['city_id' => $cityB->id]);

    $this->actingAs($owner)
        ->post(route('owner.properties.store'), propertyPayload(['city_id' => $cityA->id, 'district_id' => $districtOfB->id]))
        ->assertSessionHasErrors('district_id');
});

test('owner index search matches title and address', function () {
    $owner = ownerTierUser();
    makeProperty(['user_id' => $owner->id, 'title' => 'Unique Skyline Loft', 'address' => '1 Main St']);
    makeProperty(['user_id' => $owner->id, 'title' => 'Something Else', 'address' => '99 Rare Boulevard']);

    $this->actingAs($owner)
        ->get(route('owner.properties.index', ['search' => 'Skyline']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);

    $this->actingAs($owner)
        ->get(route('owner.properties.index', ['search' => 'Rare Boulevard']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);
});

test('owner index filters by status, purpose and availability', function () {
    $owner = ownerTierUser();
    makeProperty(['user_id' => $owner->id, 'status' => 'pending']);
    makeProperty(['user_id' => $owner->id, 'status' => 'approved', 'purpose' => 'rent', 'availability' => 'rented']);

    $this->actingAs($owner)
        ->get(route('owner.properties.index', ['status' => 'pending']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);

    $this->actingAs($owner)
        ->get(route('owner.properties.index', ['purpose' => 'rent']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);

    $this->actingAs($owner)
        ->get(route('owner.properties.index', ['availability' => 'rented']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);
});

test('owner index sorting supports price ordering', function () {
    $owner = ownerTierUser();
    $cheap = makeProperty(['user_id' => $owner->id, 'price' => 1000]);
    $expensive = makeProperty(['user_id' => $owner->id, 'price' => 9000]);

    $this->actingAs($owner)
        ->get(route('owner.properties.index', ['sort' => 'price_asc']))
        ->assertViewHas('properties', fn ($p) => $p->first()->is($cheap));

    $this->actingAs($owner)
        ->get(route('owner.properties.index', ['sort' => 'price_desc']))
        ->assertViewHas('properties', fn ($p) => $p->first()->is($expensive));
});

test('owner index pagination returns 15 properties per page', function () {
    $owner = ownerTierUser();
    collect(range(1, 20))->each(fn () => makeProperty(['user_id' => $owner->id]));

    $response = $this->actingAs($owner)->get(route('owner.properties.index', ['page' => 2]));

    $response->assertOk();
    $response->assertViewHas('properties', fn ($properties) => $properties->perPage() === 15 && $properties->currentPage() === 2);
});

test('owner can only see inquiries for their own properties', function () {
    $owner = ownerTierUser();
    $mine = makeProperty(['user_id' => $owner->id]);
    $others = makeProperty();
    $inquirer = activeUser();

    $mineInquiry = \App\Models\Inquiry::create([
        'property_id' => $mine->id,
        'user_id' => $inquirer->id,
        'phone' => '0500000001',
        'message' => 'Interested in my listing',
    ]);

    \App\Models\Inquiry::create([
        'property_id' => $others->id,
        'user_id' => $inquirer->id,
        'phone' => '0500000002',
        'message' => 'Interested in someone elses listing',
    ]);

    $this->actingAs($owner)
        ->get(route('owner.inquiries.index'))
        ->assertOk()
        ->assertViewHas('inquiries', fn ($inquiries) => $inquiries->total() === 1)
        ->assertSee('Interested in my listing')
        ->assertDontSee('Interested in someone elses listing');
});
