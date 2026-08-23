<?php

namespace Modules\Public\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:120',
            'description' => 'nullable|string|max:500',
            'price' => 'required|string|max:50',
            'period' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'features.*' => 'string|max:200',
            'highlight' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
}
