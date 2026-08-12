<?php

use App\Models\Amenity;
use App\Models\City;
use App\Models\District;
use App\Models\PropertyType;

test('default locale is english', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('lang="en"', false)
        ->assertSee('dir="ltr"', false);
});

test('guest can switch to arabic', function () {
    $this->get(route('language.switch', 'ar'))->assertRedirect();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false);
});

test('user can switch back to english after switching to arabic', function () {
    $this->get(route('language.switch', 'ar'));
    $this->get(route('language.switch', 'en'));

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('lang="en"', false)
        ->assertSee('dir="ltr"', false);
});

test('an invalid locale is rejected', function () {
    $this->get('/language/fr')->assertNotFound();
});

test('arabic pages render rtl and english pages render ltr', function () {
    $this->get(route('language.switch', 'ar'));
    $this->get(route('properties.index'))->assertOk()->assertSee('dir="rtl"', false);

    $this->get(route('language.switch', 'en'));
    $this->get(route('properties.index'))->assertOk()->assertSee('dir="ltr"', false);
});

test('locale persists between requests via session', function () {
    $this->get(route('language.switch', 'ar'));

    $this->get(route('home'))->assertSee('lang="ar"', false);
    $this->get(route('properties.index'))->assertSee('lang="ar"', false);
});

test('authenticated users switching locale persists it to their account', function () {
    $user = activeUser(['locale' => null]);

    $this->actingAs($user)->get(route('language.switch', 'ar'));

    expect($user->fresh()->locale)->toBe('ar');
});

test('authenticated user locale preference is restored on a fresh session', function () {
    $user = activeUser(['locale' => 'ar']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSee('lang="ar"', false);
});

test('public website renders correctly in arabic', function () {
    $this->get(route('language.switch', 'ar'));

    $this->get(route('home'))->assertOk();
    $this->get(route('properties.index'))->assertOk();
});

test('admin dashboard renders correctly in arabic', function () {
    $admin = staffTierUser();
    $this->get(route('language.switch', 'ar'));

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('dir="rtl"', false);
});

test('owner dashboard renders correctly in arabic', function () {
    $owner = ownerTierUser();
    $this->get(route('language.switch', 'ar'));

    $this->actingAs($owner)
        ->get(route('owner.dashboard'))
        ->assertOk()
        ->assertSee('dir="rtl"', false);
});

test('customer dashboard renders correctly in arabic', function () {
    $customer = activeUser();
    $this->get(route('language.switch', 'ar'));

    $this->actingAs($customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('مرحبًا');
});

test('customer dashboard renders correctly in english', function () {
    $customer = activeUser();

    $this->actingAs($customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('dir="ltr"', false)
        ->assertSee('Welcome back');
});

test('recently viewed page renders correctly in arabic', function () {
    $customer = activeUser();
    $this->get(route('language.switch', 'ar'));

    $this->actingAs($customer)
        ->get(route('customer.recently-viewed'))
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee(__('customer.no_recently_viewed', [], 'ar'));
});

test('recently viewed page renders correctly in english', function () {
    $customer = activeUser();

    $this->actingAs($customer)
        ->get(route('customer.recently-viewed'))
        ->assertOk()
        ->assertSee('dir="ltr"', false)
        ->assertSee(__('customer.no_recently_viewed', [], 'en'));
});

test('authentication pages render correctly in arabic', function () {
    $this->get(route('language.switch', 'ar'));

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('dir="rtl"', false);

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('dir="rtl"', false);
});

test('validation messages appear in arabic', function () {
    $this->get(route('language.switch', 'ar'));

    $this->post(route('login'), [])
        ->assertSessionHasErrors('email');

    expect(session('errors')->first('email'))->toBe('حقل البريد الإلكتروني مطلوب.');
});

test('validation messages appear in english by default', function () {
    $this->post(route('login'), [])
        ->assertSessionHasErrors('email');

    expect(session('errors')->first('email'))->toBe('The email field is required.');
});

test('pagination renders translated text in arabic', function () {
    $city = City::factory()->create();
    $district = District::factory()->create(['city_id' => $city->id]);

    foreach (range(1, 20) as $i) {
        makeProperty(['status' => 'approved', 'city_id' => $city->id, 'district_id' => $district->id]);
    }

    $this->get(route('language.switch', 'ar'));

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertSee('التالي');
});

test('search matches property type by english or arabic name', function () {
    $propertyType = PropertyType::factory()->create(['name_en' => 'Chalet', 'name_ar' => 'شاليه']);
    makeProperty(['property_type_id' => $propertyType->id, 'status' => 'approved', 'title' => 'Searchable By Name']);

    $this->get(route('properties.index', ['search' => 'Chalet']))
        ->assertOk()
        ->assertSee('Searchable By Name');

    $this->get(route('properties.index', ['search' => 'شاليه']))
        ->assertOk()
        ->assertSee('Searchable By Name');
});

test('property types expose bilingual names via the locale-aware accessor', function () {
    $propertyType = PropertyType::factory()->create(['name_en' => 'Duplex', 'name_ar' => 'دوبلكس']);

    app()->setLocale('en');
    expect($propertyType->fresh()->name)->toBe('Duplex');

    app()->setLocale('ar');
    expect($propertyType->fresh()->name)->toBe('دوبلكس');
});

test('cities expose bilingual names via the locale-aware accessor', function () {
    $city = City::factory()->create(['name_en' => 'Abha', 'name_ar' => 'أبها']);

    app()->setLocale('en');
    expect($city->fresh()->name)->toBe('Abha');

    app()->setLocale('ar');
    expect($city->fresh()->name)->toBe('أبها');
});

test('districts expose bilingual names via the locale-aware accessor', function () {
    $city = City::factory()->create();
    $district = District::factory()->create(['city_id' => $city->id, 'name_en' => 'Al Nakheel', 'name_ar' => 'النخيل']);

    app()->setLocale('en');
    expect($district->fresh()->name)->toBe('Al Nakheel');

    app()->setLocale('ar');
    expect($district->fresh()->name)->toBe('النخيل');
});

test('amenities expose bilingual names via the locale-aware accessor', function () {
    $amenity = Amenity::factory()->create(['name_en' => 'Gym', 'name_ar' => 'صالة رياضية']);

    app()->setLocale('en');
    expect($amenity->fresh()->name)->toBe('Gym');

    app()->setLocale('ar');
    expect($amenity->fresh()->name)->toBe('صالة رياضية');
});

test('property content supports bilingual title, description and address', function () {
    $property = makeProperty([
        'title_en' => 'Modern Villa',
        'title_ar' => 'فيلا حديثة',
        'description_en' => 'A modern villa.',
        'description_ar' => 'فيلا حديثة وعصرية.',
        'address_en' => '123 Main St',
        'address_ar' => '١٢٣ الشارع الرئيسي',
    ]);

    app()->setLocale('en');
    expect($property->fresh()->title)->toBe('Modern Villa');
    expect($property->fresh()->description)->toBe('A modern villa.');
    expect($property->fresh()->address)->toBe('123 Main St');

    app()->setLocale('ar');
    expect($property->fresh()->title)->toBe('فيلا حديثة');
    expect($property->fresh()->description)->toBe('فيلا حديثة وعصرية.');
    expect($property->fresh()->address)->toBe('١٢٣ الشارع الرئيسي');
});

test('property falls back to the english value when arabic is not set', function () {
    $property = makeProperty(['title_en' => 'Fallback Villa', 'title_ar' => null]);

    app()->setLocale('ar');
    expect($property->fresh()->title)->toBe('Fallback Villa');
});
