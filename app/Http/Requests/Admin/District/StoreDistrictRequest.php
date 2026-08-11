<?php

namespace App\Http\Requests\Admin\District;

use App\Models\District;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', District::class);
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
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'name_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('districts', 'name_en')->where(fn ($query) => $query->where('city_id', $this->input('city_id'))),
            ],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('districts', 'slug')->where(fn ($query) => $query->where('city_id', $this->input('city_id'))),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name_en.unique' => 'A district with this name already exists in the selected city.',
            'slug.unique' => 'A district with this slug already exists in the selected city.',
        ];
    }
}
