<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;

class TestimonialRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'position' => ['nullable', 'string', 'max:191'],
            'organization' => ['nullable', 'string', 'max:191'],
            'quote' => ['required', 'string', 'max:1500'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
