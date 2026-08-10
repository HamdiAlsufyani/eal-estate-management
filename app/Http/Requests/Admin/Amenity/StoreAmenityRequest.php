<?php

namespace App\Http\Requests\Admin\Amenity;

use App\Models\Amenity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreAmenityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Amenity::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('name')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:amenities,name'],
            'slug' => ['required', 'string', 'max:255', 'unique:amenities,slug'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
