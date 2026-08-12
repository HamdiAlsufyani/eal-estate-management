<?php

namespace App\Http\Requests\Analytics;

use App\Support\Analytics\DateRange;
use Illuminate\Foundation\Http\FormRequest;

class AnalyticsFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'range' => ['nullable', 'string', 'in:'.implode(',', DateRange::PRESETS)],
            'from' => ['nullable', 'required_if:range,custom', 'date'],
            'to' => ['nullable', 'required_if:range,custom', 'date', 'after_or_equal:from'],
            'group_by' => ['nullable', 'string', 'in:day,week,month'],
        ];
    }
}
