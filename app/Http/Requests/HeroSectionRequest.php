<?php

namespace Modules\Public\app\Http\Requests;

use App\Http\Requests\BaseRequest;

class HeroSectionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'subtitle' => ['nullable', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'button_primary_text' => ['nullable', 'string', 'max:100'],
            'button_primary_link' => ['nullable', 'string', 'max:255'],
            'button_secondary_text' => ['nullable', 'string', 'max:100'],
            'button_secondary_link' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
