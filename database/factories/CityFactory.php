<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'name' => $name,
            'name_en' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
        ];
    }

    public function create($attributes = [], ?Model $parent = null)
    {
        if (isset($attributes['name']) && ! isset($attributes['name_en'])) {
            $attributes['name_en'] = $attributes['name'];
        }

        return parent::create($attributes, $parent);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
