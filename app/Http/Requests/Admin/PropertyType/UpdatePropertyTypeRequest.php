<?php

namespace App\Http\Requests\Admin\PropertyType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdatePropertyTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('property_type'));
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
        $propertyType = $this->route('property_type');

        return [
            'name_en' => ['required', 'string', 'max:255', Rule::unique('property_types', 'name_en')->ignore($propertyType->id)],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('property_types', 'slug')->ignore($propertyType->id)],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
