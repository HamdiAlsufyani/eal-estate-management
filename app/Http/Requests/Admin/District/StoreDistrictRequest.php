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
            'slug' => Str::slug($this->input('slug') ?: $this->input('name')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('districts', 'name')->where(fn ($query) => $query->where('city_id', $this->input('city_id'))),
            ],
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
            'name.unique' => 'A district with this name already exists in the selected city.',
            'slug.unique' => 'A district with this slug already exists in the selected city.',
        ];
    }
}
