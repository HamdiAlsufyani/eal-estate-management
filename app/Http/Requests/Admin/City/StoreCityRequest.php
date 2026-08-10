<?php

namespace App\Http\Requests\Admin\City;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', City::class);
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
            'name' => ['required', 'string', 'max:255', 'unique:cities,name'],
            'slug' => ['required', 'string', 'max:255', 'unique:cities,slug'],
            'is_active' => ['boolean'],
        ];
    }
}
