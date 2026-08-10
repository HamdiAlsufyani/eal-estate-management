<?php

namespace App\Http\Requests\Admin\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('city'));
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
        $city = $this->route('city');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('cities', 'name')->ignore($city->id)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('cities', 'slug')->ignore($city->id)],
            'is_active' => ['boolean'],
        ];
    }
}
