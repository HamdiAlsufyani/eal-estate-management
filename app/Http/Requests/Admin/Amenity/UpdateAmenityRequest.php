<?php

namespace App\Http\Requests\Admin\Amenity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateAmenityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('amenity'));
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('name_en')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $amenity = $this->route('amenity');

        return [
            'name_en' => ['required', 'string', 'max:255', Rule::unique('amenities', 'name_en')->ignore($amenity->id)],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('amenities', 'slug')->ignore($amenity->id)],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
