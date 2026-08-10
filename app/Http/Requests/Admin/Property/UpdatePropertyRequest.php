<?php

namespace App\Http\Requests\Admin\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('property'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $property = $this->route('property');

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],

            'property_type_id' => [
                'required',
                Rule::exists('property_types', 'id')->where(
                    fn ($query) => $query->where('is_active', true)->orWhere('id', $property->property_type_id)
                ),
            ],
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')->where(
                    fn ($query) => $query->where('is_active', true)->orWhere('id', $property->city_id)
                ),
            ],
            'district_id' => [
                'required',
                Rule::exists('districts', 'id')->where(fn ($query) => $query->where('city_id', $this->input('city_id'))),
            ],

            'purpose' => ['required', Rule::in(['sale', 'rent'])],
            'rent_period' => [
                'required_if:purpose,rent',
                'nullable',
                Rule::in(['monthly', 'yearly']),
                Rule::prohibitedIf(fn () => $this->input('purpose') === 'sale'),
            ],
            'price' => ['required', 'numeric', 'min:0'],

            'area' => ['required', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'living_rooms' => ['nullable', 'integer', 'min:0'],
            'kitchens' => ['nullable', 'integer', 'min:0'],
            'floor' => ['nullable', 'integer', 'min:0'],
            'parking_spaces' => ['nullable', 'integer', 'min:0'],
            'furnished' => ['nullable', 'boolean'],

            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'amenities' => ['nullable', 'array'],
            'amenities.*' => [Rule::exists('amenities', 'id')->where('is_active', true)],

            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'featured' => ['nullable', 'boolean'],
            'availability' => ['nullable', Rule::in(['available', 'reserved', 'sold', 'rented'])],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
