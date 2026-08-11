<?php

namespace App\Http\Requests\Admin\PropertyType;

use App\Models\PropertyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePropertyTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', PropertyType::class);
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
        return [
            'name_en' => ['required', 'string', 'max:255', 'unique:property_types,name_en'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:property_types,slug'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
