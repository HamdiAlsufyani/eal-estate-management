<?php

namespace App\Http\Requests\Admin\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangePropertyAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('changeAvailability', $this->route('property'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'availability' => ['required', Rule::in(['available', 'reserved', 'sold', 'rented'])],
        ];
    }
}
