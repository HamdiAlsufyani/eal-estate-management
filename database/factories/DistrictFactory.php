<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->streetName();

        return [
            'city_id' => City::factory(),
            'name' => $name,
            'name_en' => $name,
            'slug' => Str::slug($name),
        ];
    }

    public function create($attributes = [], ?Model $parent = null)
    {
        if (isset($attributes['name']) && ! isset($attributes['name_en'])) {
            $attributes['name_en'] = $attributes['name'];
        }

        return parent::create($attributes, $parent);
    }
}
