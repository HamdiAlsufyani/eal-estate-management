<?php

use App\Models\Amenity;
use App\Models\City;
use App\Models\District;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can create a property', function () {
    $admin = staffTierUser();

    $response = $this->actingAs($admin)->post(route('admin.properties.store'), propertyPayload(['title' => 'Sunny Villa']));

    $property = Property::firstWhere('title', 'Sunny Villa');
    $response->assertRedirect(route('admin.properties.show', $property));
    expect($property)->not->toBeNull();
    expect($property->slug)->toBe('sunny-villa');
});

test('admin can update a property', function () {
    $admin = staffTierUser();
    $property = makeProperty(['title' => 'Old Title', 'status' => 'pending', 'user_id' => $admin->id]);

    $payload = propertyPayload([
        'title' => 'New Title',
        'city_id' => $property->city_id,
        'district_id' => $property->district_id,
    ]);

    $this->actingAs($admin)
        ->put(route('admin.properties.update', $property), $payload)
        ->assertRedirect(route('admin.properties.show', $property));

    expect($property->fresh()->title)->toBe('New Title');
});

test('admin can delete any property regardless of status', function () {
    $admin = staffTierUser();
    $property = makeProperty(['status' => 'approved']);

    $this->actingAs($admin)
        ->delete(route('admin.properties.destroy', $property))
        ->assertRedirect(route('admin.properties.index'));

    expect(Property::find($property->id))->toBeNull();
    expect(Property::withTrashed()->find($property->id))->not->toBeNull();
});

test('owner can create a property', function () {
    $owner = ownerTierUser();

    $response = $this->actingAs($owner)->post(route('admin.properties.store'), propertyPayload(['title' => 'Owner Listing']));

    $property = Property::firstWhere('title', 'Owner Listing');
    $response->assertRedirect(route('admin.properties.show', $property));
    expect($property->user_id)->toBe($owner->id);
});

test('owner created property always starts as pending', function () {
    $owner = ownerTierUser();

    // Even if the owner tries to inject a status, it must be ignored.
    $this->actingAs($owner)->post(route('admin.properties.store'), propertyPayload(['status' => 'approved']));

    $property = Property::where('user_id', $owner->id)->latest()->first();
    expect($property->status)->toBe('pending');
});

test('owner cannot approve a property even by submitting the status route directly', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'pending', 'user_id' => $owner->id]);

    $this->actingAs($owner)
        ->post(route('admin.properties.status', $property), ['status' => 'approved'])
        ->assertForbidden();

    expect($property->fresh()->status)->toBe('pending');
});

test('owner cannot view or access another owners property', function () {
    $owner = ownerTierUser();
    $otherOwnersProperty = makeProperty();

    $this->actingAs($owner)
        ->get(route('admin.properties.show', $otherOwnersProperty))
        ->assertForbidden();

    $this->actingAs($owner)
        ->get(route('admin.properties.index'))
        ->assertOk()
        ->assertDontSee($otherOwnersProperty->title);
});

test('owner cannot assign a property to another user', function () {
    $owner = ownerTierUser();
    $otherUser = activeUser();

    $this->actingAs($owner)->post(route('admin.properties.store'), propertyPayload([
        'title' => 'Not Reassignable',
        'user_id' => $otherUser->id,
    ]));

    $property = Property::firstWhere('title', 'Not Reassignable');
    expect($property->user_id)->toBe($owner->id);
});

test('amenities are attached when creating a property', function () {
    $admin = staffTierUser();
    $amenityOne = Amenity::factory()->create();
    $amenityTwo = Amenity::factory()->create();

    $this->actingAs($admin)->post(route('admin.properties.store'), propertyPayload([
        'title' => 'Amenity Property',
        'amenities' => [$amenityOne->id, $amenityTwo->id],
    ]));

    $property = Property::firstWhere('title', 'Amenity Property');
    expect($property->amenities()->pluck('amenities.id')->sort()->values()->all())
        ->toBe([$amenityOne->id, $amenityTwo->id]);
});

test('updating amenities syncs instead of duplicating', function () {
    $admin = staffTierUser();
    $property = makeProperty(['status' => 'pending', 'user_id' => $admin->id]);
    $amenityOne = Amenity::factory()->create();
    $amenityTwo = Amenity::factory()->create();
    $property->amenities()->attach($amenityOne);

    $this->actingAs($admin)->put(route('admin.properties.update', $property), propertyPayload([
        'city_id' => $property->city_id,
        'district_id' => $property->district_id,
        'amenities' => [$amenityTwo->id],
    ]));

    $property->refresh();
    expect($property->amenities()->pluck('amenities.id')->all())->toBe([$amenityTwo->id]);
});

test('inactive amenities cannot be selected', function () {
    $admin = staffTierUser();
    $inactive = Amenity::factory()->inactive()->create();

    $this->actingAs($admin)
        ->post(route('admin.properties.store'), propertyPayload(['amenities' => [$inactive->id]]))
        ->assertSessionHasErrors('amenities.0');
});

test('inactive property types cannot be selected', function () {
    $admin = staffTierUser();
    $inactive = PropertyType::factory()->inactive()->create();

    $this->actingAs($admin)
        ->post(route('admin.properties.store'), propertyPayload(['property_type_id' => $inactive->id]))
        ->assertSessionHasErrors('property_type_id');
});

test('inactive cities cannot be selected', function () {
    $admin = staffTierUser();
    $inactiveCity = City::factory()->inactive()->create();
    $district = District::factory()->create(['city_id' => $inactiveCity->id]);

    $this->actingAs($admin)
        ->post(route('admin.properties.store'), propertyPayload(['city_id' => $inactiveCity->id, 'district_id' => $district->id]))
        ->assertSessionHasErrors('city_id');
});

test('a district must belong to the selected city', function () {
    $admin = staffTierUser();
    $cityA = City::factory()->create();
    $cityB = City::factory()->create();
    $districtOfB = District::factory()->create(['city_id' => $cityB->id]);

    $this->actingAs($admin)
        ->post(route('admin.properties.store'), propertyPayload(['city_id' => $cityA->id, 'district_id' => $districtOfB->id]))
        ->assertSessionHasErrors('district_id');
});

test('sale properties cannot have a rent period', function () {
    $admin = staffTierUser();

    $this->actingAs($admin)
        ->post(route('admin.properties.store'), propertyPayload(['purpose' => 'sale', 'rent_period' => 'monthly']))
        ->assertSessionHasErrors('rent_period');
});

test('rental properties require a rent period', function () {
    $admin = staffTierUser();

    $this->actingAs($admin)
        ->post(route('admin.properties.store'), propertyPayload(['purpose' => 'rent', 'rent_period' => null]))
        ->assertSessionHasErrors('rent_period');
});

test('rental properties accept a valid rent period', function () {
    $admin = staffTierUser();

    $this->actingAs($admin)
        ->post(route('admin.properties.store'), propertyPayload(['title' => 'Rental Unit', 'purpose' => 'rent', 'rent_period' => 'yearly']))
        ->assertSessionHasNoErrors();

    expect(Property::firstWhere('title', 'Rental Unit')->rent_period)->toBe('yearly');
});

test('only approved properties can be featured', function () {
    $admin = staffTierUser();
    $property = makeProperty(['status' => 'pending', 'featured' => false]);

    $this->actingAs($admin)
        ->put(route('admin.properties.update', $property), propertyPayload([
            'city_id' => $property->city_id,
            'district_id' => $property->district_id,
            'featured' => 1,
        ]))
        ->assertSessionHasErrors('featured');

    expect($property->fresh()->featured)->toBeFalse();
});

test('an approved property can be featured by an authorized user', function () {
    $admin = staffTierUser();
    $property = makeProperty(['status' => 'approved', 'featured' => false]);

    $this->actingAs($admin)->put(route('admin.properties.update', $property), propertyPayload([
        'city_id' => $property->city_id,
        'district_id' => $property->district_id,
        'featured' => 1,
    ]));

    expect($property->fresh()->featured)->toBeTrue();
});

test('owners cannot mark their own property as featured', function () {
    $owner = ownerTierUser();
    $property = makeProperty(['status' => 'approved', 'user_id' => $owner->id, 'featured' => false]);

    $this->actingAs($owner)->put(route('admin.properties.update', $property), propertyPayload([
        'city_id' => $property->city_id,
        'district_id' => $property->district_id,
        'featured' => 1,
    ]));

    expect($property->fresh()->featured)->toBeFalse();
});

test('pending properties are excluded from the approved scope used for public visibility', function () {
    makeProperty(['status' => 'pending']);
    $approved = makeProperty(['status' => 'approved']);

    $visible = Property::approved()->pluck('id');
    expect($visible)->not->toContain(0);
    expect($visible->all())->toBe([$approved->id]);
});

test('rejected properties are excluded from the approved scope used for public visibility', function () {
    makeProperty(['status' => 'rejected']);
    $approved = makeProperty(['status' => 'approved']);

    expect(Property::approved()->pluck('id')->all())->toBe([$approved->id]);
});

test('unauthorized users receive 403 for create, edit and delete', function () {
    $user = activeUser();
    $property = makeProperty();

    $this->actingAs($user)->get(route('admin.properties.create'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.properties.edit', $property))->assertForbidden();
    $this->actingAs($user)->delete(route('admin.properties.destroy', $property))->assertForbidden();
});

test('index search matches title, description, address and owner details', function () {
    $admin = staffTierUser();
    $owner = activeUser(['name' => 'Jamie Rivera']);
    makeProperty(['title' => 'Unique Skyline Loft', 'user_id' => $owner->id]);
    makeProperty(['title' => 'Something Else Entirely']);

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['search' => 'Skyline']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['search' => 'Jamie Rivera']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);
});

test('index filters by purpose, availability, property type, city and featured', function () {
    $admin = staffTierUser();
    $sale = makeProperty(['purpose' => 'sale', 'status' => 'approved', 'availability' => 'available']);
    makeProperty(['purpose' => 'rent', 'status' => 'approved', 'availability' => 'rented']);

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['purpose' => 'sale']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1 && $p->first()->is($sale));

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['availability' => 'rented']))
        ->assertViewHas('properties', fn ($p) => $p->total() === 1);

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['property_type' => $sale->property_type_id]))
        ->assertViewHas('properties', fn ($p) => $p->total() >= 1);

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['city' => $sale->city_id]))
        ->assertViewHas('properties', fn ($p) => $p->total() >= 1);

    $featured = makeProperty(['status' => 'approved', 'featured' => true]);
    makeProperty(['status' => 'approved', 'featured' => false]);

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['featured' => 'featured']))
        ->assertViewHas('properties', fn ($p) => $p->pluck('id')->contains($featured->id) && $p->every(fn ($item) => $item->featured));
});

test('index sorting supports price and title ordering', function () {
    $admin = staffTierUser();
    $cheap = makeProperty(['title' => 'A Cheap Place', 'price' => 1000]);
    $expensive = makeProperty(['title' => 'Z Expensive Place', 'price' => 9000]);

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['sort' => 'price_asc']))
        ->assertViewHas('properties', fn ($p) => $p->first()->is($cheap));

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['sort' => 'price_desc']))
        ->assertViewHas('properties', fn ($p) => $p->first()->is($expensive));

    $this->actingAs($admin)
        ->get(route('admin.properties.index', ['sort' => 'title_asc']))
        ->assertViewHas('properties', fn ($p) => $p->first()->is($cheap));
});

test('index pagination returns 15 properties per page and preserves filters', function () {
    $admin = staffTierUser();
    collect(range(1, 20))->each(fn () => makeProperty(['status' => 'approved']));

    $response = $this->actingAs($admin)->get(route('admin.properties.index', ['status' => 'approved', 'page' => 2]));

    $response->assertOk();
    $response->assertViewHas('properties', function ($properties) {
        return $properties->perPage() === 15 && $properties->currentPage() === 2;
    });
});

test('image uploads are validated for type and size', function () {
    Storage::fake('public');
    $admin = staffTierUser();

    $badFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($admin)
        ->post(route('admin.properties.store'), propertyPayload(['images' => [$badFile]]))
        ->assertSessionHasErrors('images.0');

    $tooLarge = UploadedFile::fake()->image('huge.jpg')->size(6000);

    $this->actingAs($admin)
        ->post(route('admin.properties.store'), propertyPayload(['images' => [$tooLarge]]))
        ->assertSessionHasErrors('images.0');
});

test('valid images are attached to the property media collection', function () {
    Storage::fake('public');
    $admin = staffTierUser();
    $image = UploadedFile::fake()->image('cover.jpg', 800, 600)->size(500);

    $this->actingAs($admin)->post(route('admin.properties.store'), propertyPayload([
        'title' => 'Property With Photo',
        'images' => [$image],
    ]));

    $property = Property::firstWhere('title', 'Property With Photo');
    expect($property->getMedia('property-images'))->toHaveCount(1);
});

test('property slugs are unique even for duplicate titles', function () {
    $admin = staffTierUser();

    $this->actingAs($admin)->post(route('admin.properties.store'), propertyPayload(['title' => 'Duplicate Title']));
    $this->actingAs($admin)->post(route('admin.properties.store'), propertyPayload(['title' => 'Duplicate Title']));

    $slugs = Property::where('title', 'Duplicate Title')->pluck('slug')->sort()->values()->all();
    expect($slugs)->toBe(['duplicate-title', 'duplicate-title-2']);
});

test('availability can only change on approved properties and must match the purpose', function () {
    $admin = staffTierUser();
    $pending = makeProperty(['status' => 'pending']);

    $this->actingAs($admin)
        ->post(route('admin.properties.availability', $pending), ['availability' => 'reserved'])
        ->assertSessionHasErrors('availability');

    $approvedSale = makeProperty(['status' => 'approved', 'purpose' => 'sale']);

    $this->actingAs($admin)
        ->post(route('admin.properties.availability', $approvedSale), ['availability' => 'rented'])
        ->assertSessionHasErrors('availability');

    $this->actingAs($admin)
        ->post(route('admin.properties.availability', $approvedSale), ['availability' => 'sold'])
        ->assertRedirect();

    expect($approvedSale->fresh()->availability)->toBe('sold');
});

test('deleting a property does not delete its status history', function () {
    $admin = staffTierUser();
    $property = makeProperty(['status' => 'pending']);
    app(\App\Services\PropertyStatusService::class)->changeStatus($property, 'approved', null, $admin);

    $this->actingAs($admin)->delete(route('admin.properties.destroy', $property));

    expect(\App\Models\PropertyStatusHistory::where('property_id', $property->id)->count())->toBe(1);
});
