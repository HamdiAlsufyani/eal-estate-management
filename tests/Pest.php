<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function activeUser(array $overrides = []): \App\Models\User
{
    return \App\Models\User::factory()->create(array_merge([
        'status' => 'active',
        'phone' => fake()->unique()->numerify('05########'),
    ], $overrides));
}

function adminUser(array $permissions = []): \App\Models\User
{
    test()->seed(\Database\Seeders\RolePermissionSeeder::class);

    $user = activeUser();

    if ($permissions) {
        $user->givePermissionTo($permissions);
    }

    return $user;
}

/**
 * Legacy override keys ('title', 'description', 'address') are mapped onto
 * their '_en' bilingual counterparts so existing test call sites keep working.
 *
 * @return array<string, mixed>
 */
function withBilingualOverrides(array $overrides, array $fields): array
{
    foreach ($fields as $field) {
        if (array_key_exists($field, $overrides) && ! array_key_exists("{$field}_en", $overrides)) {
            $overrides["{$field}_en"] = $overrides[$field];
        }
    }

    return $overrides;
}

function makeProperty(array $overrides = []): \App\Models\Property
{
    $overrides = withBilingualOverrides($overrides, ['title', 'description', 'address']);

    $city = $overrides['city_id'] ?? null;
    $district = $overrides['district_id'] ?? null;

    if (! $city) {
        $city = \App\Models\City::factory()->create()->id;
    }

    if (! $district) {
        $district = \App\Models\District::factory()->create(['city_id' => $city])->id;
    }

    $title = 'Test Property '.uniqid();
    $description = 'A lovely test property.';
    $address = '123 Test Street';

    return \App\Models\Property::create(array_merge([
        'user_id' => activeUser()->id,
        'property_type_id' => \App\Models\PropertyType::factory()->create()->id,
        'city_id' => $city,
        'district_id' => $district,
        'title' => $title,
        'title_en' => $title,
        'slug' => 'test-property-'.uniqid(),
        'description' => $description,
        'description_en' => $description,
        'purpose' => 'sale',
        'price' => 500000,
        'area' => 120,
        'address' => $address,
        'address_en' => $address,
        'status' => 'approved',
    ], $overrides));
}

function propertyPayload(array $overrides = []): array
{
    $overrides = withBilingualOverrides($overrides, ['title', 'description', 'address']);

    $city = $overrides['city_id'] ?? \App\Models\City::factory()->create()->id;
    $district = $overrides['district_id'] ?? \App\Models\District::factory()->create(['city_id' => $city])->id;

    return array_merge([
        'title_en' => 'Test Listing '.uniqid(),
        'description_en' => 'A lovely place to live.',
        'property_type_id' => \App\Models\PropertyType::factory()->create()->id,
        'city_id' => $city,
        'district_id' => $district,
        'purpose' => 'sale',
        'price' => 350000,
        'area' => 150,
        'address_en' => '456 Sample Ave',
        'bedrooms' => 3,
        'bathrooms' => 2,
    ], $overrides);
}

function ownerTierUser(): \App\Models\User
{
    return adminUser(['properties.view', 'properties.create', 'properties.edit', 'properties.delete']);
}

function staffTierUser(): \App\Models\User
{
    return adminUser([
        'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
        'properties.approve', 'properties.assign_owner', 'properties.feature', 'properties.change_availability',
    ]);
}
